<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('roles')->get();
        $roles = Role::all();
        return view('pages.control-area.user.manage', [
            'users' => $users,
            'roles' => $roles
        ]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'username' => 'required|min:4|unique:users',
            'name' => 'required|min:4',
            'password' => 'required|min:8',
            'confirm_password' => 'required|min:8|same:password',
            'role' => 'required'
        ], [
            'username.required' => 'Username harus diisi',
            'username.min' => 'Username minimal 4 karakter',
            'username.unique' => 'Username telah digunakan oleh user lain',
            'name.required' => 'Nama haru diisi',
            'name.min' => 'Nama minimal 4 karakter',
            'password.required' => 'Password harus diisi',
            'password.min' => 'Password minimal 8 karakter',
            'confirm_password.required' => 'Konfirmasi password harus diisi',
            'confirm_password.min' => 'Konfirmasi password minimal 8 karakter',
            'confirm_password.same' => 'Konfirmasi password harus sama dengan password',
            'role.required' => 'Akses pengguna harus diisi'
        ]);

        $user = User::create([
            'name' => $validatedData['name'],
            'username' => $validatedData['username'],
            'password' => Hash::make($validatedData['password']),
        ]);

        $user->assignRole($validatedData['role']);

        notify()->success('Berhasil menambahkan user baru', 'Berhasil');
        return redirect()->back();
    }

    public function addAccess(Request $request, int $id)
    {
        $request->validate([
            'role' =>  'required'
        ], [
            'role.required' => 'Akses user harus diisi'
        ]);

        $user = User::find($id);
        if (!$user) {
            notify()->error('User tidak ditemukan', 'Gagal');
            return redirect()->back();
        }

        $user->assignRole($request->only('role'));

        notify()->success('Akses user telah ditabahkan', 'Berhasil');
        return redirect()->back();
    }

    public function update(Request $request, int $id)
    {
        if (!$request->only('status')) {
            notify()->error('User tidak ditemukan', 'Gagal');
            return redirect()->back();
        }

        $user = User::find($id);

        if (!$user) {
            notify()->error('User tidak ditemukan', 'Gagal');
            return redirect()->back();
        }

        if (auth()->user()->id === $id) {
            notify()->error('Tidak dapat menonaktifkan', 'Gagal');
            return redirect()->back();
        }

        $user->status = (int) $request->input('status');
        $user->save();

        $message = (int)$request->input('status') == 1 ? 'Berhasil mengaktifkan user' : 'Berhasil menonaktifkan user';
        notify()->success($message, 'Berhasil');
        return redirect()->back();
    }


    public function destroy(int $id)
    {
        $user = User::find($id);
        if (!$user) {
            notify()->error('Akun tidak ditemukan', 'Gagal');
            return redirect()->back();
        }

        $user->delete();
        notify()->success('Akun telah terhapus', 'Berhasil');
        return redirect()->back();
    }
}
