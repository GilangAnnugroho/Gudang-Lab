<?php

namespace App\Http\Controllers;

use App\Models\RequestDetail;
use Illuminate\Http\Request;

class RequestDetailController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function update(Request $request, RequestDetail $requestDetail)
    {
        if ($requestDetail->request->status !== \App\Models\Request::STATUS_PENDING) {
            return response()->json(['message' => 'Tidak bisa diubah, status parent bukan PENDING'], 422);
        }

        $data = $request->validate([
            'item_master_id'     => 'required|exists:items_master,id',
            'item_variant_id'    => 'nullable|exists:item_variants,id',
            'requested_quantity' => 'required|integer|min:1',
            'notes'              => 'nullable|string',
        ]);

        $requestDetail->update([
            'item_master_id'     => $data['item_master_id'],
            'item_variant_id'    => $data['item_variant_id'] ?? null,
            'requested_quantity' => $data['requested_quantity'],
            'notes'              => $data['notes'] ?? null,
        ]);

        return response()->json(
            $requestDetail->load('itemMaster','itemVariant')
        );
    }

    public function destroy(RequestDetail $requestDetail)
    {
        if ($requestDetail->request->status !== \App\Models\Request::STATUS_PENDING) {
            return response()->json(['message' => 'Tidak bisa dihapus, status parent bukan PENDING'], 422);
        }

        $requestDetail->delete();
        return response()->json(['ok' => true]);
    }
}
