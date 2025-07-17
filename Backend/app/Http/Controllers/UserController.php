<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        if (!$user || !$user->hasRole('admin')) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }
        $users = \App\Models\User::all();
        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);

        $validated['password'] = \Illuminate\Support\Facades\Hash::make($validated['password']);
        $user = \App\Models\User::create($validated);

        return response()->json([
            'success' => true,
            'data' => $user
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $user = \App\Models\User::find($id);
        if (!$user) {
            return response()->json(['message' => 'Not found.'], 404);
        }
        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = \App\Models\User::find($id);
        if (!$user) {
            return response()->json(['message' => 'Not found.'], 404);
        }
        $user->update($request->all());
        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = \App\Models\User::find($id);
        if (!$user) {
            return response()->json(['message' => 'Not found.'], 404);
        }
        $user->delete();
        return response()->json([
            'success' => true,
            'message' => 'User deleted.'
        ]);
    }

    /**
     * Assign a role to a user.
     */
    public function assignRole(Request $request, $user)
    {
        // TODO: Implement role assignment logic
        return response()->json([
            'success' => true,
            'message' => 'Role assigned (stub).'
        ]);
    }

    /**
     * Get roles of the currently authenticated user.
     */
    public function getRoles(Request $request)
    {
        $roles = $request->user()->roles->pluck('name')->toArray();
        return response()->json([
            'success' => true,
            'roles' => $roles
        ]);
    }

    /**
     * Programmatically check if the user passes the CheckRole middleware for 'admin'.
     */
    public function checkRoleAdmin(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }
        $allowedRoles = ['admin'];
        $userRoles = $user->roles->pluck('name')->toArray();
        if (!array_intersect($allowedRoles, $userRoles)) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }
        return response()->json([
            'success' => true,
            'message' => 'User has admin access.',
            'roles' => $userRoles
        ]);
    }
}
