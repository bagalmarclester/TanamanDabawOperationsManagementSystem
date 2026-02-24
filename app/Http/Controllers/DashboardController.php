<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Inventory;
use App\Models\InventoryCategory;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\Quote;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $totalClients = Client::count();
        $clients = Client::all();
        $totalActiveProjects = Project::where('is_active', 1)->count();
        $totalEmployees = User::where('role', '!=', 'Admin')
            ->orderBy('created_at', 'desc')
            ->count();
        $totalAcceptedQuotes = Quote::where('status', 'accepted')->count();
        $totalSentInvoices = Invoice::where('status', 'sent')->count();
        $totalInventoryItems = Inventory::count();
        $pendingQuotes = Quote::where('status', 'pending')->get();
        $projects = Project::where('is_active', true)->get();
        $categories = InventoryCategory::all();
        return view('dashboard', compact(
            'totalClients',
            'totalActiveProjects',
            'totalEmployees',
            'clients',
            'totalInventoryItems',
            'totalAcceptedQuotes',
            'totalSentInvoices',
            'pendingQuotes',
            'projects',
            'categories'
        ));
    }
}
