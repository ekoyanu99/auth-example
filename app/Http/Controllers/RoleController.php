<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class RoleController extends Controller implements HasMiddleware
{

    public static function middleware(): array
    {
        return [
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('delete'), only: ['destroy']),
        ];
    }

    public function index()
    {
        $roles = Role::get();
        return view('role-permission.role.index', [
            'roles' => $roles,
        ]);
    }

    public function create()
    {
        return view('role-permission.role.create');
    }

    public function store(Request $request)
    {
        $request->validate(
            [
                'f_Name' => [
                    'required',
                    'string',
                    'unique:roles,name'
                ]
            ]
        );

        Role::create([
            'name' => $request->f_Name
        ]);

        return redirect('roles')->with('status', 'Role Created Succcesfully');
    }

    public function edit(Role $role)
    {
        return view('role-permission.role.edit', [
            'role' => $role,
        ]);
    }

    public function update(Request $request, Role $role)
    {
        $request->validate(
            [
                'f_Name' => [
                    'required',
                    'string',
                    'unique:roles,name,' . $role->id
                ]
            ]
        );

        $role->update([
            'name' => $request->f_Name
        ]);

        return redirect('roles')->with('status', 'Role Updated Succesfully');
    }

    public function destroy($roleId)
    {
        $role = Role::find($roleId);
        $role->delete();

        return redirect('roles')->with('status', 'Role Deleted Succesfully');
    }

    public function addPermissionToRole($roleId)
    {
        $permissions = Permission::get();
        $role = Role::findOrFail($roleId);
        $rolePermissions = DB::table('role_has_permissions')->where('role_has_permissions.role_id', $role->id)->pluck('role_has_permissions.permission_id', 'role_has_permissions.permission_id')->all();
        return view('role-permission.role.add-permission', [
            'role' => $role,
            'permissions' => $permissions,
            'rolePermissions' => $rolePermissions,
        ]);
    }

    public function givePermissionToRole(Request $request, $roleId)
    {
        $request->validate([
            'f_Permission' => 'required',
        ]);

        $role = Role::findOrFail($roleId);
        $role->syncPermissions($request->f_Permission);

        return redirect()->back()->with('status', 'Permission added to role');
    }
}
