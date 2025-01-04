<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Models\BPL;
use App\Models\Item;
use App\Models\ItemReceived;
use App\Models\Order;
use App\Models\OrderBackupScan;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\File;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->input('search');
        $alert = $request->input('alert');
        $orderQuery = Order::whereNull('deleted_at')
            ->with(['partner' => function ($query) {
                $query->select(['id', 'name']);
            }]);

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
        $orders = $orderQuery->paginate(15);

        return view('pages.order.index', [
            'orders' => $orders
        ]);
    }

    public function uploadDocument(Request $request, int $orderId)
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

    function deleteBackupScanDoc(int $id)
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
            ->with(['partner', 'bpl.items' => function ($query) {
                $query->where('is_selected', true)
                    ->whereNull('deleted_at');
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

        $order = Order::create([
            'po_number' => $validatedData['po_number'],
            'partner_id' => $validatedData['partner_id'],
            'description' => $validatedData['description'],
            'po_date' => $validatedData['po_date'],
            'start_date' => $validatedData['start_date'],
            'finish_date' => $validatedData['finish_date'],
        ]);

        $this->bulkUpdateBPL($validatedData['bpl'], $order->id, $order->partner_id);

        notify()->success('Data order telah ditambahkan', 'Berhasil');
        return redirect('/order');
    }

    public function destroy($id)
    {
        $order = Order::find($id);
        if (!$order) {
            notify()->error('Order yang anda hapus tidak ditemukan', 'Gagal');
            return redirect()->back();
        }

        BPL::where('order_id', $id)->delete();
        $order->delete();

        notify()->success('Order telah dihapus', 'Berhasil');
        return redirect()->back();
    }

    public function updateItem(Request $request, int $orderId, int $itemId): RedirectResponse
    {
        try {
            $validator = Validator::make($request->input(), [
                'item_name' => 'required|min:2',
                'price' => 'required',
                'volume' => 'required',
            ], [
                'item_name.required' => 'Nama item harus diisi',
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

            Item::where('id', $itemId)->update([
                'item_name' => $request->input('item_name'),
                'price' => (int)$request->input('price'),
                'volume' => floatval($request->input('volume')),
            ]);

            notify()->success('Data item berhasil diperbarui', 'Berhasil');
            return redirect()->back();
        } catch (ValidationException $e) {
            notify()->error($e->getMessage(), 'Gagal');
            return redirect()->back();
        }
    }


    // public function addItem(Request $request, int $id)
    // {
    //     try {
    //         $validator = Validator::make($request->input(), [
    //             'bpl_number' => 'required',
    //             'item_name' => 'required|min:4',
    //             'brand' => 'nullable',
    //             'unit' => 'nullable',
    //             'specification' => 'nullable',
    //             'volume' => 'required',
    //             'price' => 'required',
    //             'is_selected' => 'required',
    //         ], [
    //             'bpl_number.required' => 'Nomor BPL harus diisi',
    //             'item_name.required' => 'Nama item harus diisi',
    //             'item_name.min' => 'Nama item minimal 4 karakter',
    //             'volume.required' => 'Volume harus diisi',
    //             'price.required' => 'Volume harus diisi',
    //         ]);

    //         if ($validator->fails()) {
    //             $error = $validator->errors()->first();
    //             throw ValidationException::withMessages([
    //                 'message' => $error,
    //             ]);
    //         }

    //         $bpl = BPL::where('bpl_number', $request->input('bpl_number'))->first();
    //         if (!$bpl) {
    //             notify()->error('BPL tidak ditemukan', 'Gagal');
    //             return redirect()->back();
    //         }

    //         Item::create([
    //             'bpl_number' => $request->input('bpl_number'),
    //             'item_name' => $request->input('item_name'),
    //             'unit' => $request->input('unit'),
    //             'brand' => $request->input('brand'),
    //             'price' => $request->input('price'),
    //             'specification' => $request->input('specification'),
    //             'volume' => $request->input('volume'),
    //             'is_selected' => $request->has('is_selected') ? 1 : 0,
    //         ]);

    //         notify()->success('Berhasil manambahkan item', 'Berhasil');
    //         return redirect()->back();
    //     } catch (ValidationException $e) {
    //         notify()->error($e->getMessage(), 'Gagal');
    //         return redirect()->back();
    //     }
    // }

    public function addBPLForm(int $id): View | RedirectResponse
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

    public function addBPL(Request $request, int $id)
    {
        try {
            $data = $this->addBPLValidation($request);
            $this->bulkUpdateBPL($data['bpl'], $id, $data['partner_id']);

            notify()->success('Berhasil manambahkan BPL', 'Berhasil');
            return redirect("/order/$id");
        } catch (ValidationException $e) {
            notify()->error($e->getMessage(), 'Gagal');
            return redirect()->back();
        }
    }

    private function addBPLValidation(Request $request)
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
        $item = Item::find($itemId);

        if (!$item) {
            notify()->error('Item yang anda hapus tidak ditemukan', 'Gagal');
            return redirect()->back();
        }
        $item->is_selected = 0;
        $item->save();
        $item->delete();

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
