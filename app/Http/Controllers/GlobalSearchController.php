<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ItemMaster;
use App\Models\ItemVariant;
use App\Models\Request as RequestModel;
use App\Models\Transaction;

class GlobalSearchController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $term = trim($request->get('q', ''));
        $user = Auth::user();
        $roleName = strtolower(optional($user->role)->role_name ?? '');
        $items        = collect();
        $variants     = collect();
        $requests     = collect();
        $transactions = collect();

        if ($term === '') {
            return view('search.global', compact(
                'term', 'items', 'variants', 'requests', 'transactions'
            ));
        }

        if (in_array($roleName, ['super admin', 'admin gudang'], true)) {
            $items = ItemMaster::query()
                ->where('item_code', 'like', "%{$term}%")
                ->orWhere('item_name', 'like', "%{$term}%")
                ->orderBy('item_code')
                ->limit(15)
                ->get();

            $variants = ItemVariant::with('itemMaster')
                ->where('brand', 'like', "%{$term}%")
                ->orWhere('lot_number', 'like', "%{$term}%")
                ->orderBy('brand')
                ->orderBy('lot_number')
                ->limit(15)
                ->get();

            $requests = RequestModel::with(['unit'])
                ->where(function ($q) use ($term) {
                    if (ctype_digit($term)) {
                        $q->orWhere('id', (int) $term);
                    }

                    $q->orWhere('status', 'like', "%{$term}%");

                    $q->orWhereHas('unit', function ($uq) use ($term) {
                        $uq->where('unit_name', 'like', "%{$term}%");
                    });
                })
                ->orderByDesc('created_at')
                ->limit(15)
                ->get();
            $transactions = Transaction::with(['variant.itemMaster', 'unit', 'supplier'])
                ->whereHas('variant.itemMaster', function ($q) use ($term) {
                    $q->where('item_name', 'like', "%{$term}%")
                      ->orWhere('item_code', 'like', "%{$term}%");
                })
                ->orWhereHas('unit', function ($q) use ($term) {
                    $q->where('unit_name', 'like', "%{$term}%");
                })
                ->orWhereHas('supplier', function ($q) use ($term) {
                    $q->where('supplier_name', 'like', "%{$term}%");
                })
                ->orderByDesc('trans_date')
                ->limit(20)
                ->get();
        }

        if ($roleName === 'kepala lab') {
            $requests = RequestModel::with(['unit'])
                ->where(function ($q) use ($term) {
                    if (ctype_digit($term)) {
                        $q->orWhere('id', (int) $term);
                    }

                    $q->orWhere('status', 'like', "%{$term}%");

                    $q->orWhereHas('unit', function ($uq) use ($term) {
                        $uq->where('unit_name', 'like', "%{$term}%");
                    });
                })
                ->orderByDesc('created_at')
                ->limit(20)
                ->get();

            $transactions = Transaction::with(['variant.itemMaster', 'unit'])
                ->where('type', 'KELUAR')
                ->where(function ($q) use ($term) {
                    $q->whereHas('variant.itemMaster', function ($iq) use ($term) {
                        $iq->where('item_name', 'like', "%{$term}%")
                           ->orWhere('item_code', 'like', "%{$term}%");
                    })
                    ->orWhereHas('unit', function ($uq) use ($term) {
                        $uq->where('unit_name', 'like', "%{$term}%");
                    });
                })
                ->orderByDesc('trans_date')
                ->limit(20)
                ->get();
        }

        if ($roleName === 'petugas unit') {
            $unitId = $user->unit_id ?? null;
            $requests = RequestModel::with(['unit'])
                ->when($unitId, function ($q) use ($unitId) {
                    $q->where('unit_id', $unitId);
                })
                ->where(function ($q) use ($term) {
                    if (ctype_digit($term)) {
                        $q->orWhere('id', (int) $term);
                    }

                    $q->orWhere('status', 'like', "%{$term}%");
                })
                ->orderByDesc('created_at')
                ->limit(20)
                ->get();

            $transactions = Transaction::with(['variant.itemMaster', 'unit'])
                ->where('type', 'KELUAR')
                ->when($unitId, function ($q) use ($unitId) {
                    $q->where('unit_id', $unitId);
                })
                ->whereHas('variant.itemMaster', function ($iq) use ($term) {
                    $iq->where('item_name', 'like', "%{$term}%")
                       ->orWhere('item_code', 'like', "%{$term}%");
                })
                ->orderByDesc('trans_date')
                ->limit(20)
                ->get();
        }

        return view('search.global', compact(
            'term', 'items', 'variants', 'requests', 'transactions'
        ));
    }
}
