<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $project = Project::first();

        return view('pages.dashboard', [
            'project' => $project,
        ]);
    }
}
