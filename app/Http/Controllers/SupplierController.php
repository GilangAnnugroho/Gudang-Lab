<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use App\Http\Requests\StoreSupplierRequest;
use App\Http\Requests\UpdateSupplierRequest;

class SupplierController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:Super Admin|Admin Gudang');
    }

    public function index(Request $request)
    {
        $search = trim($request->get('q', ''));

        $q = Supplier::query()
            ->withCount([
                'transactions as total_masuk' => fn ($x) => $x->where('type', 'MASUK'),
            ]);

        if ($search !== '') {
            $q->where(function ($w) use ($search) {
                $w->where('supplier_name', 'like', "%{$search}%")
                  ->orWhere('contact_person', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $suppliers = $q->orderBy('supplier_name')
                       ->paginate(12)
                       ->withQueryString();

        return view('suppliers.index', [
            'suppliers' => $suppliers,
            'search'    => $search,
        ]);
    }

    public function create()
    {
        return view('suppliers.create');
    }

    public function store(StoreSupplierRequest $request)
    {
        $row = Supplier::create($request->validated());

        return redirect()
            ->route('suppliers.index')
            ->with('success', "Supplier <strong>{$row->supplier_name}</strong> berhasil dibuat.");
    }

    public function edit(Supplier $supplier)
    {
        return view('suppliers.edit', ['supplier' => $supplier]);
    }

    public function update(UpdateSupplierRequest $request, Supplier $supplier)
    {
        $supplier->update($request->validated());

        return redirect()
            ->route('suppliers.index')
            ->with('success', "Supplier <strong>{$supplier->supplier_name}</strong> berhasil diperbarui.");
    }

    public function destroy(Supplier $supplier)
    {
        if ($supplier->transactions()->exists()) {
            return redirect()
                ->route('suppliers.index')
                ->with('error', "Supplier <strong>{$supplier->supplier_name}</strong> tidak dapat dihapus karena sudah dipakai pada transaksi.");
        }

        $name = $supplier->supplier_name;
        $supplier->delete();

        return redirect()
            ->route('suppliers.index')
            ->with('success', "Supplier <strong>{$name}</strong> berhasil dihapus.");
    }
}
