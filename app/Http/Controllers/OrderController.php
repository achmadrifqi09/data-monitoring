<?php

namespace App\Http\Controllers;

use App\Models\BPL;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Support\Facades\Validator;
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
        $orders = $orderQuery->paginate(10);

        return view('pages.order.index', [
            'orders' => $orders
        ]);
    }

    public function show(int $id): View
    {
        $order = Order::where('id', $id)
            ->with('partner', 'items')
            ->first();
        return view('pages.order.detail', [
            'order' => $order
        ]);
    }

    public function add(): View
    {
        return view('pages.order.create-form');
    }

    public function store(Request $request): RedirectResponse
    {
        $validatedData = $this->validateData($request);

        $order = Order::create([
            'po_number' => $validatedData['po_number'],
            'partner_id' => $validatedData['partner_id'],
            'description' => $validatedData['description'],
            'po_date' => $validatedData['po_date'],
            'start_date' => $validatedData['start_date'],
            'finish_date' => $validatedData['finish_date'],
        ]);

        $this->bulkUpdateBPL($validatedData['items'], $order->id, $order->partner_id);

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
                'item_name' => 'required|min:4',
                'price' => 'required',
                'volume' => 'required',
            ], [
                'item_name.required' => 'Nama item harus diisi',
                'item_name.min' => 'Nama item minimal 4 karakter',
                'price.required' => 'Harga item harus diisi',
                'volume.required' => 'Volume item harus diisi',
            ]);

            if ($validator->fails()) {
                $error = $validator->errors()->first();
                throw ValidationException::withMessages([
                    'message' => $error,
                ]);
            }

            BPL::where('id', $itemId)->update([
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


    public function addItem(Request $request, int $id)
    {
        try {
            $validator = Validator::make($request->input(), [
                'item_id' => 'required',
                'volume' => 'required',
                'price' => 'required',
                'partner_id' => 'required',
            ], [
                'item_id.required' => 'Item harus diisi',
                'price.required' => 'Harga item harus diisi',
                'volume.required' => 'Volume item harus diisi',
                'partner_id.required' => 'Data rekanan tidak valid',
            ]);
            if ($validator->fails()) {
                $error = $validator->errors()->first();
                throw ValidationException::withMessages([
                    'message' => $error,
                ]);
            }

            $item = BPL::find((int)$request->input('item_id'));

            if (!$item) {
                notify()->error('Item tidak ditemukan', 'Gagal');
                return redirect()->back();
            }

            $item->order_id = (int)$id;
            $item->price = $request->input('price');
            $item->volume = $request->input('volume');
            $item->partner_id = $request->input('partner_id');
            $item->save();

            notify()->success('Berhasil manambahkan item', 'Berhasil');
            return redirect()->back();
        } catch (ValidationException $e) {
            notify()->error($e->getMessage(), 'Gagal');
            return redirect()->back();
        }
    }

    public function destroyItem(int $orderId, int $itemId)
    {
        $item = BPL::find((int)$itemId);

        if (!$item) {
            notify()->error('Item yang anda hapus tidak ditemukan', 'Gagal');
            return redirect()->back();
        }

        $item->order_id = null;
        $item->partner_id = null;
        $item->price = null;
        $item->volume = null;
        $item->save();

        notify()->success('Berhasil menghapus item', 'Berhasil');
        return redirect()->back();
    }

    private function bulkUpdateBPL(array $items, int $orderId, int $partnerId): void
    {
        if (empty($items)) {
            return;
        }

        $cases = [];
        $volumeCases = [];
        $ids = [];
        $params = [];
        $volumeParams = [];

        foreach ($items as $item) {
            $id = (int)$item['id'];
            $price = (int)$item['price'];
            $volume = floatval($item['volume']);

            $cases[] = "WHEN {$id} THEN ?";
            $params[] = $price;

            $volumeCases[] = "WHEN {$id} THEN ?";
            $volumeParams[] = $volume;

            $ids[] = $id;
        }

        if (empty($ids)) {
            return;
        }

        DB::update("
            UPDATE bpl 
            SET order_id = ?,
                partner_id = ?,
                price = CASE id " . implode(' ', $cases) . " END,
                volume = CASE id " . implode(' ', $volumeCases) . " END
            WHERE id IN (" . implode(',', $ids) . ")
            ", array_merge([$orderId, $partnerId], $params, $volumeParams));
    }


    private function validateData(Request $request)
    {
        $data = $request->validate([
            'po_number' => 'required|min:4',
            'partner_id' => 'required',
            'description' => 'nullable',
            'po_date' => 'required|date',
            'start_date' => 'required|date',
            'finish_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.id' => 'string|required',
            'items.*.volume' => 'string|required',
            'items.*.price' => 'string|required',
        ], [
            'po_number.required' => 'Nomor PO harus diisi',
            'po_number.min' => 'Nomor PO minimal 4 karakter',
            'partner_id.required' => 'Rekanan harus diisi',
            'po_date.required' => 'Tanggal PO harus diisi',
            'po_date.date' => 'Tanggal PO harus beruba tanggal',
            'start_date.required' => 'Tanggal start harus diisi',
            'start_date.date' => 'Tanggal start harus berupa tanggal',
            'finish_date.required' => 'Tanggal finis harus diisi',
            'finish_date.date' => 'Tanggal finis harus berupa tanggal',
            'items.required' => 'Item BPL harus diisi',
            'items.min' => 'Item BPL minimal 1',
            'items.*.id.required' => 'Item BPL harus diisi',
            'items.*.volume.required' => 'Item BPL harus diisi',
            'items.*.price.required' => 'Harga item BPL harus diisi'
        ]);
        return $data;
    }
}
