<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserTechController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('model.user-tech', compact('users'));
    }

    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|string',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

        return redirect()->back()->with('success', 'Utilisateur ajouté');
    }

    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required',
            'role' => 'required|in:user,admin,superadmin',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
        ]);
        $user->update($request->only('name', 'role'));
        return response()->json(['success' => true]);
    }

    public function destroyUser(User $user)
    {
        $user->delete();
        return redirect()->route('user-tech.index');
    }

}
