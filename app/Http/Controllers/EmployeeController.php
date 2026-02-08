<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{
    public function index()
    {
        // Get all users except admin
        $employees = User::where('role', '!=', 'Admin')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('employees', compact('employees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate(
            [
                'name'  => 'required|string|max:255',
                'email' => [
                    'required',
                    'email',
                    'regex:/^[\w\.\-]+@[a-zA-Z\d\-]+\.[a-zA-Z]{2,}$/',
                    Rule::unique('users', 'email')->whereNull('deleted_at'),
                ],
                'role'  => 'required|string|in:Operations Manager,Head Landscaper,Field Crew',
            ],
            [
                'email.regex' => 'Please enter a valid email, e.g., abc@email.com',
            ]
        );
        try {
            // Look for existing user (including soft-deleted)
            $user = User::withTrashed()
                ->where('email', $validated['email'])
                ->first();

            if ($user && $user->trashed()) {
                // Restore instead of create
                $user->restore();

                $user->update([
                    'name'     => $validated['name'],
                    'username' => explode('@', $validated['email'])[0],
                    'role'     => $validated['role'],
                    'status'   => 'Active',
                ]);

                return response()->json([
                    'message' => 'Employee restored successfully!',
                ]);
            }

            // Create new user
            User::create([
                'name'     => $validated['name'],
                'email'    => $validated['email'],
                'username' => explode('@', $validated['email'])[0],
                'password' => Hash::make('password123'),
                'role'     => $validated['role'],
                'status'   => 'Active',
            ]);

            return response()->json(['message' => 'Employee added successfully!']);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An unexpected error has occurred. Please contact the developer. Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'name'   => 'required|string|max:255',
            'email'  => 'required|email|unique:users,email,' . $id,
            'role'   => 'required|string|in:Operations Manager,Head Landscaper,Field Crew',
            'status' => 'required|string|in:Active,Inactive',
            'reset_password' => 'nullable|boolean'
        ]);
        try {
            $user = User::find($id);
            if (! $user) {
                return response()->json([
                    'message' => 'Employee not found'
                ], 404);
            }

            $dataToUpdate = [
                'name'   => $validated['name'],
                'email'  => $validated['email'],
                'role'   => $validated['role'],
                'status' => $validated['status'],
            ];

            if ($request->boolean('reset_password')) {
                $dataToUpdate['password'] = Hash::make('password123');
            }

            $user->fill($dataToUpdate);

            if (! $user->isDirty()) {
                return response()->json([
                    'message' => 'No changes were made'
                ], 200);
            }

            $user->save();

            return response()->json([
                'message' => 'Employee updated successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An unexpected error has occurred. Please contact the developer. Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * New Function: Deactivate Employee
     * Sets status to 'Inactive' instantly.
     */
    public function deactivate(string $id)
    {
        try {
            $user = User::find($id);

            if (!$user) {
                return response()->json([
                    'message' => 'Employee not found'
                ], 404);
            }

            // Update status to Inactive
            $user->status = 'Inactive';
            $user->save();

            return response()->json([
                'message' => 'Employee deactivated successfully',
                'status' => 'Inactive'
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
            $user = User::find($id);
            if ($user) {
                $user->delete();

                return response()->json([
                    'message' => 'Employee deleted successfully',
                    'redirect' => route('employees')
                ], 200);
            } else {
                return response()->json([
                    'message' => 'Employee not found'
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
        $employee = User::find($id);
        return view('employeespanel', compact('employee'));
    }
}
