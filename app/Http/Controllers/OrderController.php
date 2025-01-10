<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Models\BPL;
use App\Models\ItemReceived;
use App\Models\Order;
use App\Models\OrderBackupScan;
use App\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\File;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->input('search');
        $alert = $request->input('alert');
        $orderQuery = Order::whereNull('deleted_at');

        if ($search) {
            $orderQuery->where(function ($query) use ($search) {
                $query->where('po_number', 'like', '%' . $search . '%')
                    ->orWhereHas('partner', function ($query) use ($search) {
                        $query->where('name', 'like', '%' . $search . '%');
                    });
            });
        }

        if ($alert && $alert != 'all') {
            $orderQuery->where(function ($query) use ($alert) {
                $currentDate = Carbon::now();
                $sevenDaysFromNow = Carbon::now()->addDays(7);

                if ($alert == 'close') {
                    $query->whereDate('finish_date', '<', $currentDate);
                } elseif ($alert == 'ltw') {
                    $query->whereDate('finish_date', '>=', $currentDate)
                        ->whereDate('finish_date', '<=', $sevenDaysFromNow);
                } elseif ($alert == 'mtw') {
                    $query->whereDate('finish_date', '>', $sevenDaysFromNow);
                }
            });
        }
        $orders = $orderQuery->with(['partner' => function ($query) {
            $query->select('id', 'name');
        }])->paginate(15);

        return view('pages.order.index', [
            'orders' => $orders
        ]);
    }

    public function getOrder(Request $request)
    {
        $search = $request->input('search');
        $orderQuery = Order::whereNull('deleted_at')
            ->whereHas('item_receiveds');

        if ($search) {
            $orderQuery->where('po_number', 'like', '%' . $search . '%')
                ->orWhereHas('partner', function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%');
                });
        }

        return $orderQuery
            ->with('partner')
            ->get()
            ->take(15);
    }

    public function uploadDocument(Request $request, int $orderId): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'order_document' => [
                File::types(['pdf'])
                    ->max(10 * 1024),
            ],
        ], [
            'order_document.required' => 'Dokumen order harus diisi',
            'order_document.mimes' => 'Dokumen order berformat PDF',
            'order_document.max' => 'Dokumen order maksimal 1 MB',
        ]);
        $orderDocument = $request->file('order_document');

        $order = Order::find($orderId);
        if (!$order) {
            notify()->error('Data order tidak ditemukan', 'Gagal');
            return redirect()->back();
        }

        if ($validator->fails()) {
            $error = $validator->errors()->first();
            notify()->error($error, 'Gagal');
            return redirect()->back();
        }

        $fileName = 'order_' . $orderId . '_' . time() . '_' . Str::random(10) . '.' . $orderDocument->getClientOriginalExtension();

        $directory = '/order/documents';
        $path = Storage::putFileAs($directory, $orderDocument, $fileName);

        OrderBackupScan::create([
            'order_id' => $order->id,
            'document' => $path
        ]);

        notify()->success('Berhasil mengunggah dokumen', 'Berhasil');
        return redirect()->back();
    }

    function deleteBackupScanDoc(int $id): RedirectResponse
    {
        $backupScan = OrderBackupScan::find($id);

        if (!$backupScan) {
            notify()->error('File tidak ditemukan', 'Gagal');
            return redirect()->back();
        }

        if (Storage::exists($backupScan->document)) {
            Storage::delete($backupScan->document);
        }

        $backupScan->delete();
        notify()->success('Berhasil menghapus dokumen', 'Berhasil');
        return redirect()->back();
    }

    public function show(int $id): View
    {
        $order = Order::where('id', $id)
            ->with(['order_items' => function ($query) {
                $query->with('item');
            }, 'order_backup_scans'])
            ->first();
        $itemReceived = ItemReceived::where('order_id', $id)->with('item')->orderBy('date_received', 'desc')->get();
        return view('pages.order.detail', [
            'order' => $order,
            'itemReceiveds' => $itemReceived,
        ]);
    }

    public function add(): View
    {
        return view('pages.order.create-form');
    }

    public function store(OrderRequest $request): RedirectResponse
    {
        $validatedData = $request->validated();

        $partnerId = $validatedData['partner_id'];
        $order = Order::create([
            'po_number' => $validatedData['po_number'],
            'description' => $validatedData['description'],
            'partner_id' => $partnerId,
            'po_date' => $validatedData['po_date'],
            'start_date' => $validatedData['start_date'],
            'finish_date' => $validatedData['finish_date'],
        ]);

        $this->bulkCreateOrderItem($validatedData['bpl'], $partnerId, $order->id);

        notify()->success('Data order telah ditambahkan', 'Berhasil');
        return redirect('/order');
    }


    private function bulkCreateOrderItem(array $BPLs, int $partnerId, int $oderId): void
    {
        if (empty($BPLs)) {
            return;
        }
        $finalPayload = [];

        foreach ($BPLs as $BPL) {
            foreach ($BPL['items'] as $item) {
                if ($item['volume']) {
                    $finalPayload[] = [
                        'order_id' => $oderId,
                        'item_id' => $item['id'],
                        'bpl_number' => $BPL['bpl_number'],
                        'partner_id' => $partnerId,
                        'volume' => doubleval($item['volume']),
                        'price' => $item['price'],
                    ];
                }
            }
        }
        OrderItem::insert($finalPayload);
    }

    public function destroy($id): RedirectResponse
    {
        $order = Order::find($id);
        if (!$order) {
            notify()->error('Order yang anda hapus tidak ditemukan', 'Gagal');
            return redirect()->back();
        }

        $documents = OrderBackupScan::where('order_id', $id)->get();
        if ($documents) {
            foreach ($documents as $document) {
                if (Storage::exists($document->document)) {
                    Storage::delete($document->document);
                }
                $document->delete();
            }
        }

        OrderItem::where('order_id', $id)->delete();
        ItemReceived::where('order_id', $order->id)->delete();

        $order->delete();

        notify()->success('Order telah dihapus', 'Berhasil');
        return redirect()->back();
    }

    public function updateItem(Request $request, int $orderId, int $itemId): RedirectResponse
    {
        try {
            $validator = Validator::make($request->input(), [
                'price' => 'required',
                'volume' => 'required',
            ], [
                'item_name.min' => 'Nama item minimal 2 karakter',
                'price.required' => 'Harga item harus diisi',
                'volume.required' => 'Volume item harus diisi',
            ]);

            if ($validator->fails()) {
                $error = $validator->errors()->first();
                throw ValidationException::withMessages([
                    'message' => $error,
                ]);
            }

            $orderItem = OrderItem::where('item_id', $itemId)->update([
                'price' => (int)$request->input('price'),
                'volume' => floatval($request->input('volume')),
            ]);

            if (empty($orderItem)) {
                notify()->error('Data order item tidak ditemukan', 'Gagal');
                return redirect()->back();
            }

            notify()->success('Data item berhasil diperbarui', 'Berhasil');
            return redirect()->back();
        } catch (ValidationException $e) {
            notify()->error($e->getMessage(), 'Gagal');
            return redirect()->back();
        }
    }

    public function addBPLForm(int $id): View|RedirectResponse
    {
        $order = Order::find($id);
        if (!$order) {
            notify()->error('Url tambah BPL tidak valid, id order tidak ditemukan', 'Terjadi Kesalahan');
            return redirect('/order');
        }

        return view('pages.order.add-bpl-form', [
            'order' => $order
        ]);
    }

    public function addBPL(Request $request, int $id): RedirectResponse
    {
        try {
            $data = $this->addBPLValidation($request);
            $order = Order::find($id);
            if (!$order) {
                notify()->error('Data order tidak ditemukan', 'Gagal');
                return redirect()->back();
            }
            $this->bulkCreateOrderItem($data['bpl'], $data['partner_id'], $id);

            notify()->success('Berhasil manambahkan BPL', 'Berhasil');
            return redirect("/order/$id");
        } catch (ValidationException $e) {
            notify()->error($e->getMessage(), 'Gagal');
            return redirect()->back();
        }
    }

    /**
     * @throws ValidationException
     */
    private function addBPLValidation(Request $request): array
    {
        $rules = [
            'partner_id' => 'required',
            'bpl' => 'required|array|min:1',
            'bpl.*.bpl_number' => 'required|min:1',
            'bpl.*.items' => 'required|array|min:1',
            'bpl.*.items.*.id' => 'required|string',
            'bpl.*.items.*.is_selected' => 'nullable',
            'bpl.*.items.*.volume' => 'nullable',
            'bpl.*.items.*.price' => 'nullable',
        ];
        $messages = [
            'bpl.required' => 'BPL harus diisi',
            'bpl.array' => 'BPL harus berupa array',
            'bpl.min' => 'BPL minimal memiliki 1 item',
            'bpl.*.bpl_number.required' => 'Nomor BPL harus diisi',
            'bpl.*.items.required' => 'Items BPL harus diisi',
            'bpl.*.items.array' => 'Items BPL harus berupa array',
            'bpl.*.items.min' => 'Items BPL minimal memiliki 1 item',
            'bpl.*.items.*.id.required' => 'ID item BPL harus diisi',
            'bpl.*.items.*.id.string' => 'ID item BPL harus berupa string',
        ];

        $validator = Validator::make($request->input(), $rules, $messages);

        if ($validator->fails()) {
            $error = $validator->errors()->first();
            throw ValidationException::withMessages([
                'message' => $error,
            ]);
        }

        return $request->except('_token');
    }


    public function destroyItem(int $orderId, int $itemId): RedirectResponse
    {
        $orderItem = OrderItem::where('item_id', $itemId)->where('order_id', $orderId)->first();

        if (!$orderItem) {
            notify()->error('Item yang anda hapus tidak ditemukan', 'Gagal');
            return redirect()->back();
        }

        $orderItem->delete();

        notify()->success('Berhasil menghapus item', 'Berhasil');
        return redirect()->back();
    }

    private function bulkUpdateBPL(array $BPLs, int $orderId, int $partnerId): void
    {
        if (empty($BPLs)) {
            return;
        }

        $cases = [];
        $volumeCases = [];
        $ids = [];
        $params = [];
        $bplNumbers = [];
        $volumeParams = [];

        foreach ($BPLs as $bpl) {
            foreach ($bpl['items'] as $item) {
                if (isset($item['is_selected'])) {
                    $id = (int)$item['id'];
                    $price = (int)$item['price'];
                    $volume = floatval($item['volume']);

                    $cases[] = "WHEN {$id} THEN ?";
                    $params[] = $price;

                    $volumeCases[] = "WHEN {$id} THEN ?";
                    $volumeParams[] = $volume;

                    $ids[] = $id;
                    if (!in_array($bpl['bpl_number'], $bplNumbers)) $bplNumbers[] = $bpl['bpl_number'];
                }
            }
        }

        if (empty($ids)) {
            return;
        }

        BPL::whereIn('bpl_number', $bplNumbers)->update([
            'order_id' => $orderId,
            'partner_id' => $partnerId,
        ]);

        DB::update("
            UPDATE items
            SET is_selected = ?,
                price = CASE id " . implode(' ', $cases) . " END,
                volume = CASE id " . implode(' ', $volumeCases) . " END
            WHERE id IN (" . implode(',', $ids) . ")
        ", array_merge([1], $params, $volumeParams));
    }
}
