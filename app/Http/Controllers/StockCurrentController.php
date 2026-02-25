<?php

namespace App\Http\Controllers;

use App\Models\ItemVariant;
use App\Models\StockCurrent;
use App\Models\Transaction;
use App\Models\ItemMaster;
use App\Models\ItemBatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class StockCurrentController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:Super Admin|Admin Gudang']);
    }

    public function index(Request $request)
    {
        $s    = trim($request->get('q', ''));
        $fefo = $request->get('fefo');

        $q = ItemVariant::with(['itemMaster', 'stock'])
            ->join('items_master', 'items_master.id', '=', 'item_variants.item_master_id')
            ->select('item_variants.*');

        if ($s !== '') {
            $q->where(function ($qq) use ($s) {
                $qq->where('item_variants.brand', 'like', "%{$s}%")
                   ->orWhere('item_variants.lot_number', 'like', "%{$s}%")
                   ->orWhere('items_master.item_code', 'like', "%{$s}%")
                   ->orWhere('items_master.item_name', 'like', "%{$s}%");
            });
        }

        $fefo = $fefo ? strtolower($fefo) : null;

        if ($fefo) {
            $today = Carbon::today();
            $q->whereNotNull('item_variants.expiration_date');

            if ($fefo === 'expired') {
                $q->whereDate('item_variants.expiration_date', '<', $today);
            } elseif ($fefo === 'merah') {
                $upper = $today->copy()->addMonths(3);
                $q->whereDate('item_variants.expiration_date', '>=', $today)
                  ->whereDate('item_variants.expiration_date', '<', $upper);
            } elseif ($fefo === 'kuning') {
                $from = $today->copy()->addMonths(3);
                $to   = $today->copy()->addMonths(12);
                $q->whereDate('item_variants.expiration_date', '>=', $from)
                  ->whereDate('item_variants.expiration_date', '<=', $to);
            } elseif ($fefo === 'hijau') {
                $from = $today->copy()->addMonths(12);
                $q->whereDate('item_variants.expiration_date', '>', $from);
            }
        }

        $variants = $q->orderBy('items_master.item_code')
                      ->orderBy('item_variants.brand')
                      ->orderBy('item_variants.lot_number')
                      ->orderBy('item_variants.expiration_date')
                      ->paginate(12)
                      ->withQueryString();

        return view('stock.index', compact('variants', 's', 'fefo'));
    }

    public function show(ItemVariant $variant)
    {
        $variant->load(['itemMaster', 'stock']);

        $stock = $variant->stock ?? new StockCurrent([
            'item_variant_id'  => $variant->id,
            'current_quantity' => 0,
        ]);

        $transactions = Transaction::where('item_variant_id', $variant->id)
            ->orderBy('trans_date')
            ->orderBy('id')
            ->get();

        $runningBalance = 0;
        foreach ($transactions as $t) {
            if ($t->type === 'MASUK') {
                $t->in_qty  = $t->quantity;
                $t->out_qty = 0;
                $runningBalance += $t->quantity;
            } else {
                $t->in_qty  = 0;
                $t->out_qty = $t->quantity;
                $runningBalance -= $t->quantity;
            }
            $t->balance = $runningBalance;
        }

        $batches = ItemBatch::where('item_variant_id', $variant->id)
            ->orderBy('expiration_date')
            ->orderBy('lot_number')
            ->get();

        return view('stock.show', compact('variant', 'stock', 'transactions', 'batches'));
    }

    public function cardByItem(ItemMaster $itemMaster)
    {
        $variants = ItemVariant::with('stock')
            ->where('item_master_id', $itemMaster->id)
            ->get();

        $currentStock = $variants->sum(function ($v) {
            return (int) (optional($v->stock)->current_quantity ?? 0);
        });

        $variantIds = $variants->pluck('id')->all();

        $transactions = Transaction::with('variant')
            ->whereIn('item_variant_id', $variantIds)
            ->orderBy('trans_date')
            ->orderBy('id')
            ->get();

        $runningBalance = 0;

        foreach ($transactions as $t) {
            $brand = $t->brand ?? $t->variant?->brand;

            if ($t->type === 'MASUK') {
                $t->in_qty  = $t->quantity;
                $t->out_qty = 0;
                $runningBalance += $t->quantity;
            } else {
                $t->in_qty  = 0;
                $t->out_qty = $t->quantity;
                $runningBalance -= $t->quantity;
            }

            $t->brand_display = $brand;
            $t->balance       = $runningBalance;
        }

        return view('stock.card_item_master', [
            'item'         => $itemMaster,
            'currentStock' => $currentStock,
            'transactions' => $transactions,
        ]);
    }

    public function seed(Request $request, ItemVariant $variant)
    {
        $data = $request->validate([
            'current_quantity' => 'required|integer|min:0',
        ]);

        StockCurrent::updateOrCreate(
            ['item_variant_id' => $variant->id],
            ['current_quantity' => (int) $data['current_quantity']]
        );

        return back()->with('success', 'Stok saat ini berhasil di-set.');
    }

    public function recompute(ItemVariant $variant)
    {
        $variant->loadMissing('stock');

        $masuk  = (int) Transaction::where('item_variant_id', $variant->id)
                    ->where('type', 'MASUK')
                    ->sum('quantity');

        $keluar = (int) Transaction::where('item_variant_id', $variant->id)
                    ->where('type', 'KELUAR')
                    ->sum('quantity');

        $newQty = max(0, $masuk - $keluar);

        DB::transaction(function () use ($variant, $newQty) {
            StockCurrent::updateOrCreate(
                ['item_variant_id' => $variant->id],
                ['current_quantity' => $newQty]
            );
        });

        return back()->with(
            'success',
            "Recompute berhasil. MASUK: {$masuk}, KELUAR: {$keluar}, Stok Saat Ini: {$newQty}."
        );
    }
}
