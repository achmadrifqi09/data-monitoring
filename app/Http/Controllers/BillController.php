<?php

namespace App\Http\Controllers;

use App\Http\Requests\BillRequest;
use App\Models\Bill;
use App\Models\BillDocument;
use App\Models\BillItem;
use App\Models\ItemReceived;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Illuminate\Validation\Rules\File;
use Illuminate\Support\Str;

class BillController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->input('search');
        $orderQuery = Order::whereNull('deleted_at');

        if ($search) {
            $orderQuery->where(function ($query) use ($search) {
                $query->where('po_number', 'like', '%' . $search . '%')
                    ->orWhereHas('bill', function ($query) use ($search) {
                        $query->where('bap', 'like', '%' . $search . '%');
                    });
            });
        }
        $orders = $orderQuery->with(['partner', 'bills'])
            ->whereHas('bills')
            ->paginate(15);

        return view('pages.bill.index', [
            'orders' => $orders ?? []
        ]);
    }

    public function create(): View
    {
        return view('pages.bill.form');
    }

    public function show(int $id)
    {
        $bill = Bill::where('id', $id)
            ->with([
                'order.partner',
                'bill_items',
                'documents'
            ])->first();

        return view('pages.bill.detail', [
            'bill' => $bill
        ]);
    }

    public function uploadDocument(Request $request, int $id): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'bill_document' => [
                File::types(['pdf'])
                    ->max(10 * 1024),
            ],
        ], [
            'bill_document.required' => 'Dokumen tagihan harus diisi',
            'bill_document.mimes' => 'Dokumen tagihan berformat PDF',
            'bill_document.max' => 'Dokumen tagihan maksimal 1 MB',
        ]);
        $billDocument = $request->file('bill_document');

        $bill = Bill::find($id);
        if (!$bill) {
            notify()->error('Data tagihan tidak ditemukan', 'Gagal');
            return redirect()->back();
        }

        if ($validator->fails()) {
            $error = $validator->errors()->first();
            notify()->error($error, 'Gagal');
            return redirect()->back();
        }

        $fileName = 'bill_' . $id . '_' . time() . '_' . Str::random(10) . '.' . $billDocument->getClientOriginalExtension();

        $directory = '/bill/documents';
        $path = Storage::putFileAs($directory, $billDocument, $fileName);

        BillDocument::create([
            'bill_id' => $bill->id,
            'document' => $path
        ]);

        notify()->success('Berhasil mengunggah dokumen', 'Berhasil');
        return redirect()->back();
    }

    public function destroy(int $id)
    {
        $bill = Bill::find($id);
        if (!$bill) {
            notify()->error('Tagihan tidak ditemukan', 'Gagal');
            return redirect()->back();
        }

        $billDocuments = BillDocument::where('bill_id', $id)->get();
        foreach ($billDocuments as $billDocument) {
            if (Storage::exists($billDocument->document)) Storage::delete($billDocument->document);
        }
        $bill->delete();

        notify()->success('Berhasil meghapus tagihan', 'Berhasil');
        return redirect()->back();
    }

    function destroyDocument(int $id): RedirectResponse
    {
        $billDocument = BillDocument::find($id);

        if (!$billDocument) {
            notify()->error('File tidak ditemukan', 'Gagal');
            return redirect()->back();
        }

        if (Storage::exists($billDocument->document)) {
            Storage::delete($billDocument->document);
        }

        $billDocument->delete();
        notify()->success('Berhasil menghapus dokumen', 'Berhasil');
        return redirect()->back();
    }

    public function store(BillRequest $request): RedirectResponse
    {
        $validatedData = $request->validated();

        $receivedTotalPrice = DB::table('item_receiveds')
            ->where('order_id', $validatedData['order_id'])
            ->sum(DB::raw('CAST(nominal AS DECIMAL(10,2))'));
        $order = Order::find($validatedData['order_id']);

        if (!$order) {
            notify()->error('Data order tidak ditemukan', 'Gagal');
            return redirect()->back();
        }
        if (empty($validatedData['bill_items'])) {
            notify()->error('Setidaknya harus ada 1 item tagihan', 'Gagal');
            return redirect()->back();
        }
        $checkedResult = $this->checkCapcityBillItem($validatedData['order_id'], $validatedData['bill_items']);
        if (!$checkedResult) {
            notify()
                ->error(
                    "Item dari tagihan melebihi jumlah item yang diterima",
                    'Gagal'
                );
            return redirect()->back();
        }
        if (floatval($validatedData['raw_bill_total']) > floatval($receivedTotalPrice)) {
            notify()
                ->error(
                    "Nominal yang ditagihkan harus kurang atau sama dengan $$receivedTotalPrice",
                    'Gagal'
                );
            return redirect()->back();
        }

        $DPP = floatval($validatedData['raw_bill_total']) -
            floatval($validatedData['fee_deduction']) -
            floatval($validatedData['retention']);
        $ppn = $DPP * (floatval($validatedData['ppn']) / 100);
        $pph = $DPP * (floatval($validatedData['pph']) / 100);
        $netto = $DPP + $ppn - $pph;

        $bill = Bill::create([
            'order_id' => $validatedData['order_id'],
            'netto' => $netto,
            'bap' => $validatedData['bap'],
            'bill_total' => $validatedData['raw_bill_total'],
            'date_of_bap' => $validatedData['date_of_bap'],
            'dpp' => $DPP,
            'fee_deduction' => $validatedData['fee_deduction'],
            'retention' => $validatedData['retention'],
            'ppn_percentage' => $validatedData['ppn'],
            'ppn' => $ppn,
            'pph_percentage' => $validatedData['pph'],
            'pph' => $pph,
            'receipt_date' => $validatedData['receipt_date'],
            'due_date' => $validatedData['due_date'],
        ]);
        $this->bulkCreateBillItem($validatedData['bill_items'], $bill->id, (int)$validatedData['order_id']);
        notify()->success('Tagihan berhasil ditambahkan', 'Berhasil');
        return redirect('/bill');
    }

    public function update(Request $request, int $id)
    {
        $validator = Validator::make($request->except('_token', '_method'), [
            'bap' => 'required',
            'date_of_bap' => 'required|date',
            'fee_deduction' => 'required|numeric',
            'retention' => 'required|numeric',
            'ppn' => 'required|numeric',
            'pph' => 'required|numeric',
            'receipt_date' => 'required|date',
            'due_date' => 'required|numeric',
        ], [
            'bap.required' => 'BAP harus diisi',
            'date_of_bap.required' => 'Tanggal BAP harus diisi',
            'date_of_bap.date' => 'Tanggal BAP harus berformat tanggal',
            'fee_deduction.required' => 'Potongan harus diisin',
            'fee_deduction.numeric' => 'Potongan harus berupa angka',
            'retention.required' => 'Retensi harus diisi',
            'retention.numeric' => 'Retensi harus berupa angka',
            'ppn.required' => 'PPN harus diisi',
            'ppn.numeric' => 'PPN harus berupa angka',
            'pph.required' => 'PPH harus diisi',
            'pph.numeric' => 'PPH harus berupa angka',
            'receipt_date.required' => 'Tanggal kuitansi harus diisi',
            'receipt_date.date' => 'Tanggal kuitansi harus berupa tanggal',
            'due_date.required' => 'Jatuh tempo harus diisi',
            'due_date.numeric' => 'Jatuh tempo harus berupa angka',
        ]);

        if ($validator->fails()) {
            $error = $validator->errors()->first();
            notify()->error($error, 'Gagal');
            return redirect()->back();
        }

        $bill = Bill::find($id);
        if (!$bill) {
            notify()->error('Data tagihan tidak ditemukan', 'Gagal');
            return redirect()->back();
        }

        $DPP = $bill->bill_total -
            floatval($request->input('fee_deduction'))
            - floatval($request->input('retention'));
        $PPN = $DPP * (floatval($request->input('ppn')) / 100);
        $PPH = $DPP * (floatval($request->input('pph')) / 100);
        $netto = $DPP + $PPN - $PPH;

        $bill->update([
            'netto' => $netto,
            'bap' => $request->input('bap'),
            'date_of_bap' => $request->input('date_of_bap'),
            'dpp' => $DPP,
            'fee_deduction' => $request->input('fee_deduction'),
            'retention' => $request->input('retention'),
            'ppn_percentage' => $request->input('ppn'),
            'ppn' => $PPN,
            'pph_percentage' => $request->input('pph'),
            'pph' => $PPH,
            'receipt_date' => $request->input('receipt_date'),
            'due_date' => $request->input('due_date'),
        ]);
        notify()->success('Berhasil memperbarui tagihan', 'Berhasil');
        return redirect()->back();
    }

    public function updateBillItem(Request $request, int $bill_id, int $bill_item_id)
    {

        $validator = Validator::make($request->except('_token', '_method'), [
            'total_item_billed' => 'required|numeric',
            'price' => 'required',
        ], [
            'total_item_billed.required' => 'Jumlah item yang ditagih harus diisi',
            'total_item_billed.numeric' => 'Jumlah item yang ditagih harus berupa angka',
            'price.required' => 'Item yand dipilih tidak valid',
        ]);

        if ($validator->fails()) {
            $error = $validator->errors()->first();
            notify()->error($error, 'Gagal');
            return redirect()->back();
        }
        $bill = Bill::find($bill_id);
        $billItem = BillItem::where('item_id', $bill_item_id)->first();

        if (!$bill || !$billItem) {
            $error = $validator->errors()->first();
            notify()->error('Data Tagihan tidak ditemukan', 'Gagal');
            return redirect()->back();
        }

        $totalItemBilled = floatval($request->input('total_item_billed'));
        $isAvailable = $this->checkAmountReceived(
            $billItem->item_id,
            $bill->order_id,
            $totalItemBilled
        );

        if (!$isAvailable) {
            notify()->error('Jumlah item yang ditagih melebihi jumlah item yang diterima', 'Gagal');
            return redirect()->back();
        }

        $itemPriceBeforUpdate = floatval($bill->bill_total) - (
            floatval($billItem->total_item_billed) * floatval($request->input('price'))
        );

        $nominalUpdatedBill = $itemPriceBeforUpdate + (
            floatval($request->input('price')) * floatval($request->input('total_item_billed'))
        );

        $DPP = $nominalUpdatedBill -
            floatval($bill->fee_deduction)
            - floatval($bill->retention);

        $PPN = $DPP * (floatval($bill->ppn_percentage) / 100);
        $PPH = $DPP * (floatval($bill->pph_percentage) / 100);
        $netto = $DPP + $PPN - $PPH;

        $bill->update([
            'bill_total' => $nominalUpdatedBill,
            'netto' => $netto,
            'dpp' => $DPP,
            'ppn' => $PPN,
        ]);

        $billItem->update([
            'total_item_billed' => $totalItemBilled
        ]);
        notify()->success('Date item tagihan berhasil diupdate', 'Berhasil');
        return redirect()->back();
    }

    public function destroyBillItem(int $bill_id, int $bill_item_id)
    {
        $billItem = BillItem::find($bill_item_id);
        $bill = Bill::find($bill_id);

        if (!$billItem || !$bill) {
            notify()->error('Data item tagihan tidak ditemukan', 'Gagal');
            return redirect()->back();
        }


        $deletedData = DB::table('bill_items')
            ->where('bill_items.id', $bill_item_id)
            ->leftJoin('order_items', 'order_items.item_id', '=', 'bill_items.item_id')
            ->first();


        $nominalDeleteBill = $bill->bill_total - (
            floatval($deletedData->total_item_billed) * floatval($deletedData->price)
        );
        $DPP = $nominalDeleteBill -
            floatval($bill->fee_deduction)
            - floatval($bill->retention);
        $PPN = $DPP * (floatval($bill->ppn_percentage) / 100);
        $PPH = $DPP * (floatval($bill->pph_percentage) / 100);
        $netto = $DPP + $PPN - $PPH;

        $bill->update([
            'bill_total' => $nominalDeleteBill,
            'netto' => $netto,
            'dpp' => $DPP,
            'ppn' => $PPN,
        ]);

        $billItem->delete();

        notify()->success('Data item tagihan telah dihapus', 'Berhasil');
        return redirect()->back();
    }

    public function bulkCreateBillItem(array $billItems, int $billId, int $orderId): void
    {
        $payload = [];
        foreach ($billItems as $billItem) {
            if ($billItem['total_item_billed']) {
                $payload[] = [
                    'order_id' => $orderId,
                    'bill_id' => $billId,
                    'item_id' => $billItem['item_id'],
                    'total_item_billed' => $billItem['total_item_billed'],
                ];
            }
        }

        BillItem::insert($payload);
    }

    private function checkAmountReceived(int $itemId, int $orderId, float $filledTotalItem): Bool
    {
        $amountReceived = ItemReceived::where('item_id', $itemId)
            ->where('order_id', $orderId)
            ->sum('amount_received');

        if ($filledTotalItem > floatval($amountReceived)) {
            return false;
        }

        return true;
    }

    private function checkCapcityBillItem(int $orderId, array $billedItems): Bool
    {
        $billItems = DB::table('bill_items')
            ->where('bill_items.order_id', $orderId)
            ->leftJoin('item_receiveds', 'item_receiveds.item_id', '=', 'bill_items.id')
            ->select(
                'bill_items.item_id',
                DB::raw('SUM(item_receiveds.amount_received) as total_amount_received'),
                DB::raw('SUM(bill_items.total_item_billed) as total_item_billed')
            )
            ->groupBy(
                'bill_items.item_id',
            )->get();

        foreach ($billedItems as $billedItem) {
            $toBeBilled = floatval($billedItem['total_item_billed']);
            foreach ($billItems as $billItem) {
                if ((int)$billedItem['item_id'] === $billItem->item_id) $toBeBilled += floatval($billItem->total_item_billed);
                if ($toBeBilled > $billItem->total_amount_received) return false;
            }
        }
        return true;
    }

    public function getBill(Request $request): mixed
    {
        $search = $request->input('search');
        $billQuery = Bill::whereHas('bill_items')->doesntHave('payment');

        if ($search) {
            $billQuery->where('bap', 'like', '%' . $search . '%')
                ->orWhereHas('orders', function ($query) use ($search) {
                    $query->where('po_number', 'like', '%' . $search . '%');
                });
        }

        return $billQuery
            ->get()
            ->take(15);
    }
}
