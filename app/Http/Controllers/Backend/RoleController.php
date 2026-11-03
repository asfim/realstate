<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::where('guard_name', 'admin')->with('permissions')->get();
        return view('backend.roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::where('guard_name', 'admin')->get();
        // Group permissions by their prefix/module
        $groupedPermissions = $permissions->groupBy(function($item) {
            $parts = explode('.', $item->name);
            if (count($parts) > 1) {
                return $parts[0];
            }
            $parts = explode('-', $item->name);
            return count($parts) > 1 ? $parts[0] : 'other';
        });
        return view('backend.roles.create', compact('groupedPermissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name,NULL,id,guard_name,admin',
            'permissions' => 'nullable|array',
        ]);

        $role = Role::create([
            'name' => $request->name,
            'guard_name' => 'admin'
        ]);

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        return redirect()->route('admin.roles.index')->with('success', 'Role created successfully.');
    }

    public function edit(Role $role)
    {
        // Prevent editing Super Admin to avoid issues
        if ($role->name === 'Super Admin') {
            return redirect()->route('admin.roles.index')->with('error', 'Super Admin role permissions cannot be edited directly.');
        }

        $permissions = Permission::where('guard_name', 'admin')->get();
        $groupedPermissions = $permissions->groupBy(function($item) {
            $parts = explode('.', $item->name);
            if (count($parts) > 1) {
                return $parts[0];
            }
            $parts = explode('-', $item->name);
            return count($parts) > 1 ? $parts[0] : 'other';
        });

        $rolePermissions = $role->permissions->pluck('name')->toArray();

        return view('backend.roles.edit', compact('role', 'groupedPermissions', 'rolePermissions'));
    }

    public function show(Role $role)
    {
        return redirect()->route('admin.roles.edit', $role);
    }

    public function update(Request $request, Role $role)
    {
        if ($role->name === 'Super Admin') {
            return redirect()->route('admin.roles.index')->with('error', 'Super Admin role cannot be updated.');
        }

        $request->validate([
            'name' => 'required|string|unique:roles,name,' . $role->id . ',id,guard_name,admin',
            'permissions' => 'nullable|array',
        ]);

        $role->update([
            'name' => $request->name,
        ]);

        $role->syncPermissions($request->input('permissions', []));

        return redirect()->route('admin.roles.index')->with('success', 'Role updated successfully.');
    }

    public function destroy(Role $role)
    {
        if ($role->name === 'Super Admin') {
            return redirect()->route('admin.roles.index')->with('error', 'Super Admin role cannot be deleted.');
        }

        $role->delete();

        return redirect()->route('admin.roles.index')->with('success', 'Role deleted successfully.');
    }
}
