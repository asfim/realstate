<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminUserController extends Controller
{
    public function index()
    {
        $admins = Admin::with('roles')->orderBy('id', 'desc')->get();
        return view('backend.admins.index', compact('admins'));
    }

    public function create()
    {
        $roles = Role::where('guard_name', 'admin')->get();
        return view('backend.admins.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:admins,email',
            'password' => 'required|string|min:8|confirmed',
            'roles' => 'required|array',
            'roles.*' => 'string|exists:roles,name',
        ]);

        $admin = Admin::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $admin->assignRole($request->roles);

        return redirect()->route('admin.admins.index')->with('success', 'Admin user created successfully.');
    }

    public function edit(Admin $admin)
    {
        $roles = Role::where('guard_name', 'admin')->get();
        $adminRoles = $admin->roles->pluck('name')->toArray();
        return view('backend.admins.edit', compact('admin', 'roles', 'adminRoles'));
    }

    public function show(Admin $admin)
    {
        return redirect()->route('admin.admins.edit', $admin);
    }

    public function update(Request $request, Admin $admin)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:admins,email,' . $admin->id,
            'password' => 'nullable|string|min:8|confirmed',
            'roles' => 'required|array',
            'roles.*' => 'string|exists:roles,name',
        ]);

        $admin->name = $request->name;
        $admin->email = $request->email;

        if ($request->filled('password')) {
            $admin->password = Hash::make($request->password);
        }

        $admin->save();

        $admin->syncRoles($request->roles);

        return redirect()->route('admin.admins.index')->with('success', 'Admin user updated successfully.');
    }

    public function destroy(Admin $admin)
    {
        if ($admin->id === 1 || $admin->email === 'admin@example.com') {
            return redirect()->route('admin.admins.index')->with('error', 'The primary administrator account cannot be deleted.');
        }

        if (auth('admin')->id() === $admin->id) {
            return redirect()->route('admin.admins.index')->with('error', 'You cannot delete your own administrator account.');
        }

        $admin->delete();

        return redirect()->route('admin.admins.index')->with('success', 'Admin user deleted successfully.');
    }
}
