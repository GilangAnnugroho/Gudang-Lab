<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\ItemVariant;
use App\Models\StockCurrent;
use App\Models\Unit;
use App\Models\Request as RequestModel;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function distribution(Request $request)
    {
        $dateFrom = $request->get('date_from');
        $dateTo   = $request->get('date_to');
        $unitId   = $request->get('unit_id');

        $q = Transaction::with(['variant.itemMaster', 'unit'])
            ->where('type', 'KELUAR');

        if ($dateFrom) {
            $q->whereDate('trans_date', '>=', $dateFrom);
        }
        if ($dateTo) {
            $q->whereDate('trans_date', '<=', $dateTo);
        }
        if ($unitId) {
            $q->where('unit_id', $unitId);
        }

        $rows  = $q->orderBy('trans_date')
                   ->orderBy('id')
                   ->get();

        $units = Unit::orderBy('unit_name')->get();

        return view('reports.distribution', compact(
            'rows', 'dateFrom', 'dateTo', 'unitId', 'units'
        ));
    }

    public function printDistribution(Request $request)
    {
        $dateFrom = $request->get('date_from');
        $dateTo   = $request->get('date_to');
        $unitId   = $request->get('unit_id');

        $q = Transaction::with(['variant.itemMaster', 'unit'])
            ->where('type', 'KELUAR');

        if ($dateFrom) {
            $q->whereDate('trans_date', '>=', $dateFrom);
        }
        if ($dateTo) {
            $q->whereDate('trans_date', '<=', $dateTo);
        }
        if ($unitId) {
            $q->where('unit_id', $unitId);
        }

        $rows = $q->orderBy('trans_date')
                  ->orderBy('id')
                  ->get();
        $user = Auth::user();
        $unit = $unitId ? Unit::find($unitId) : null;

        $pdf = Pdf::loadView('reports.distribution_pdf', [
                'rows'     => $rows,
                'dateFrom' => $dateFrom,
                'dateTo'   => $dateTo,
                'unit'     => $unit,
                'user'     => $user,
            ])
            ->setPaper('A4', 'landscape');

        return $pdf->download('laporan_distribusi.pdf');
    }

    public function stock(Request $request)
    {
        $onlyReagen = $request->boolean('reagen_only');
        $asOf       = $request->get('as_of');
        $mode       = $request->get('mode', 'simple');
        if (!in_array($mode, ['simple', 'opname'], true)) {
            $mode = 'simple';
        }

        $variantQuery = ItemVariant::with(['itemMaster'])
            ->join('items_master', 'items_master.id', '=', 'item_variants.item_master_id')
            ->select('item_variants.*');

        if ($onlyReagen) {
            $variantQuery->whereHas('itemMaster.category', function ($qc) {
                $qc->where('category_name', 'like', '%reagen%');
            });
        }

        $variants = $variantQuery
            ->orderBy('items_master.item_code')
            ->orderBy('item_variants.brand')
            ->orderBy('item_variants.lot_number')
            ->get();

        $txQuery = Transaction::selectRaw('
                item_variant_id,
                SUM(CASE WHEN type = "MASUK"  THEN quantity ELSE 0 END) AS qty_in,
                SUM(CASE WHEN type = "KELUAR" THEN quantity ELSE 0 END) AS qty_out
            ');

        if ($asOf) {
            $txQuery->whereDate('trans_date', '<=', $asOf);
        }

        $txAgg = $txQuery->groupBy('item_variant_id')->get();

        $qtyByVariant = [];
        foreach ($txAgg as $row) {
            $qtyByVariant[$row->item_variant_id] = max(
                0,
                (int) $row->qty_in - (int) $row->qty_out
            );
        }

        $saldoAwal         = [];
        $qtyInPeriod       = [];
        $qtyOutPeriod      = [];
        $saldoAkhirOpname  = [];

        if ($mode === 'opname') {
            $periodAgg = collect();

            if ($asOf) {
                $asOfDate    = Carbon::parse($asOf);
                $periodStart = $asOfDate->copy()->startOfMonth();
                $periodEnd   = $asOfDate->copy();
                $openingAgg = Transaction::selectRaw('
                        item_variant_id,
                        SUM(CASE WHEN type = "MASUK"  THEN quantity ELSE 0 END) AS qty_in,
                        SUM(CASE WHEN type = "KELUAR" THEN quantity ELSE 0 END) AS qty_out
                    ')
                    ->whereDate('trans_date', '<', $periodStart)
                    ->groupBy('item_variant_id')
                    ->get();

                foreach ($openingAgg as $row) {
                    $saldoAwal[$row->item_variant_id] = (int) $row->qty_in - (int) $row->qty_out;
                }

                $periodAgg = Transaction::selectRaw('
                        item_variant_id,
                        SUM(CASE WHEN type = "MASUK"  THEN quantity ELSE 0 END) AS qty_in,
                        SUM(CASE WHEN type = "KELUAR" THEN quantity ELSE 0 END) AS qty_out
                    ')
                    ->whereDate('trans_date', '>=', $periodStart)
                    ->whereDate('trans_date', '<=', $periodEnd)
                    ->groupBy('item_variant_id')
                    ->get();
            } else {
                $periodAgg = Transaction::selectRaw('
                        item_variant_id,
                        SUM(CASE WHEN type = "MASUK"  THEN quantity ELSE 0 END) AS qty_in,
                        SUM(CASE WHEN type = "KELUAR" THEN quantity ELSE 0 END) AS qty_out
                    ')
                    ->groupBy('item_variant_id')
                    ->get();
            }

            foreach ($periodAgg as $row) {
                $id                 = $row->item_variant_id;
                $qtyInPeriod[$id]   = (int) $row->qty_in;
                $qtyOutPeriod[$id]  = (int) $row->qty_out;
            }

            foreach ($variants as $v) {
                $id   = $v->id;
                $open = (int) ($saldoAwal[$id] ?? 0);
                $in   = (int) ($qtyInPeriod[$id] ?? 0);
                $out  = (int) ($qtyOutPeriod[$id] ?? 0);

                $saldoAkhirOpname[$id] = max(0, $open + $in - $out);
            }
        }

        return view('reports.stock', [
            'variants'          => $variants,
            'onlyReagen'        => $onlyReagen,
            'asOf'              => $asOf,
            'qtyByVariant'      => $qtyByVariant,
            'mode'              => $mode,
            'saldoAwal'         => $saldoAwal,
            'qtyInPeriod'       => $qtyInPeriod,
            'qtyOutPeriod'      => $qtyOutPeriod,
            'saldoAkhirOpname'  => $saldoAkhirOpname,
        ]);
    }

    public function printStock(Request $request)
    {
        $onlyReagen = $request->boolean('reagen_only');
        $asOf       = $request->get('as_of');
        $mode       = $request->get('mode', 'simple');
        if (!in_array($mode, ['simple', 'opname'], true)) {
            $mode = 'simple';
        }

        $variantQuery = ItemVariant::with(['itemMaster'])
            ->join('items_master', 'items_master.id', '=', 'item_variants.item_master_id')
            ->select('item_variants.*');

        if ($onlyReagen) {
            $variantQuery->whereHas('itemMaster.category', function ($qc) {
                $qc->where('category_name', 'like', '%reagen%');
            });
        }

        $variants = $variantQuery
            ->orderBy('items_master.item_code')
            ->orderBy('item_variants.brand')
            ->orderBy('item_variants.lot_number')
            ->get();

        $txQuery = Transaction::selectRaw('
                item_variant_id,
                SUM(CASE WHEN type = "MASUK"  THEN quantity ELSE 0 END) AS qty_in,
                SUM(CASE WHEN type = "KELUAR" THEN quantity ELSE 0 END) AS qty_out
            ');

        if ($asOf) {
            $txQuery->whereDate('trans_date', '<=', $asOf);
        }

        $txAgg = $txQuery->groupBy('item_variant_id')->get();

        $qtyByVariant = [];
        foreach ($txAgg as $row) {
            $qtyByVariant[$row->item_variant_id] = max(
                0,
                (int) $row->qty_in - (int) $row->qty_out
            );
        }

        $saldoAwal         = [];
        $qtyInPeriod       = [];
        $qtyOutPeriod      = [];
        $saldoAkhirOpname  = [];

        if ($mode === 'opname') {
            $periodAgg = collect();

            if ($asOf) {
                $asOfDate    = Carbon::parse($asOf);
                $periodStart = $asOfDate->copy()->startOfMonth();
                $periodEnd   = $asOfDate->copy();
                $openingAgg = Transaction::selectRaw('
                        item_variant_id,
                        SUM(CASE WHEN type = "MASUK"  THEN quantity ELSE 0 END) AS qty_in,
                        SUM(CASE WHEN type = "KELUAR" THEN quantity ELSE 0 END) AS qty_out
                    ')
                    ->whereDate('trans_date', '<', $periodStart)
                    ->groupBy('item_variant_id')
                    ->get();

                foreach ($openingAgg as $row) {
                    $saldoAwal[$row->item_variant_id] = (int) $row->qty_in - (int) $row->qty_out;
                }

                $periodAgg = Transaction::selectRaw('
                        item_variant_id,
                        SUM(CASE WHEN type = "MASUK"  THEN quantity ELSE 0 END) AS qty_in,
                        SUM(CASE WHEN type = "KELUAR" THEN quantity ELSE 0 END) AS qty_out
                    ')
                    ->whereDate('trans_date', '>=', $periodStart)
                    ->whereDate('trans_date', '<=', $periodEnd)
                    ->groupBy('item_variant_id')
                    ->get();
            } else {
                $periodAgg = Transaction::selectRaw('
                        item_variant_id,
                        SUM(CASE WHEN type = "MASUK"  THEN quantity ELSE 0 END) AS qty_in,
                        SUM(CASE WHEN type = "KELUAR" THEN quantity ELSE 0 END) AS qty_out
                    ')
                    ->groupBy('item_variant_id')
                    ->get();
            }

            foreach ($periodAgg as $row) {
                $id                 = $row->item_variant_id;
                $qtyInPeriod[$id]   = (int) $row->qty_in;
                $qtyOutPeriod[$id]  = (int) $row->qty_out;
            }

            foreach ($variants as $v) {
                $id   = $v->id;
                $open = (int) ($saldoAwal[$id] ?? 0);
                $in   = (int) ($qtyInPeriod[$id] ?? 0);
                $out  = (int) ($qtyOutPeriod[$id] ?? 0);

                $saldoAkhirOpname[$id] = max(0, $open + $in - $out);
            }
        }

        $user   = Auth::user();
        $search = null;
        $title  = 'Laporan Stok Akhir';

        $pdf = Pdf::loadView('reports.stock_pdf', [
                'variants'          => $variants,
                'onlyReagen'        => $onlyReagen,
                'user'              => $user,
                'search'            => $search,
                'qtyByVariant'      => $qtyByVariant,
                'asOf'              => $asOf,
                'title'             => $title,
                'mode'              => $mode,
                'saldoAwal'         => $saldoAwal,
                'qtyInPeriod'       => $qtyInPeriod,
                'qtyOutPeriod'      => $qtyOutPeriod,
                'saldoAkhirOpname'  => $saldoAkhirOpname,
            ])
            ->setPaper('A4', 'landscape');

        return $pdf->download('laporan_stok_akhir.pdf');
    }

    public function usageYearly(Request $request)
    {
        $year = (int) ($request->get('year') ?: date('Y'));
        $mode = $request->get('mode', 'item');
        if (!in_array($mode, ['item', 'variant'], true)) {
            $mode = 'item';
        }

        $availableYears = Transaction::where('type', 'KELUAR')
            ->selectRaw('YEAR(trans_date) as year')
            ->whereNotNull('trans_date')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year');

        $transactions = Transaction::with(['variant.itemMaster'])
            ->where('type', 'KELUAR')
            ->whereYear('trans_date', $year)
            ->whereNotNull('trans_date')
            ->get();

        $rows          = [];
        $monthlyTotals = array_fill(1, 12, 0);
        $grandTotal    = 0;

        foreach ($transactions as $t) {
            if (!$t->variant || !$t->variant->itemMaster) {
                continue;
            }

            $variant = $t->variant;
            $item    = $variant->itemMaster;

            $month = (int) \Carbon\Carbon::parse($t->trans_date)->format('n');
            if ($month < 1 || $month > 12) {
                continue;
            }

            $qty = (int) $t->quantity;

            if ($mode === 'item') {
                $key = 'item_' . $item->id;
                if (!isset($rows[$key])) {
                    $rows[$key] = (object) [
                        'item_id'        => $item->id,
                        'item_code'      => $item->item_code,
                        'item_name'      => $item->item_name,
                        'base_unit'      => $item->base_unit,
                        'qty_by_month'   => array_fill(1, 12, 0),
                        'total_qty'      => 0,
                    ];
                }
            } else {
                $key = 'variant_' . $variant->id;
                if (!isset($rows[$key])) {
                    $rows[$key] = (object) [
                        'item_id'        => $item->id,
                        'item_code'      => $item->item_code,
                        'item_name'      => $item->item_name,
                        'base_unit'      => $item->base_unit,
                        'variant_id'     => $variant->id,
                        'brand'          => $variant->brand,
                        'lot_number'     => $variant->lot_number,
                        'expiration_date'=> $variant->expiration_date,
                        'qty_by_month'   => array_fill(1, 12, 0),
                        'total_qty'      => 0,
                    ];
                }
            }

            $rows[$key]->qty_by_month[$month] += $qty;
            $rows[$key]->total_qty            += $qty;
            $monthlyTotals[$month] += $qty;
            $grandTotal            += $qty;
        }

        $rows = collect($rows)
            ->sortBy(function ($row) use ($mode) {
                if ($mode === 'item') {
                    return $row->item_code;
                }
                return $row->item_code . ' ' . ($row->brand ?? '');
            })
            ->values();

        return view('reports.usage_yearly', [
            'rows'           => $rows,
            'year'           => $year,
            'mode'           => $mode,
            'availableYears' => $availableYears,
            'monthlyTotals'  => $monthlyTotals,
            'grandTotal'     => $grandTotal,
        ]);
    }

    public function printUsageYearly(Request $request)
    {
        $year = (int) ($request->get('year') ?: date('Y'));
        $mode = $request->get('mode', 'item');
        if (!in_array($mode, ['item', 'variant'], true)) {
            $mode = 'item';
        }

        $transactions = Transaction::with(['variant.itemMaster'])
            ->where('type', 'KELUAR')
            ->whereYear('trans_date', $year)
            ->whereNotNull('trans_date')
            ->get();

        $rows          = [];
        $monthlyTotals = array_fill(1, 12, 0);
        $grandTotal    = 0;

        foreach ($transactions as $t) {
            if (!$t->variant || !$t->variant->itemMaster) {
                continue;
            }

            $variant = $t->variant;
            $item    = $variant->itemMaster;

            $month = (int) \Carbon\Carbon::parse($t->trans_date)->format('n');
            if ($month < 1 || $month > 12) {
                continue;
            }

            $qty = (int) $t->quantity;

            if ($mode === 'item') {
                $key = 'item_' . $item->id;
                if (!isset($rows[$key])) {
                    $rows[$key] = (object) [
                        'item_id'        => $item->id,
                        'item_code'      => $item->item_code,
                        'item_name'      => $item->item_name,
                        'base_unit'      => $item->base_unit,
                        'qty_by_month'   => array_fill(1, 12, 0),
                        'total_qty'      => 0,
                    ];
                }
            } else {
                $key = 'variant_' . $variant->id;
                if (!isset($rows[$key])) {
                    $rows[$key] = (object) [
                        'item_id'        => $item->id,
                        'item_code'      => $item->item_code,
                        'item_name'      => $item->item_name,
                        'base_unit'      => $item->base_unit,
                        'variant_id'     => $variant->id,
                        'brand'          => $variant->brand,
                        'lot_number'     => $variant->lot_number,
                        'expiration_date'=> $variant->expiration_date,
                        'qty_by_month'   => array_fill(1, 12, 0),
                        'total_qty'      => 0,
                    ];
                }
            }

            $rows[$key]->qty_by_month[$month] += $qty;
            $rows[$key]->total_qty            += $qty;

            $monthlyTotals[$month] += $qty;
            $grandTotal            += $qty;
        }

        $rows = collect($rows)
            ->sortBy(function ($row) use ($mode) {
                if ($mode === 'item') {
                    return $row->item_code;
                }
                return $row->item_code . ' ' . ($row->brand ?? '');
            })
            ->values();

        $user = Auth::user();

        $pdf = Pdf::loadView('reports.print_usage_yearly', [
                'rows'          => $rows,
                'year'          => $year,
                'mode'          => $mode,
                'user'          => $user,
                'monthlyTotals' => $monthlyTotals,
                'grandTotal'    => $grandTotal,
            ])
            ->setPaper('A4', 'landscape');

        return $pdf->download('rekap_pemakaian_tahunan_' . $year . '.pdf');
    }

    public function approvedRequests(Request $request)
    {
        $dateFrom = $request->get('date_from');
        $dateTo   = $request->get('date_to');

        $q = RequestModel::with([
                'unit',
                'requester',                 
                'approver',                
                'details.variant.itemMaster',
            ])
            ->where('status', RequestModel::STATUS_APPROVED);

        if ($dateFrom) {
            $q->whereDate('updated_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $q->whereDate('updated_at', '<=', $dateTo);
        }

        $rows = $q->orderBy('updated_at', 'desc')->get();

        return view('reports.approved', compact('rows', 'dateFrom', 'dateTo'));
    }

    public function printApprovedRequests(Request $request)
    {
        $dateFrom = $request->get('date_from');
        $dateTo   = $request->get('date_to');

        $q = RequestModel::with([
                'unit',
                'requester',
                'approver',
                'details.variant.itemMaster',
            ])
            ->where('status', RequestModel::STATUS_APPROVED);

        if ($dateFrom) {
            $q->whereDate('updated_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $q->whereDate('updated_at', '<=', $dateTo);
        }

        $rows = $q->orderBy('updated_at', 'desc')->get();

        $user = Auth::user();

        $pdf = Pdf::loadView('reports.approved_pdf', [
                'rows'     => $rows,
                'dateFrom' => $dateFrom,
                'dateTo'   => $dateTo,
                'user'     => $user,
            ])
            ->setPaper('A4', 'portrait');

        return $pdf->download('laporan_permintaan_disetujui.pdf');
    }


    public function outgoing(Request $request)
    {
        $dateFrom = $request->get('date_from');
        $dateTo   = $request->get('date_to');
        $unitId   = $request->get('unit_id');

        $q = Transaction::with(['variant.itemMaster', 'unit'])
            ->where('type', 'KELUAR');

        if ($dateFrom) {
            $q->whereDate('trans_date', '>=', $dateFrom);
        }
        if ($dateTo) {
            $q->whereDate('trans_date', '<=', $dateTo);
        }
        if ($unitId) {
            $q->where('unit_id', $unitId);
        }

        $rows  = $q->orderBy('trans_date')->orderBy('id')->get();
        $units = Unit::orderBy('unit_name')->get();

        return view('reports.outgoing', compact(
            'rows', 'dateFrom', 'dateTo', 'unitId', 'units'
        ));
    }

    public function printOutgoing(Request $request)
    {
        $dateFrom = $request->get('date_from');
        $dateTo   = $request->get('date_to');
        $unitId   = $request->get('unit_id');

        $q = Transaction::with(['variant.itemMaster', 'unit'])
            ->where('type', 'KELUAR');

        if ($dateFrom) {
            $q->whereDate('trans_date', '>=', $dateFrom);
        }
        if ($dateTo) {
            $q->whereDate('trans_date', '<=', $dateTo);
        }
        if ($unitId) {
            $q->where('unit_id', $unitId);
        }

        $rows = $q->orderBy('trans_date')->orderBy('id')->get();

        $user = Auth::user();
        $unit = $unitId ? Unit::find($unitId) : null;

        $pdf = Pdf::loadView('reports.outgoing_pdf', [
                'rows'     => $rows,
                'dateFrom' => $dateFrom,
                'dateTo'   => $dateTo,
                'unit'     => $unit,
                'user'     => $user,
            ])
            ->setPaper('A4', 'landscape');

        return $pdf->download('laporan_barang_keluar.pdf');
    }

    public function unitReceived(Request $request)
    {
        $user   = Auth::user();
        $unitId = $user->unit_id ?? null;

        $dateFrom = $request->get('date_from');
        $dateTo   = $request->get('date_to');

        $q = Transaction::with(['variant.itemMaster', 'unit'])
            ->where('type', 'KELUAR');

        if ($unitId) {
            $q->where('unit_id', $unitId);
        }

        if ($dateFrom) {
            $q->whereDate('trans_date', '>=', $dateFrom);
        }
        if ($dateTo) {
            $q->whereDate('trans_date', '<=', $dateTo);
        }

        $rows = $q->orderBy('trans_date')->orderBy('id')->get();

        return view('reports.unit_received', compact('rows', 'dateFrom', 'dateTo'));
    }

    public function printUnitReceived(Request $request)
    {
        $user   = Auth::user();
        $unitId = $user->unit_id ?? null;

        $dateFrom = $request->get('date_from');
        $dateTo   = $request->get('date_to');

        $q = Transaction::with(['variant.itemMaster', 'unit'])
            ->where('type', 'KELUAR');

        if ($unitId) {
            $q->where('unit_id', $unitId);
        }

        if ($dateFrom) {
            $q->whereDate('trans_date', '>=', $dateFrom);
        }
        if ($dateTo) {
            $q->whereDate('trans_date', '<=', $dateTo);
        }

        $rows = $q->orderBy('trans_date')->orderBy('id')->get();

        $pdf = Pdf::loadView('reports.unit_received_pdf', [
                'rows'     => $rows,
                'dateFrom' => $dateFrom,
                'dateTo'   => $dateTo,
                'user'     => $user,
            ])
            ->setPaper('A4', 'portrait');

        return $pdf->download('laporan_barang_diterima_unit.pdf');
    }
}
