<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $project = Project::first();
        $receivedTotalPrice = DB::table('item_receiveds')
            ->sum(DB::raw('CAST(nominal AS DECIMAL(30,2))'));

        $orderTotalPrice = DB::table('order_items')
            ->sum(DB::raw('CAST(price AS DECIMAL(30,2)) * volume'));

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
