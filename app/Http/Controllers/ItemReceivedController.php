<?php

namespace App\Http\Controllers;

use App\Models\BPL;
use App\Models\Item;
use App\Models\ItemReceived;
use App\Models\Order;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ItemReceivedController extends Controller
{
    public function form(Request $request): View|RedirectResponse
    {
        $orderId = $request->query('order_id');
        if (!$orderId) {
            notify()->error('Url penerimaan barang tidak valid', 'Terjadi Kesalahan');
            return redirect()->back();
        }

        $order = Order::where('id', (int)$orderId)->select('id', 'po_number')->first();
        if (!$order) {
            notify()->error('Data order tidak ditemukan', 'Terjadi Kesalahan');
            return redirect()->back();
        }

        $BPLs = BPL::with(['items' => function ($query) use ($orderId) {
            $query->whereHas('order_item', function ($query) use ($orderId) {
                $query->where('order_id', $orderId);
            });
        }])
            ->get();

        return view('pages.order.item-received-form', [
            'BPLs' => $BPLs,
            'po_number' => $order->po_number,
            'order_id' => $order->id,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        try {
            $payload = [];
            $BPLs = $request->input('bpl');
            $orderId = $request->input('order_id');
            $itemIds = [];

            foreach ($BPLs as $inputItem) {
                foreach ($inputItem['items'] as $item) {
                    if ($item['amount_received'] && $item['amount_received'] != 0) {
                        $itemIds[] = (int)$item['item_id'];
                        $payload[] = [
                            'bpl_number' => $inputItem['bpl_number'],
                            'item_id' => (int)$item['item_id'],
                            'order_id' => (int)$orderId,
                            'amount_received' => doubleval($item['amount_received']),
                            'date_received' => $item['received_date'],
                            'nominal' => floatval($item['amount_received']) * intval($item['price'])
                        ];
                    }
                }
            }

            if (empty($itemIds)) {
                notify()->warning('Tidak ada item yang ditambahkan ke penerimaan barang', 'Peringatan');
                return redirect()->back();
            }

            $items = Item::whereIn('id', $itemIds)->with(['item_receiveds', 'order_item'])->get();

            foreach ($payload as $payloadItem) {
                foreach ($items as $item) {
                    if ($payloadItem['item_id'] === $item->id) {
                        $volumeUsed = 0;
                        foreach ($item->item_receiveds as $itemReceived) {
                            $volumeUsed += $itemReceived->amount_received;
                        }
                        $volumeUsed += doubleval($payloadItem['amount_received']);
                        if ($volumeUsed > $item->order_item->volume) {
                            throw new Exception("Item diterima melebihi volume order (Item $item->id)");
                        }
                    }
                }
            }
            ItemReceived::insert($payload);
            notify()->success('Penerimaan item berhasil disimpan', 'Berhasil');
            return redirect("/order/$orderId");
        } catch (Exception $e) {
            notify()->error($e->getMessage(), 'Gagal');
            return redirect()->back();
        }
    }

    public function destroy(int $id): RedirectResponse
    {
        $itemReceived = ItemReceived::find($id);

        if (!$itemReceived) {
            notify()->error('Item/barang tidak ditemukan', 'Gagal');
            return redirect()->back();
        }

        $itemReceived->delete();
        notify()->success('Item/barang diterima berhasil dihapus', 'Gagal');
        return redirect()->back();
    }
}
