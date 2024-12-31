<?php

namespace App\Http\Controllers;

use App\Http\Requests\ItemReceivedRequest;
use App\Models\BPL;
use App\Models\ItemReceived;
use Illuminate\Support\Facades\DB;

class ItemReceivedController extends Controller
{
    public function store(ItemReceivedRequest $request)
    {
        try {
            $input = $request->validated();
            $orderId = $input['order_id'];
            $receivedItems = collect($input['received_items']);

            $bplIds = $receivedItems->pluck('bpl_id')->toArray();

            $existingTotals = DB::table('item_receiveds')
                ->select('bpl_id', DB::raw('SUM(amount_received) as total_received'))
                ->whereNull('deleted_at')
                ->whereIn('bpl_id', $bplIds)
                ->groupBy('bpl_id')
                ->pluck('total_received', 'bpl_id')
                ->toArray();

            $bplVolumes = BPL::select('id', 'volume')
                ->whereNull('deleted_at')
                ->whereIn('id', $bplIds)
                ->get()
                ->keyBy('id');

            return DB::transaction(function () use ($receivedItems, $bplVolumes, $existingTotals, $orderId) {
                foreach ($receivedItems as $item) {
                    $bplId = $item['bpl_id'];
                    $bpl = $bplVolumes->get($bplId);

                    if (!$bpl) {
                        throw new \Exception("BPL ID {$bplId} tidak ditemukan");
                    }

                    $currentTotal = $existingTotals[$bplId] ?? 0;
                    $newTotal = $currentTotal + floatval($item['amount_received']);

                    if ($newTotal > $bpl->volume) {
                        throw new \Exception("Volume diterima untuk BPL ID {$bplId} melebihi jumlah order");
                    }
                }

                $dataToInsert = $receivedItems->map(function ($item) use ($orderId) {
                    return [
                        'order_id' => $orderId,
                        'bpl_id' => $item['bpl_id'],
                        'amount_received' => $item['amount_received'],
                        'date_received' => $item['received_date'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                })->toArray();

                foreach (array_chunk($dataToInsert, 1000) as $chunk) {
                    ItemReceived::insert($chunk);
                }

                notify()->success('Data berhasil disimpan', 'Berhasil');
                return redirect()->back();
            });
        } catch (\Exception $e) {
            notify()->error($e->getMessage(), 'Gagal');
            return redirect()->back();
        }
    }

    public function destroy(int $id)
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
