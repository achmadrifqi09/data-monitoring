<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        return view('pages.order.index');
    }

    public function form(Request $request): View
    {
        $mode = $request->input('mode');

        return view('pages.order.create-form');
    }
}
