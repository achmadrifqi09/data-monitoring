<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $project = Project::first();
        $receivedTotalPrice = DB::table('item_receiveds')
            ->whereNull('deleted_at')
            ->sum(DB::raw('CAST(nominal AS DECIMAL(10,2))'));

        $orderTotalPrice = DB::table('items')
            ->whereNull('deleted_at')
            ->sum(DB::raw('CAST(price AS DECIMAL(10,2)) * volume'));


        $totalOrder = DB::table('orders')
            ->whereNull('deleted_at')
            ->count();


        return view('pages.dashboard', [
            'project' => $project,
            'received_total_price' => floor($receivedTotalPrice),
            'order_total_price' => floor($orderTotalPrice),
            'total_order' => $totalOrder
        ]);
    }
}
