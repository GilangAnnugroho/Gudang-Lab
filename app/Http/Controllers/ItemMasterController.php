<?php

namespace App\Http\Controllers;

use App\Models\ItemMaster;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Requests\StoreItemMasterRequest;
use App\Http\Requests\UpdateItemMasterRequest;
use Illuminate\Database\QueryException; 

class ItemMasterController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','role:Super Admin|Admin Gudang']);
    }

    public function index(Request $request)
    {
        $s        = trim($request->get('q',''));
        $category = $request->get('category_id');

        $q = ItemMaster::with('category')->search($s);

        if ($category) {
            $q->where('category_id', $category);
        }

        $items = $q->join('categories', 'categories.id', '=', 'items_master.category_id')
                   ->select('items_master.*')
                   ->orderBy('categories.category_name')
                   ->orderBy('items_master.item_code')
                   ->paginate(12)
                   ->withQueryString();

        $categories = Category::orderBy('category_name')
            ->pluck('category_name','id');

        return view('items.index', compact('items','categories','s','category'));
    }

    public function create()
    {
        $categories = Category::orderBy('category_name')->pluck('category_name','id');
        return view('items.create', compact('categories'));
    }

    public function store(StoreItemMasterRequest $request)
    {
        $data = $request->validated();
        $data['warning_stock'] = $request->input('warning_stock');

        $row = ItemMaster::create($data);

        return redirect()
            ->route('items.index')
            ->with('success', "Item <strong>{$row->item_code}</strong> berhasil dibuat.");
    }

    public function edit(ItemMaster $item)
    {
        $categories = Category::orderBy('category_name')->pluck('category_name','id');
        return view('items.edit', compact('item','categories'));
    }

    public function update(UpdateItemMasterRequest $request, ItemMaster $item)
    {
        $data = $request->validated();
        $data['warning_stock'] = $request->input('warning_stock');

        $item->update($data);

        return redirect()
            ->route('items.index')
            ->with('success', "Item <strong>{$item->item_code}</strong> berhasil diperbarui.");
    }

    public function destroy(ItemMaster $item)
    {
        $code = $item->item_code;

        try {
            $item->delete();

            return redirect()
                ->route('items.index')
                ->with('success', "Item <strong>{$code}</strong> berhasil dihapus.");
        } catch (QueryException $e) {
            if ($e->getCode() === '23000') {
                return redirect()
                    ->route('items.index')
                    ->with('error',
                        "Item <strong>{$code}</strong> tidak dapat dihapus karena sudah dipakai ".
                        "pada <strong>varian, permintaan, atau transaksi</strong>. ".
                        "Silakan pastikan tidak ada data yang masih menggunakan item ini."
                    );
            }

            return redirect()
                ->route('items.index')
                ->with('error', "Terjadi kesalahan saat menghapus item <strong>{$code}</strong>.");
        }
    }

    public function show(ItemMaster $item)
    {
        return redirect()->route('items.edit', $item);
    }
}
