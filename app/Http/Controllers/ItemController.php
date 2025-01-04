<?php

namespace App\Http\Controllers;

use App\Http\Requests\ItemRequest;
use App\Models\Item;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ItemController extends Controller
{
    public function add(string $bpl_number): View
    {
        return view('pages.bpl.form', [
            'bpl_number' => $bpl_number,
        ]);
    }

    public function getItemByBPLNumber(string $bpl_number)
    {
        $items = Item::where('bpl_number', $bpl_number)
            ->whereNull('deleted_at')->get();
        return response()->json($items);
    }

    public function store(Request $request, string $bpl_number): RedirectResponse
    {
        try {
            $data = $this->singleValidation($request);
            $data['bpl_number'] = $bpl_number;

            Item::create($data);

            notify()->success('Berhasil menambahkan item BPL', 'Berhasil');
            return redirect()->back();
        } catch (ValidationException | \Exception $e) {
            notify()->error($e->getMessage(), 'Gagal');
            return redirect()->back();
        }
    }

    public function bulkStore(ItemRequest $request, string $bpl_number): RedirectResponse
    {
        $items = $request->validated();
        $items = $items['items'];
        $createdAt = Carbon::now();

        $items = array_map(function ($item) use ($bpl_number, $createdAt) {
            $item['bpl_number'] = $bpl_number;
            $item['created_at'] = $createdAt;
            return $item;
        }, $items);
        Item::insert($items);
        notify()->success('Berhasil menambahkan item dari BPL', 'Berhasil');
        return redirect('/bpl');
    }

    public function update(Request $request, string $bpl_number, int $id): RedirectResponse
    {
        try {
            $data = $this->singleValidation($request);

            $bpl = Item::where('id', $id)
                ->whereNull('deleted_at')
                ->first();

            if (!$bpl) {
                notify()->error('Item yang diupdate tidak ditemukan', 'Gagal');
                return redirect()->back();
            }

            $bpl->fill($data);
            $bpl->save();

            notify()->success('Berhasil menambahkan item BPL', 'Berhasil');
            return redirect()->back();
        } catch (ValidationException | \Exception $e) {
            notify()->error($e->getMessage(), 'Gagal');
            return redirect()->back();
        }
    }

    public function destroy(string $bpl_number, int $id): RedirectResponse
    {
        $bpl = Item::where('id', $id)
            ->whereNull('deleted_at')
            ->first();

        if (!$bpl) {
            notify()->error('Item yang diupdate tidak ditemukan', 'Gagal');
            return redirect()->back();
        }

        $bpl->delete();
        notify()->success('Berhasil menghapus item BPL', 'Berhasil');
        return redirect()->back();
    }

    /**
     * @throws ValidationException
     */
    private function singleValidation(Request $request): array
    {
        $validator = Validator::make($request->except('_token'), [
            'item_name' => 'required|min:2',
            'unit' => 'nullable',
            'brand' => 'nullable',
            'specification' => 'nullable',
        ], [
            'item_name.required' => 'Nama item harus diisi',
            'item_name.min' => 'Nama item minimal 2 karakter',
        ]);

        if ($validator->fails()) {
            $error = $validator->errors()->first();
            throw ValidationException::withMessages([
                'message' => $error,
            ]);
        }

        return $request->except('_token');
    }
}
