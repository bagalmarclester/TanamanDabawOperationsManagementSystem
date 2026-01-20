<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    public function index()
    {
        // Get all users except admin
        $employees = User::where('is_admin', false)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('employees', compact('employees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'position' => 'required|string|max:100',
        ]);

        User::create([
            'name'      => $validated['name'],
            'email'     => $validated['email'],
            'username'  => explode('@', $validated['email'])[0],
            'password'  => Hash::make('password123'),
            'position'  => $validated['position'],
            'is_admin'  => false,
            'is_active' => true,
        ]);

        return response()->json(['message' => 'Employee added successfully!']);
    }

    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $id,
            'reset_password' => 'nullable|boolean'
        ]);

        $user = User::find($id);
        if ($user) {
            $dataToUpdate = [
                'name'     => $validated['name'],
                'email'    => $validated['email'],
            ];

            if ($request->boolean('reset_password')) {
                $dataToUpdate['password'] = Hash::make('password123');
            }
            $user->update($dataToUpdate);

            return response()->json([
                'message' => 'Employee updated successfully'
            ], 200);
        } else {
            return response()->json([
                'message' => 'Employee not found'
            ], 404);
        }
    }


    public function destroy(string $id)
    {
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
    }

    public function show(string $id)
    {
        $employee = User::find($id);
        return view('employeespanel', compact('employee'));
    }
}
