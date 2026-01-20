<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $query = Client::query();

        if ($request->filled('search')) {
            $searchTerm = $request->input('search');

            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                    ->orWhere('email', 'like', "%{$searchTerm}%");
            });
        }
        $clients = $query->paginate(20)->withQueryString();
        return view('client', compact('clients'));
    }

    public function create(Request $request)
    {
        Client::create($request->all());
        return response()->json([
            'message' => 'Client created successfully',
            'redirect' => route('clients')
        ], 200);
    }

    public function update(Request $request, string $id)
    {
        $client = Client::find($id);
        if ($client) {
            $client->update($request->all());

            return response()->json([
                'message' => 'Client updated successfully'
            ], 200);
        } else {
            return response()->json([
                'message' => 'Client not found'
            ], 404);
        }
    }
    public function destroy(string $id)
    {
        $client = Client::find($id);
        if ($client) {
            $client->delete();

            return response()->json([
                'message' => 'Client deleted successfully',
                'redirect' => route('projects')
            ], 200);
        } else {
            return response()->json([
                'message' => 'Client not found'
            ], 404);
        }
    }

    public function show(string $id)
    {
        $client = Client::find($id);
        return view('clientpanel', compact('client'));
    }
}
