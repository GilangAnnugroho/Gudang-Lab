<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Models\Transaction;
use App\Models\ItemVariant;
use App\Models\StockCurrent;
use App\Models\Supplier;
use App\Models\Unit;
use App\Models\ItemBatch;

class TransactionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
        $this->middleware('role:Admin Gudang|Super Admin')
            ->only(['store','update','destroy','create','edit']);
    }

    public function index(Request $request)
    {
        $variantId = $request->get('variant_id');
        $type      = $request->get('type');
        $dateFrom  = $request->get('date_from');
        $dateTo    = $request->get('date_to');
        $requestId = $request->get('request_id');

        $q = Transaction::query()
            ->with([
                'variant.itemMaster',
                'supplier',
                'unit',
                'batch',
                'request.unit',
            ]);

        if ($variantId) {
            $q->where('item_variant_id', $variantId);
        }

        if (in_array($type, ['MASUK','KELUAR'], true)) {
            $q->where('type', $type);
        }

        if ($requestId) {
            $q->where('request_id', $requestId);
        }

        if ($dateFrom) {
            $q->whereDate('trans_date', '>=', $dateFrom);
        }

        if ($dateTo) {
            $q->whereDate('trans_date', '<=', $dateTo);
        }

        $transactions = $q->orderByDesc('trans_date')
                          ->orderByDesc('id')
                          ->paginate(20)
                          ->withQueryString();

        $variants = ItemVariant::query()
            ->join('items_master','items_master.id','=','item_variants.item_master_id')
            ->selectRaw("
                item_variants.id,
                CONCAT(
                    items_master.item_code,' — ',items_master.item_name,' | ',
                    item_variants.brand,' | ',
                    COALESCE(NULLIF(item_variants.lot_number,''),'—')
                ) AS label
            ")
            ->orderBy('items_master.item_code')
            ->orderBy('item_variants.brand')
            ->pluck('label','id');

        $suppliers = Supplier::orderBy('supplier_name')
            ->pluck('supplier_name','id');

        $units = Unit::orderBy('unit_name')
            ->pluck('unit_name','id');

        $sumMasuk  = (clone $q)->where('type','MASUK')->sum('quantity');
        $sumKeluar = (clone $q)->where('type','KELUAR')->sum('quantity');

        return view('transactions.index', compact(
            'transactions',
            'variants',
            'variantId',
            'type',
            'dateFrom',
            'dateTo',
            'sumMasuk',
            'sumKeluar',
            'suppliers',
            'units',
            'requestId'
        ));
    }

    public function create()
    {
        $variants = ItemVariant::query()
            ->join('items_master','items_master.id','=','item_variants.item_master_id')
            ->select(
                'item_variants.id',
                'items_master.item_name',
                'items_master.base_unit',
                'item_variants.brand',
                'item_variants.lot_number',
                'item_variants.expiration_date'
            )
            ->orderBy('items_master.item_code')
            ->orderBy('item_variants.brand')
            ->get();

        $batches = ItemBatch::orderBy('item_variant_id')
            ->orderBy('expiration_date')
            ->orderBy('lot_number')
            ->get();

        $suppliers = Supplier::orderBy('supplier_name')
            ->pluck('supplier_name','id');

        $units = Unit::orderBy('unit_name')
            ->pluck('unit_name','id');

        return view('transactions.create', compact('variants','suppliers','units','batches'));
    }

    public function edit(Transaction $transaction)
    {
        $variants = ItemVariant::query()
            ->join('items_master','items_master.id','=','item_variants.item_master_id')
            ->select(
                'item_variants.id',
                'items_master.item_name',
                'items_master.base_unit',
                'item_variants.brand',
                'item_variants.lot_number',
                'item_variants.expiration_date'
            )
            ->orderBy('items_master.item_code')
            ->orderBy('item_variants.brand')
            ->get();

        $batches = ItemBatch::where('item_variant_id', $transaction->item_variant_id)
            ->orderBy('expiration_date')
            ->orderBy('lot_number')
            ->get();

        $suppliers = Supplier::orderBy('supplier_name')
            ->pluck('supplier_name','id');

        $units = Unit::orderBy('unit_name')
            ->pluck('unit_name','id');

        return view('transactions.edit', compact('transaction','variants','suppliers','units','batches'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'trans_date'        => 'required|date',
            'doc_no'            => 'nullable|string|max:100',
            'type'              => 'required|in:MASUK,KELUAR',
            'item_variant_id'   => 'required|exists:item_variants,id',
            'quantity'          => 'required|integer|min:1',
            'note'              => 'nullable|string|max:255',
            'brand'             => 'nullable|string|max:100',
            'supplier_id'       => 'nullable|exists:suppliers,id',
            'invoice_no'        => 'nullable|string|max:100',
            'price'             => 'nullable|numeric|min:0',
            'tax_amount'        => 'nullable|numeric|min:0',
            'total_amount'      => 'nullable|numeric|min:0',
            'lot_number'        => 'nullable|string|max:100',
            'expiration_date'   => 'nullable|date',
            'payment_status'    => 'nullable|in:LUNAS,HUTANG',
            'storage_condition' => 'nullable|string|max:255',
            'package_size'      => 'nullable|string|max:255',
            'unit_id'           => 'nullable|required_if:type,KELUAR|exists:units,id',
            'request_id'        => 'nullable|exists:requests,id',
        ]);

        $variant = ItemVariant::find($data['item_variant_id']);

        if (empty($data['brand']) && $variant) {
            $data['brand'] = $variant->brand;
        }

        if (empty($data['lot_number']) && $variant) {
            $data['lot_number'] = $variant->lot_number;
        }

        if (empty($data['expiration_date']) && $variant && $variant->expiration_date) {
            $data['expiration_date'] = $variant->expiration_date;
        }

        if ($data['type'] === 'KELUAR') {
            $current = (int) StockCurrent::where('item_variant_id', $data['item_variant_id'])
                ->value('current_quantity');

            if ($current < (int) $data['quantity']) {
                throw ValidationException::withMessages([
                    'quantity' => 'Stok tidak mencukupi untuk transaksi barang keluar.',
                ]);
            }
        }

        $batchId = null;

        if ($variant) {
            $lot = $data['lot_number'] ?? $variant->lot_number;
            $exp = $data['expiration_date'] ?? $variant->expiration_date;

            if ($lot || $exp) {
                $batch = ItemBatch::firstOrCreate([
                    'item_variant_id' => $variant->id,
                    'lot_number'      => $lot,
                    'expiration_date' => $exp,
                ]);

                $batchId = $batch->id;
            }
        }

        $data['batch_id'] = $batchId;
        $tx = Transaction::create($data);
        $this->recomputeStock($tx->item_variant_id);

        return redirect()
            ->route('transactions.index')
            ->with('success','Transaksi berhasil disimpan dan stok diperbarui.');
    }

    public function update(Request $request, Transaction $transaction)
    {
        $data = $request->validate([
            'trans_date'        => 'required|date',
            'doc_no'            => 'nullable|string|max:100',
            'quantity'          => 'required|integer|min:1',
            'note'              => 'nullable|string|max:255',
            'brand'             => 'nullable|string|max:100',
            'supplier_id'       => 'nullable|exists:suppliers,id',
            'invoice_no'        => 'nullable|string|max:100',
            'price'             => 'nullable|numeric|min:0',
            'tax_amount'        => 'nullable|numeric|min:0',
            'total_amount'      => 'nullable|numeric|min:0',
            'lot_number'        => 'nullable|string|max:100',
            'expiration_date'   => 'nullable|date',
            'payment_status'    => 'nullable|in:LUNAS,HUTANG',
            'storage_condition' => 'nullable|string|max:255',
            'package_size'      => 'nullable|string|max:255',
            'type'              => 'nullable|in:MASUK,KELUAR',
            'unit_id'           => 'nullable|required_if:type,KELUAR|exists:units,id',
            'request_id'        => 'nullable|exists:requests,id',
        ]);

        $data['type'] = $transaction->type;
        if ($transaction->type === 'KELUAR') {
            $errors = [];
            $incomingQty = (int) $request->input('quantity');
            $incomingUnit = $request->input('unit_id');
            $incomingLot = $request->input('lot_number');
            $incomingExp = $request->input('expiration_date');
            $txQty = (int) $transaction->quantity;
            $txUnit = $transaction->unit_id === null ? null : (string) $transaction->unit_id;
            $txLot = (string) ($transaction->lot_number ?? '');

            $txExpRaw = $transaction->expiration_date;
            if (is_object($txExpRaw) && method_exists($txExpRaw, 'format')) {
                $txExp = $txExpRaw->format('Y-m-d');
            } else {
                $txExp = $txExpRaw ?: null; 
            }

            $incomingUnitStr = $incomingUnit === null ? null : (string) $incomingUnit;
            $incomingLotStr = (string) ($incomingLot ?? '');
            $incomingExpStr = $incomingExp ?: null;

            if ($incomingQty !== $txQty) {
                $errors['quantity'] = 'Jumlah terkunci untuk transaksi KELUAR. Koreksi gunakan transaksi pembalik + transaksi baru.';
            }
            if ($incomingUnitStr !== $txUnit) {
                $errors['unit_id'] = 'Unit tujuan terkunci untuk transaksi KELUAR. Koreksi gunakan transaksi pembalik + transaksi baru.';
            }
            if ($incomingLotStr !== $txLot) {
                $errors['lot_number'] = 'Nomor LOT terkunci untuk transaksi KELUAR. Koreksi gunakan transaksi pembalik + transaksi baru.';
            }
            if ($incomingExpStr !== $txExp) {
                $errors['expiration_date'] = 'Tanggal kadaluarsa terkunci untuk transaksi KELUAR. Koreksi gunakan transaksi pembalik + transaksi baru.';
            }

            if (!is_null($transaction->request_id)) {
                $incomingReq = $request->input('request_id');
                $txReq = (string) $transaction->request_id;
                $incomingReqStr = $incomingReq === null ? null : (string) $incomingReq;

                if ($incomingReqStr !== $txReq) {
                    $errors['request_id'] = 'ID Permintaan terkunci karena transaksi sudah terkait permintaan.';
                }
            }

            if (!empty($errors)) {
                throw ValidationException::withMessages($errors);
            }

            $data['quantity'] = (int) $transaction->quantity;
            $data['unit_id'] = $transaction->unit_id;
            $data['lot_number'] = $transaction->lot_number;
            $data['expiration_date'] = $transaction->expiration_date;

            if (!is_null($transaction->request_id)) {
                $data['request_id'] = $transaction->request_id;
            }
        }

        if ($transaction->type === 'KELUAR') {
            $oldQty = (int) $transaction->quantity;
            $newQty = (int) $data['quantity'];

            if ($newQty !== $oldQty) {
                $current = (int) StockCurrent::where('item_variant_id', $transaction->item_variant_id)
                    ->value('current_quantity');

                $stockWithoutThis = $current + $oldQty;

                if ($stockWithoutThis < $newQty) {
                    throw ValidationException::withMessages([
                        'quantity' => 'Stok tidak mencukupi untuk perubahan jumlah transaksi keluar.',
                    ]);
                }
            }
        }

        $variant = ItemVariant::find($transaction->item_variant_id);

        if (empty($data['brand']) && $variant) {
            $data['brand'] = $variant->brand;
        }
        if (empty($data['lot_number']) && $variant) {
            $data['lot_number'] = $variant->lot_number;
        }
        if (empty($data['expiration_date']) && $variant && $variant->expiration_date) {
            $data['expiration_date'] = $variant->expiration_date;
        }

        $batchId = null;
        if ($variant) {
            $lot = $data['lot_number'] ?? $variant->lot_number;
            $exp = $data['expiration_date'] ?? $variant->expiration_date;

            if ($lot || $exp) {
                $batch = ItemBatch::firstOrCreate([
                    'item_variant_id' => $variant->id,
                    'lot_number'      => $lot,
                    'expiration_date' => $exp,
                ]);
                $batchId = $batch->id;
            }
        }

        $data['batch_id'] = $batchId;

        $transaction->update($data);
        $this->recomputeStock($transaction->item_variant_id);

        return redirect()
            ->route('transactions.index')
            ->with('success','Transaksi diperbarui dan stok disinkronkan.');
    }

    public function destroy(Transaction $transaction)
    {
        if ($transaction->request_id) {
            return back()->with('error', 'Transaksi yang sudah terkait permintaan tidak dapat dihapus.');
        }

        $variantId = $transaction->item_variant_id;

        $transaction->delete();
        $this->recomputeStock($variantId);

        return back()->with('success','Transaksi dihapus dan stok disinkronkan.');
    }

    public function show(Transaction $transaction)
    {
        $transaction->load([
            'variant.itemMaster',
            'supplier',
            'unit',
            'request.unit',
            'batch'
        ]);

        return view('transactions.show', compact('transaction'));
    }

    protected function recomputeStock(int $variantId): void
    {
        $in  = (int) Transaction::where('item_variant_id',$variantId)
                                ->where('type','MASUK')
                                ->sum('quantity');

        $out = (int) Transaction::where('item_variant_id',$variantId)
                                ->where('type','KELUAR')
                                ->sum('quantity');

        $cur = max(0, $in - $out);

        StockCurrent::updateOrCreate(
            ['item_variant_id' => $variantId],
            ['current_quantity' => $cur]
        );
    }
}
