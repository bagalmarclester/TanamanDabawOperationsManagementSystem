<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Inventory;
use App\Models\Project;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $totalClients = Client::count();
        $clients = Client::all();
        $totalActiveProjects = Project::where('is_active', 1)->count();
        $totalEmployees = User::where('is_admin', false)
            ->orderBy('created_at', 'desc')
            ->count();
        $totalInventoryItems = Inventory::count();
        return view('dashboard', compact('totalClients', 'totalActiveProjects', 'totalEmployees', 'clients', 'totalInventoryItems'));
    }
}