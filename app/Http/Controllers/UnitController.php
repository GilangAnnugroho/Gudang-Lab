<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;
use App\Http\Requests\StoreUnitRequest;
use App\Http\Requests\UpdateUnitRequest;

class UnitController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:Admin Gudang|Super Admin']);
    }

    public function index(Request $request)
    {
        $search = trim($request->get('q', ''));

        $q = Unit::query()
            ->withCount([
                'users',
                'requests',
                'destinationTransactions',
            ]);

        if ($search !== '') {
            $q->search($search);
        }

        $units = $q->orderBy('unit_name')
                   ->paginate(15)
                   ->withQueryString();

        return view('units.index', [
            'units'  => $units,
            'search' => $search,
        ]);
    }

    public function create()
    {
        return view('units.create');
    }

    public function store(StoreUnitRequest $request)
    {
        $row = Unit::create($request->validated());

        return redirect()
            ->route('units.index')
            ->with('success', "Unit <strong>{$row->unit_name}</strong> berhasil dibuat.");
    }

    public function edit(Unit $unit)
    {
        return view('units.edit', ['unit' => $unit]);
    }

    public function update(UpdateUnitRequest $request, Unit $unit)
    {
        $unit->update($request->validated());

        return redirect()
            ->route('units.index')
            ->with('success', "Unit <strong>{$unit->unit_name}</strong> berhasil diperbarui.");
    }

    public function destroy(Unit $unit)
    {
        if (
            $unit->users()->exists() ||
            $unit->requests()->exists() ||
            $unit->destinationTransactions()->exists()
        ) {
            return redirect()
                ->route('units.index')
                ->with('error', "Unit <strong>{$unit->unit_name}</strong> tidak dapat dihapus karena sudah digunakan pada user, permintaan, atau transaksi.");
        }

        $name = $unit->unit_name;
        $unit->delete();

        return redirect()
            ->route('units.index')
            ->with('success', "Unit <strong>{$name}</strong> berhasil dihapus.");
    }
}
