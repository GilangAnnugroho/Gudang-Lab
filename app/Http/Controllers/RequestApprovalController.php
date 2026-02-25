<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Request as RequestModel;
use App\Models\Transaction;
use App\Models\StockCurrent;
use App\Models\ItemVariant;

class RequestApprovalController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function approve(Request $request, RequestModel $requestModel)
    {
        $this->authorize('approve', $requestModel);

        if ($requestModel->status !== RequestModel::STATUS_PENDING) {
            return back()->with('error', 'Hanya permintaan dengan status PENDING yang dapat di-approve.');
        }

        $requestModel->status           = RequestModel::STATUS_APPROVED;
        $requestModel->approver_user_id = $request->user()->id;
        $requestModel->save();

        return back()->with('success', 'Permintaan berhasil disetujui.');
    }

    public function reject(Request $request, RequestModel $requestModel)
    {
        $this->authorize('approve', $requestModel);

        if ($requestModel->status !== RequestModel::STATUS_PENDING) {
            return back()->with('error', 'Hanya permintaan dengan status PENDING yang dapat ditolak.');
        }

        $requestModel->status           = RequestModel::STATUS_REJECTED;
        $requestModel->approver_user_id = $request->user()->id;
        $requestModel->save();

        return back()->with('success', 'Permintaan telah ditolak.');
    }

    public function distribute(Request $request, RequestModel $requestModel)
    {
        $this->authorize('distribute', $requestModel);

        if ($requestModel->status !== RequestModel::STATUS_APPROVED) {
            return back()->with('error', 'Distribusi hanya dapat dilakukan pada permintaan dengan status APPROVED.');
        }

        $data = $request->validate([
            'trans_date' => 'nullable|date',
            'doc_no'     => 'nullable|string|max:100',
            'note'       => 'nullable|string|max:255',
        ]);
        $transDate = $data['trans_date'] ?? now()->toDateString();
        $docNo     = $data['doc_no']     ?? 'REQ-' . $requestModel->id;
        $note      = $data['note']       ?? 'Distribusi dari permintaan #' . $requestModel->id;
        $requestModel->load(['details.itemMaster', 'details.itemVariant.stock', 'unit']);
        $createdCount = 0;
        $errors       = [];

        foreach ($requestModel->details as $idx => $detail) {
            $remaining = (int) $detail->requested_quantity - (int) $detail->distributed_quantity;

            if ($remaining <= 0) {
                continue;
            }

            $variant = $detail->itemVariant;

            if (!$variant) {
                $variantsQuery = ItemVariant::with(['stock', 'itemMaster'])
                    ->where('item_master_id', $detail->item_master_id)
                    ->whereHas('stock', function ($q) {
                        $q->where('current_quantity', '>', 0);
                    })
                    ->where(function ($q) {
                        $q->whereNull('expiration_date')
                          ->orWhere('expiration_date', '>=', now()->toDateString());
                    })
                    ->orderByRaw('CASE WHEN expiration_date IS NULL THEN 1 ELSE 0 END') 
                    ->orderBy('expiration_date', 'ASC');

                $variant = $variantsQuery->first();
            }

            if (!$variant) {
                $itemName = optional($detail->itemMaster)->item_name ?? 'Item tanpa nama';
                $errors[] = "Detail #" . ($idx + 1) . " ({$itemName}) tidak memiliki varian dengan stok yang cukup.";
                continue;
            }

            $stock = $variant->stock;
            $currentQty = (int) optional($stock)->current_quantity;

            if ($currentQty <= 0) {
                $errors[] = "Stok varian {$variant->brand} / {$variant->lot_number} kosong.";
                continue;
            }

            $qtyOut = min($remaining, $currentQty);

            if ($qtyOut <= 0) {
                continue;
            }

            Transaction::create([
                'type'            => 'KELUAR',
                'trans_date'      => $transDate,
                'doc_no'          => $docNo,
                'invoice_no'      => null,
                'item_variant_id' => $variant->id,
                'brand'           => $variant->brand,
                'lot_number'      => $variant->lot_number,
                'expiration_date' => $variant->expiration_date,
                'quantity'        => $qtyOut,
                'price'           => 0,
                'tax_amount'      => 0,
                'total_amount'    => 0,
                'supplier_id'     => null,
                'unit_id'         => $requestModel->unit_id,
                'request_id'      => $requestModel->id,
                'note'            => $note,
                'payment_status'  => 'LUNAS',
                'storage_condition' => optional($variant->itemMaster)->storage_temp,
                'created_by'      => $request->user()->id,
            ]);

            $detail->distributed_quantity += $qtyOut;
            $detail->item_variant_id       = $variant->id; 
            $detail->save();

            if ($stock) {
                $stock->current_quantity = max(0, $stock->current_quantity - $qtyOut);
                $stock->save();
            } else {
                StockCurrent::create([
                    'item_variant_id'  => $variant->id,
                    'current_quantity' => max(0, $currentQty - $qtyOut),
                ]);
            }

            $createdCount++;
        }

        $stillPending = $requestModel->details()
            ->whereColumn('distributed_quantity', '<', 'requested_quantity')
            ->exists();

        if (!$stillPending) {
            $requestModel->status = RequestModel::STATUS_DISTRIBUTED;
            $requestModel->save();
        }

        if ($createdCount === 0) {
            $msg = 'Tidak ada transaksi distribusi yang dibuat.';
            if ($errors) {
                $msg .= ' Beberapa detail tidak dapat diproses:<ul><li>' . implode('</li><li>', $errors) . '</li></ul>';
            }
            return back()->with('error', $msg);
        }

        $msg = "Distribusi berhasil. {$createdCount} transaksi KELUAR dibuat.";
        if ($errors) {
            $msg .= '<br><small>Catatan:<ul><li>' . implode('</li><li>', $errors) . '</li></ul></small>';
        }

        return redirect()
            ->route('requests.show', $requestModel->id)
            ->with('success', $msg);
    }
}
