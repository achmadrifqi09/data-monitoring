<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ProjectController extends Controller
{
    public function index(): View
    {
        $project = Project::first();
        return view('pages.control-area.project.index', [
            'project' => $project
        ]);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        try {
            $validator = Validator::make($request->only(['name', 'planned_date', 'planned_finish']), [
                'name' => 'required|min:4',
                'planned_date' => 'required',
                'planned_finish' => 'required',
            ], [
                'name.required' => 'Nama proyek harus diisi',
                'name.min' => 'Nama proyek minimal 4 karakter',
                'planned_date.required' => 'Tanggal direncanakan harus diisi',
                'planned_finish.required' => 'Tanggal selesai harus diisi'
            ]);

            if ($validator->fails()) {
                $error = $validator->errors()->first();
                throw ValidationException::withMessages([
                    'message' => $error,
                ]);
            }
            $project = Project::find($id);

            if (!$project) {
                throw ValidationException::withMessages(['message' => 'Date proyek tidak ditemukan']);
            }

            $project->name = $request->input('name');
            $project->planned_date = $request->input('planned_date');
            $project->planned_finish = $request->input('planned_finish');
            $project->save();

            notify()->success('Data proyek berhasil diperbarui', 'Berhasil');
            return redirect()->back();
        } catch (ValidationException $e) {
            notify()->success($e->getMessage(), 'Gagal');
            return redirect()->back();
        }
    }
}
