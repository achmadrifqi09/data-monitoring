<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function index()
    {
        return view('pages.auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|min:4',
            'password' => 'required|min:8'
        ], [
            'username.required' => 'Password tidak boleh kosong',
            'password.required' => 'Password tidak boleh kosong',
            'password.min' => 'Password tidak boleh kurang dari 8 karakter',
            'username.min' => 'Username tidak boleh kurang dari 4 karakter'
        ]);

        if (auth()->attempt($request->only(['username', 'password']))) {
            return redirect('/');
        } else {
            return back()->withErrors(['credential-error' => 'Username atau password salah']);
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
