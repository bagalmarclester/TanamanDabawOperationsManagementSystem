<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;


class ClientController extends Controller
{

    public function index(Request $request)
    {
        $query = Client::query();
        $clients = $query->paginate(20)->withQueryString();
        return view('client', compact('clients'));
    }

    public function create(Request $request)
    {
        $validated = $request->validate(
            [
                'name'     => 'required|string|max:100',
                'email'    => 'required|email|regex:/^[\w\.\-]+@[a-zA-Z\d\-]+\.[a-zA-Z]{2,}$/|unique:clients,email',
                'phone' => 'required|string|regex:/^09\d{9}$/|unique:clients,phone',
                'address' => 'required|string|max:255',
            ],
            [
                'email.regex' => 'Please enter a valid email, e.g., abc@email.com',
                'phone.regex' => 'Please enter a valid PH number, e.g., 09123456789'
            ]
        );
        try {
            Client::create([
                'name'     => $validated['name'],
                'email'    => $validated['email'],
                'phone' => $validated['phone'],
                'address' => $validated['address'],
            ]);
            return response()->json([
                'message' => 'Client created successfully',
                'redirect' => route('clients')
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An unexpected error has occurred. Please contact the developer. Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, string $id)
    {
        $validated = $request->validate(
            [
                'name'     => 'required|string|max:100',
                'email'    => 'required|email|regex:/^[\w\.\-]+@[a-zA-Z\d\-]+\.[a-zA-Z]{2,}$/',
                'phone' => 'required|string|regex:/^09\d{9}$/',
                'address' => 'required|string|max:255',
            ],
            [
                'email.regex' => 'Please enter a valid email, e.g., abc@email.com',
                'phone.regex' => 'Please enter a valid PH number, e.g., 09123456789'
            ]
        );
        try {
            $client = Client::find($id);

            if (! $client) {
                return response()->json([
                    'message' => 'Client not found'
                ], 404);
            }

            // Fill but donot  save
            $client->fill($validated);
            // Check if anything is changed in the data
            if (! $client->isDirty()) {
                return response()->json([
                    'message' => 'No changes were made'
                ], 200);
            }

            $client->save();
            return response()->json([
                'message' => 'Client updated successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An unexpected error has occurred. Please contact the developer. Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(string $id)
    {
        try {
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
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An unexpected error has occurred. Please contact the developer. Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(string $id)
    {
        $client = Client::find($id);
        return view('clientpanel', compact('client'));
    }
}
