<?php

namespace App\Http\Controllers;

use App\Imports\PartnerImport;
use App\Models\Partner;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class PartnerController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->input('search');
        $partnersQuery = Partner::whereNull('deleted_at');

        if ($search) {
            $partnersQuery->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('address', 'like', '%' . $search . '%');
            });
        }

        $partners = $partnersQuery->paginate(10);
        return view('pages.partner.index', [
            'partners' => $partners
        ]);
    }

    public function getData(Request $request)
    {
        $search = $request->input('search');
        $partnersQuery = Partner::whereNull('deleted_at');

        if ($search) {
            $partnersQuery->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            });
        }

        $partners = $partnersQuery->take(10)->get();
        return response()->json($partners);
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls,csv',
        ]);
        try {

            Excel::import(new PartnerImport, $request->file('excel_file'));
            notify()->success('Data rekanan berhasil diimport', 'Berhasil');
            return redirect()->back()->with('success', 'Data berhasil diimpor!');
        } catch (\Throwable $e) {
            notify()->error($e->getMessage(), 'Gagal');
            return redirect('/rekanan');
        }
    }

    public function store(Request $request): RedirectResponse
    {
        try {
            $data = $this->validateData($request->input());
            Partner::create($data);

            notify()->success('Data rekanan ditambahkan', 'Berhasil');
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

            Partner::where('id', $id)
                ->update([
                    'name' => $data['name'],
                    'address' => $data['address'],
                ]);
            notify()->success('Data rekanan berhasil diupdate', 'Berhasil');
            return redirect()->back();

        } catch (ValidationException $e) {
            notify()->success($e->getMessage(), 'Gagal');
            return redirect()->back();
        }
    }

    public function destroy($id): RedirectResponse
    {
        $partner = Partner::find($id);
        if (!$partner) {
            notify()->success('Data rekanan tidak ditemukan', 'Gagal');
            return redirect()->back();
        }
        $partner->delete();
        notify()->success('Data rekanan telah dihapus', 'Berhasil');
        return redirect()->back();
    }


    /**
     * @throws ValidationException
     */
    private function validateData($input)
    {
        $validator = Validator::make($input, [
            'name' => 'required|min:4',
            'address' => 'nullable',
        ], ['name.required' => 'Nama rekanan harus diisi', 'name.min' => 'Nama rekanan minimal 4 karakter']);

        if ($validator->fails()) {
            $error = $validator->errors()->first();
            throw ValidationException::withMessages([
                'message' => $error,
            ]);
        }

        return $input;
    }
}
