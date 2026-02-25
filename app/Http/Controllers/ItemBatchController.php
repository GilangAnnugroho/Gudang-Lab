<?php

namespace App\Http\Controllers;

use App\Models\ItemBatch;
use App\Models\ItemMaster;
use App\Models\ItemVariant;
use Illuminate\Http\Request;

class ItemBatchController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','role:Super Admin|Admin Gudang']);
    }

    public function index(Request $request)
    {
        $s         = trim($request->get('q', ''));
        $itemId    = $request->get('item_id');
        $variantId = $request->get('variant_id');

        $q = ItemBatch::with(['variant.itemMaster']);

        if ($s) {
            $q->where(function ($qq) use ($s) {
                $qq->where('lot_number', 'like', "%{$s}%")
                   ->orWhereHas('variant', function ($qv) use ($s) {
                       $qv->where('brand', 'like', "%{$s}%")
                          ->orWhereHas('itemMaster', function ($qi) use ($s) {
                              $qi->where('item_code', 'like', "%{$s}%")
                                 ->orWhere('item_name', 'like', "%{$s}%");
                          });
                   });
            });
        }

        if ($itemId) {
            $q->whereHas('variant', function ($qq) use ($itemId) {
                $qq->where('item_master_id', $itemId);
            });
        }

        if ($variantId) {
            $q->where('item_variant_id', $variantId);
        }

        $batches = $q->orderBy('expiration_date')
                     ->orderBy('lot_number')
                     ->paginate(15)
                     ->withQueryString();

        $items = ItemMaster::orderBy('item_name')
            ->get()
            ->mapWithKeys(function ($it) {
                return [
                    $it->id => "{$it->item_code} — {$it->item_name}",
                ];
            })
            ->toArray();

        $variants = ItemVariant::with('itemMaster')
            ->orderBy('brand')
            ->get();

        return view('batches.index', compact(
            'batches',
            'items',
            'variants',
            's',
            'itemId',
            'variantId'
        ));
    }

    public function create()
    {
        $items = ItemMaster::orderBy('item_name')
            ->get()
            ->mapWithKeys(function ($it) {
                return [
                    $it->id => "{$it->item_code} — {$it->item_name}",
                ];
            });

        $variants = ItemVariant::with('itemMaster')
            ->orderBy('brand')
            ->get();

        return view('batches.create', compact('items', 'variants'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'item_variant_id'  => ['required', 'exists:item_variants,id'],
            'lot_number'       => ['nullable', 'string', 'max:100'],
            'expiration_date'  => ['nullable', 'date'],
        ]);

        $batch = ItemBatch::create([
            'item_variant_id'  => $data['item_variant_id'],
            'lot_number'       => $data['lot_number']      ?? null,
            'expiration_date'  => $data['expiration_date'] ?? null,
            'quantity_in'      => 0,
            'quantity_out'     => 0,
            'current_quantity' => 0,
        ]);

        $lotLabel = $batch->lot_number ?: 'Tanpa Lot';

        return redirect()
            ->route('batches.index')
            ->with('success', "Batch <strong>{$lotLabel}</strong> berhasil dibuat.");
    }

    public function edit(ItemBatch $batch)
    {
        $items = ItemMaster::orderBy('item_name')
            ->get()
            ->mapWithKeys(function ($it) {
                return [
                    $it->id => "{$it->item_code} — {$it->item_name}",
                ];
            });

        $variants = ItemVariant::with('itemMaster')
            ->orderBy('brand')
            ->get();

        return view('batches.edit', compact('batch', 'items', 'variants'));
    }

    public function update(Request $request, ItemBatch $batch)
    {
        $data = $request->validate([
            'item_variant_id'  => ['required', 'exists:item_variants,id'],
            'lot_number'       => ['nullable', 'string', 'max:100'],
            'expiration_date'  => ['nullable', 'date'],
        ]);

        $batch->update([
            'item_variant_id'  => $data['item_variant_id'],
            'lot_number'       => $data['lot_number']      ?? null,
            'expiration_date'  => $data['expiration_date'] ?? null,
        ]);

        $lotLabel = $batch->lot_number ?: 'Tanpa Lot';

        return redirect()
            ->route('batches.index')
            ->with('success', "Batch <strong>{$lotLabel}</strong> berhasil diperbarui.");
    }
    
    public function destroy(ItemBatch $batch)
    {
        $lotLabel = $batch->lot_number ?: 'Tanpa Lot';
        $batch->delete();

        return redirect()
            ->route('batches.index')
            ->with('success', "Batch <strong>{$lotLabel}</strong> berhasil dihapus.");
    }
}
