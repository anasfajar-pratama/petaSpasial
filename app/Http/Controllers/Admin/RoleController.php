<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions')->paginate(15);
        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::all()->groupBy(fn($p) => explode('-', $p->name)[0] ?? 'general');
        return view('admin.roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role = Role::create(['name' => $data['name']]);

        if (!empty($data['permissions'])) {
            $role->syncPermissions($data['permissions']);
        }

        return to_route('admin.roles.index')->with('success', 'Role berhasil ditambahkan.');
    }

    public function edit(Role $role)
    {
        if ($role->name === 'Administrator') {
            return back()->with('error', 'Role Administrator tidak bisa diedit.');
        }

        $permissions = Permission::all()->groupBy(fn($p) => explode('-', $p->name)[0] ?? 'general');
        $rolePermissions = $role->permissions->pluck('id')->toArray();

        return view('admin.roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    public function update(Request $request, Role $role)
    {
        if ($role->name === 'Administrator') {
            return back()->with('error', 'Role Administrator tidak bisa diedit.');
        }

        $data = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role->update(['name' => $data['name']]);
        $role->syncPermissions($data['permissions'] ?? []);

        return to_route('admin.roles.index')->with('success', 'Role berhasil diperbarui.');
    }

    public function destroy(Role $role)
    {
        if ($role->name === 'Administrator') {
            return back()->with('error', 'Role Administrator tidak bisa dihapus.');
        }

        if ($role->users()->count() > 0) {
            return back()->with('error', 'Role masih digunakan oleh user. Tidak bisa dihapus.');
        }

        $role->delete();
        return to_route('admin.roles.index')->with('success', 'Role berhasil dihapus.');
    }
}
