<?php

namespace App\Http\Controllers;

use App\Models\Request as RequestModel;
use App\Models\RequestDetail;
use App\Models\Unit;
use App\Models\ItemMaster;
use App\Models\ItemVariant;
use Illuminate\Http\Request;

class RequestController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index(Request $request)
    {
        $this->authorize('viewAny', RequestModel::class);

        $status = $request->get('status');
        $unitId = $request->get('unit_id');
        $from   = $request->get('from');
        $to     = $request->get('to');

        $q = RequestModel::with([
                'requester',
                'approver',
                'unit',
                'details.itemMaster.category',
                'details.itemVariant',
            ])
            ->withCount('transactions') 
            ->forUser($request->user())
            ->status($status)
            ->unitId($unitId)
            ->dateBetween($from, $to);

        $requests = $q->orderByDesc('request_date')
                      ->orderByDesc('id')
                      ->paginate(20)
                      ->withQueryString();

        $units = Unit::orderBy('unit_name')->pluck('unit_name', 'id');

        return view('requests.index', compact(
            'requests',
            'units',
            'status',
            'unitId',
            'from',
            'to'
        ));
    }

    public function create(Request $request)
    {
        $this->authorize('create', RequestModel::class);

        $user = $request->user();

        if ($user->unit_id) {
            $units = Unit::where('id', $user->unit_id)
                ->pluck('unit_name', 'id');
            $selectedUnitId = $user->unit_id;
        } else {
            $units = Unit::orderBy('unit_name')->pluck('unit_name', 'id');
            $selectedUnitId = null;
        }

        $items = ItemMaster::orderBy('item_code')
            ->selectRaw("id, CONCAT(item_code,' — ',item_name) AS label")
            ->pluck('label', 'id');

        return view('requests.create', [
            'units'          => $units,
            'selectedUnitId' => $selectedUnitId,
            'items'          => $items,
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', RequestModel::class);

        $data = $request->validate([
            'request_date' => 'required|date',
            'unit_id'      => 'required|exists:units,id',

            'details'      => 'required|array|min:1',
            'details.*.item_master_id'     => 'required|exists:items_master,id',
            'details.*.item_variant_id'    => 'nullable|exists:item_variants,id',
            'details.*.requested_quantity' => 'required|integer|min:1',
            'details.*.notes'              => 'nullable|string',
        ]);

        $req = RequestModel::create([
            'request_date'     => $data['request_date'],
            'status'           => RequestModel::STATUS_PENDING,
            'request_user_id'  => auth()->id(),
            'approver_user_id' => null,
            'unit_id'          => $data['unit_id'],
        ]);

        foreach ($data['details'] as $d) {
            RequestDetail::create([
                'request_id'          => $req->id,
                'item_master_id'      => $d['item_master_id'],
                'item_variant_id'     => $d['item_variant_id'] ?? null,
                'requested_quantity'  => $d['requested_quantity'],
                'distributed_quantity'=> 0,
                'notes'               => $d['notes'] ?? null,
            ]);
        }

        return redirect()
            ->route('requests.show', $req)
            ->with('success', 'Permintaan barang berhasil dibuat dan menunggu persetujuan.');
    }

    public function show(RequestModel $requestModel)
    {
        $this->authorize('view', $requestModel);

        $requestModel->load([
            'requester',
            'approver',
            'unit',
            'details.itemMaster.category',
            'details.itemVariant',
            'transactions.variant.itemMaster',
            'transactions.unit',
        ]);

        return view('requests.show', [
            'req' => $requestModel,
        ]);
    }

    public function edit(RequestModel $requestModel)
    {
        $this->authorize('update', $requestModel);

        if (!$requestModel->is_pending) {
            return redirect()
                ->route('requests.show', $requestModel)
                ->with('error', 'Permintaan yang sudah diproses tidak dapat diedit.');
        }

        $units = Unit::orderBy('unit_name')->pluck('unit_name', 'id');

        return view('requests.edit', [
            'req'   => $requestModel,
            'units' => $units,
        ]);
    }

    public function update(Request $request, RequestModel $requestModel)
    {
        $this->authorize('update', $requestModel);

        if (!$requestModel->is_pending) {
            return redirect()
                ->route('requests.show', $requestModel)
                ->with('error', 'Permintaan yang sudah diproses tidak dapat diubah.');
        }

        $data = $request->validate([
            'request_date' => 'required|date',
            'unit_id'      => 'required|exists:units,id',
        ]);

        $requestModel->update($data);

        return redirect()
            ->route('requests.show', $requestModel)
            ->with('success', 'Header permintaan berhasil diperbarui.');
    }

    public function destroy(RequestModel $requestModel)
    {
        $this->authorize('delete', $requestModel);

        if (!$requestModel->is_pending) {
            return redirect()
                ->route('requests.show', $requestModel)
                ->with('error', 'Permintaan yang sudah diproses tidak dapat dihapus.');
        }

        $requestModel->details()->delete();
        $requestModel->delete();

        return redirect()
            ->route('requests.index')
            ->with('success', 'Permintaan berhasil dihapus.');
    }
}
