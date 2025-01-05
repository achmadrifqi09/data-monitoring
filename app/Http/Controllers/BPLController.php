<?php

namespace App\Http\Controllers;

use App\Imports\BPLImport;
use App\Models\BPL;
use App\Models\Item;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class BPLController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->input('search');
        $bplQuery = BPL::whereNull('deleted_at');

        if ($search) {
            $bplQuery->where('bpl_number', 'like', '%' . $search . '%')
                ->orWhereHas('items', function ($query) use ($search) {
                    $query->where('description', 'like', '%' . $search . '%');
                });
        }
        $bpl = $bplQuery->paginate(15);
        return view('pages.bpl.index', [
            'bpl' => $bpl,
        ]);
    }

    public function show(int $id): View
    {
        $bpl = BPL::where('id', $id)
            ->with(['items.order_item'])
            ->first();

        return view('pages.bpl.detail', [
            'bpl' => $bpl,
        ]);
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls,csv',
        ]);

        try {
            Excel::import(new BPLImport, $request->file('excel_file'));
            notify()->success('Data BPL berhasil diimport', 'Berhasil');
            return redirect()->back()->with('success', 'Data berhasil diimpor!');
        } catch (\Throwable $e) {
            notify()->error($e->getMessage(), 'Gagal');
            return redirect()->back();
        }
    }

    public function getData(Request $request): JsonResponse
    {
        $bpl = $this->getBpl($request);
        return response()->json($bpl);
    }


    public function store(Request $request): RedirectResponse
    {
        try {
            $data = $this->validation($request);

            $bpl = BPL::create([
                'bpl_number' => $data['bpl_number'],
                'description' => $data['description'],
                'date_of_use' => $data['date_of_use'],
            ]);

            notify()->success('Data BPL ditambahkan, silakan masukkan item', 'Berhasil');
            return redirect("/bpl/$bpl->bpl_number/form");
        } catch (ValidationException $e) {
            notify()->error($e->getMessage(), 'Gagal');
            return redirect()->back();
        }
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        try {
            $data = $this->validation($request);

            $bpl = BPL::find($id);

            if (!$bpl) {
                notify()->error('Data BPL tidak ditemukan', 'Gagal');
                return redirect()->back();
            }

            $oldBPLNumber = $bpl->bpl_number;
            $bpl->update($data);
            Item::where('bpl_number', $oldBPLNumber)->update(['bpl_number' => $bpl->bpl_number]);

            notify()->success('Data BPL berhasil diupdate', 'Berhasil');
            return redirect()->back();
        } catch (ValidationException $e) {
            notify()->error($e->getMessage(), 'Gagal');
            return redirect()->back();
        }
    }

    /**
     * @throws ValidationException
     */
    private function validation(Request $request): array
    {
        $bplId = $request->route('id');

        $rules = [
            'bpl_number' => 'required|min:1|unique:bpl',
            'description' => 'required|min:4',
            'date_of_use' => 'required|date'
        ];

        $messages = [
            'bpl_number.required' => 'Nomor BPL harus diisi',
            'bpl_number.min' => 'Nomor BPL minimal 1 karakter',
            'description.required' => 'Uraian harus diisi',
            'description.min' => 'Uraian minimal 4 karakter',
            'date_of_use.required' => 'Tanggal rencana pakai harus diisi',
            'date_of_use.date' => 'Tanggal rencana pakai harus berupa tanggal',
            'bpl_number.unique' => 'Nomor BPL sudah digunakan',
        ];

        if ($request->isMethod('patch')) {
            $rules['bpl_number'] = 'required|min:1|unique:bpl' . ",id,$bplId";
        }

        $validator = Validator::make($request->except('_token'), $rules, $messages);

        if ($validator->fails()) {
            $error = $validator->errors()->first();
            throw ValidationException::withMessages([
                'message' => $error,
            ]);
        }

        return $request->except(['_token', '_method']);
    }

    public function destroy(string $bpl_number): RedirectResponse
    {
        $bpl = BPL::where('bpl_number', $bpl_number)->first();

        if (!$bpl) {
            notify()->error('Data BPL tidak ditemukan', 'Gagal');
            return redirect()->back();
        }

        $bpl->delete();
        Item::where('bpl_number', $bpl_number)->delete();

        notify()->success('Data BPL telah dihapus', 'Berhasil');
        return redirect('/bpl');
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function getBpl(Request $request): mixed
    {
        $search = $request->input('search');
        $bplQuery = BPL::whereNull('deleted_at')
            ->whereHas('items', function ($query) {
                $query->doesntHave('order_item');
            });

        if ($search) {
            $bplQuery->where('bpl_number', 'like', '%' . $search . '%')
                ->orWhereHas('items', function ($query) use ($search) {
                    $query->where('description', 'like', '%' . $search . '%');
                });
        }

        return $bplQuery
            ->get()
            ->take(15);
    }
}
