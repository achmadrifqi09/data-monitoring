<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function __invoke(Request $request)
    {
        abort_if(Auth::guest(), Response::HTTP_FORBIDDEN);
        $path = $request->query('path');
        if (!Storage::exists($path)) {
            notify()->error('Dokument order tidak ditemukan', 'Terjadi Kesalahan');
            return redirect()->back();
        }
        return response()->file(
            Storage::path($path)
        );
    }
}
