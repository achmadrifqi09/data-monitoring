<?php

namespace App\Http\Controllers;

use App\Imports\BPLImport;
use App\Models\BPL;
use App\Models\Partner;
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
        $partnersQuery = BPL::whereNull('deleted_at');

        if ($search) {
            $partnersQuery->where(function ($query) use ($search) {
                $query->where('item_name', 'like', '%' . $search . '%')
                    ->orWhere('unit', 'like', '%' . $search . '%');
            });
        }

        $bpl = $partnersQuery->paginate(10);
        return view('pages.bpl.index', [
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

    public function store(Request $request): RedirectResponse
    {
        try {
            $data = $this->validateData($request->input());
            BPL::create($data);

            notify()->success('Data BPL ditambahkan', 'Berhasil');
            return redirect()->back();

        } catch (ValidationException $e) {
            notify()->success($e->getMessage(), 'Gagal');
            return redirect()->back();
        }
    }

    public function update(Request $request, $id): RedirectResponse
    {
        try {
            $data = $this->validateData($request->except('_token', '_method'));

            BPL::where('id', $id)
                ->update([
                    'item_name' => $data['item_name'],
                    'unit' => $data['unit'],
                ]);
            notify()->success('Data BPL berhasil diupdate', 'Berhasil');
            return redirect()->back();

        } catch (ValidationException $e) {
            notify()->success($e->getMessage(), 'Gagal');
            return redirect()->back();
        }
    }

    public function destroy($id): RedirectResponse
    {
        $bpl = BPL::find($id);
        if (!$bpl) {
            notify()->error('Data BPL tidak ditemukan', 'Gagal');
            return redirect()->back();
        }
        $bpl->delete();
        notify()->success('Data BPL telah dihapus', 'Berhasil');
        return redirect()->back();
    }

    /**
     * @throws ValidationException
     */
    private function validateData($input)
    {
        $validator = Validator::make($input, [
            'item_name' => 'required|min:4',
            'unit' => 'nullable',
        ], ['item_name.required' => 'Nama item harus diisi', 'item_name.min' => 'Nama item minimal 4 karakter']);

        if ($validator->fails()) {
            $error = $validator->errors()->first();
            throw ValidationException::withMessages([
                'message' => $error,
            ]);
        }

        return $input;
    }
}
