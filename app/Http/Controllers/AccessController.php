<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AccessController extends Controller
{
    public function index(): View
    {
        $roles = Role::with('permissions')->get();
        $permissions = Permission::all();
        return view('pages.control-area.user.access', [
            'roles' => $roles,
            'permissions' => $permissions
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $permissions = collect($request->except('_token', 'name'))->keys();
        if ($permissions->isEmpty()) {
            notify()->error('Izin akses minimal 1 akses', 'Gagal');
            return redirect()->back();
        }

        if (!$request->only('name')) {
            notify()->error('Nama akses harus diisi', 'Gagal');
            return redirect()->back();
        }

        $roleName = $request->input('name');
        $roleName = strtoupper(str_replace(' ', '_', $roleName));
        $role = Role::create(['name' => $roleName]);
        $role->givePermissionTo($permissions->toArray());

        notify()->success('Berhasil membuat akses user', 'Berhasil');
        return redirect()->back();
    }

    public function destroy(int $id): RedirectResponse
    {
        $role = Role::find($id);

        if (!$role) {
            notify()->error('Akses tidak ditemukan', 'Gagal');
            return redirect()->back();
        }

        $users = User::role($role->name)->get();

        foreach ($users as $user) {
            $user->removeRole($role->name);
        }
        $role->delete();
        notify()->success('Berhasil menghapus akses user', 'Berhasil');
        return redirect()->back();
    }
}
