<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ItemVariant;
use App\Models\ItemMaster;
use App\Models\Transaction;
use App\Models\StockCurrent; 
use App\Http\Requests\StoreItemVariantRequest;
use App\Http\Requests\UpdateItemVariantRequest;

class ItemVariantController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','role:Super Admin|Admin Gudang']);
    }

    public function index(Request $request)
    {
        $s      = trim($request->get('q', ''));
        $itemId = $request->get('item_master_id');

        $q = ItemVariant::query()
            ->with(['itemMaster','stock'])
            ->withCount('batches') 
            ->join('items_master', 'items_master.id', '=', 'item_variants.item_master_id')
            ->select('item_variants.*');

        if ($itemId) {
            $q->where('item_variants.item_master_id', $itemId);
        }

        if ($s !== '') {
            $q->where(function ($qq) use ($s) {
                $qq->where('item_variants.brand', 'like', "%{$s}%")
                   ->orWhere('item_variants.lot_number', 'like', "%{$s}%")
                   ->orWhere('items_master.item_code', 'like', "%{$s}%")
                   ->orWhere('items_master.item_name', 'like', "%{$s}%");
            });
        }

        $variants = $q->orderBy('items_master.item_code')
                      ->orderBy('item_variants.brand')
                      ->orderBy('item_variants.lot_number')
                      ->orderBy('item_variants.expiration_date')
                      ->paginate(12)
                      ->withQueryString();

        $items = ItemMaster::query()
            ->orderBy('item_code')
            ->selectRaw("id, CONCAT(item_code, ' — ', item_name) AS label")
            ->pluck('label', 'id');

        return view('variants.index', compact('variants','items','s','itemId'));
    }

    public function create()
    {
        $items = ItemMaster::query()
            ->orderBy('item_code')
            ->selectRaw("id, CONCAT(item_code, ' — ', item_name) AS label")
            ->pluck('label', 'id');

        return view('variants.create', compact('items'));
    }

    public function store(StoreItemVariantRequest $request)
    {
        $row = ItemVariant::create($request->validated());
        $row->stock()->firstOrCreate(
            ['item_variant_id' => $row->id],
            ['current_quantity' => 0]
        );

        return redirect()
            ->route('variants.index')
            ->with('success', "Variant <strong>{$row->brand}</strong> berhasil dibuat.");
    }

    public function edit(ItemVariant $itemVariant)
    {
        $items = ItemMaster::query()
            ->orderBy('item_code')
            ->selectRaw("id, CONCAT(item_code, ' — ', item_name) AS label")
            ->pluck('label', 'id');

        return view('variants.edit', [
            'variant' => $itemVariant->load('itemMaster','stock'),
            'items'   => $items,
        ]);
    }

    public function update(UpdateItemVariantRequest $request, ItemVariant $itemVariant)
    {
        $itemVariant->update($request->validated());
        $itemVariant->stock()->firstOrCreate(
            ['item_variant_id' => $itemVariant->id],
            ['current_quantity' => 0]
        );

        return redirect()
            ->route('variants.index')
            ->with('success', "Variant <strong>{$itemVariant->brand}</strong> diperbarui.");
    }

    public function destroy(ItemVariant $itemVariant)
    {
        $hasTx = Transaction::where('item_variant_id', $itemVariant->id)->exists();
        if ($hasTx) {
            return redirect()
                ->route('variants.index')
                ->with('error', 'Variant tidak dapat dihapus karena sudah dipakai di transaksi.');
        }

        $name = $itemVariant->brand.' / '.($itemVariant->lot_number ?: '-');
        $itemVariant->delete();

        return redirect()
            ->route('variants.index')
            ->with('success', "Variant <strong>{$name}</strong> dihapus.");
    }

    public function show(ItemVariant $itemVariant, Request $request)
    {
        $type = $request->get('type');

        $item = $itemVariant->itemMaster()
            ->with(['variants.stock'])
            ->firstOrFail();

        $variantIds = $item->variants->pluck('id');

        $transactions = Transaction::query()
            ->with('variant') 
            ->whereIn('item_variant_id', $variantIds)
            ->when(in_array($type, ['MASUK','KELUAR'], true), function ($q) use ($type) {
                $q->where('type', $type);
            })
            ->orderBy('trans_date')
            ->orderBy('id')
            ->get();

        $running = 0;
        foreach ($transactions as $t) {
            $in  = $t->type === 'MASUK'  ? $t->quantity : 0;
            $out = $t->type === 'KELUAR' ? $t->quantity : 0;
            $running += ($in - $out);

            $t->in_qty   = $in;
            $t->out_qty  = $out;
            $t->balance  = $running;
        }

        $currentStock = (int) StockCurrent::whereIn('item_variant_id', $variantIds)
            ->sum('current_quantity');

        return view('variants.show', [
            'item'         => $item,
            'transactions' => $transactions,
            'type'         => $type,
            'currentStock' => $currentStock,
        ]);
    }
}
