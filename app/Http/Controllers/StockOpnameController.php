<?php

namespace App\Http\Controllers;

use App\Models\StockOpname;
use App\Models\ItemVariant;
use App\Models\StockCurrent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf; 

class StockOpnameController extends Controller
{
    private function getFilteredQuery(Request $request)
    {
        $query = StockOpname::with(['variant.itemMaster', 'user']);

        if ($request->has('q') && $request->q != '') {
            $search = $request->q;
            $query->whereHas('variant', function($q) use ($search) {
                $q->where(function($subQ) use ($search) {
                    $subQ->where('brand', 'like', "%{$search}%")
                         ->orWhere('lot_number', 'like', "%{$search}%");
                })
                ->orWhereHas('itemMaster', function($q2) use ($search) {
                    $q2->where('item_name', 'like', "%{$search}%")
                       ->orWhere('item_code', 'like', "%{$search}%");
                });
            });
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('opname_date', [$request->start_date, $request->end_date]);
        } elseif ($request->filled('start_date')) {
            $query->where('opname_date', '>=', $request->start_date);
        } elseif ($request->filled('end_date')) {
            $query->where('opname_date', '<=', $request->end_date);
        }

        return $query;
    }

    public function index(Request $request)
    {
        $query = $this->getFilteredQuery($request);
        $opnames = $query->latest('opname_date')->paginate(10);
        $opnames->appends([
            'q' => $request->q,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date
        ]);

        return view('stock_opnames.index', compact('opnames'));
    }

    public function print(Request $request)
    {
        $query = $this->getFilteredQuery($request);
        $opnames = $query->orderBy('opname_date', 'asc')->get();
        $meta = [
            'start_date' => $request->start_date,
            'end_date'   => $request->end_date,
            'print_date' => now()->format('d-m-Y H:i'),
            'user'       => auth()->user()->name
        ];

        $pdf = Pdf::loadView('stock_opnames.pdf', compact('opnames', 'meta'))
                  ->setPaper('a4', 'landscape'); 
        return $pdf->stream('Laporan_Stok_Opname_' . date('Ymd_Hi') . '.pdf');
    }

    public function create()
    {
        $variants = ItemVariant::with('itemMaster', 'stock')->get();
        return view('stock_opnames.create', compact('variants'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'opname_date'     => 'required|date',
            'item_variant_id' => 'required|exists:item_variants,id',
            'physical_stock'  => 'required|integer|min:0',
            'notes'           => 'nullable|string|max:1000',
        ]);

        DB::transaction(function () use ($request) {
            $variant = ItemVariant::findOrFail($request->item_variant_id);
            $currentStockData = StockCurrent::where('item_variant_id', $variant->id)->first();
            $systemStock = $currentStockData ? $currentStockData->current_quantity : 0;
            $physicalStock = (int) $request->physical_stock;
            $difference = $physicalStock - $systemStock;

            StockOpname::create([
                'user_id'         => auth()->id(),
                'item_variant_id' => $variant->id,
                'opname_date'     => $request->opname_date,
                'system_stock'    => $systemStock,
                'physical_stock'  => $physicalStock,
                'difference'      => $difference,
                'notes'           => $request->notes,
            ]);

            StockCurrent::updateOrCreate(
                ['item_variant_id' => $variant->id],
                ['current_quantity' => $physicalStock]
            );
        });

        return redirect()->route('stock-opnames.index');
    }
}