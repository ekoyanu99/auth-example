<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::get();
        return view('role-permission.permission.index', [
            'permissions' => $permissions,
        ]);
    }

    public function create()
    {
        return view('role-permission.permission.create');
    }

    public function store(Request $request)
    {
        $request->validate(
            [
                'f_Name' => [
                    'required',
                    'string',
                    'unique:permissions,name'
                ]
            ]
        );

        Permission::create([
            'name' => $request->f_Name
        ]);

        return redirect('permissions')->with('status', 'Permission Created Succcesfully');
    }

    public function edit(Permission $permission)
    {
        return view('role-permission.permission.edit', [
            'permission' => $permission,
        ]);
    }

    public function update(Request $request, Permission $permission)
    {
        $request->validate(
            [
                'f_Name' => [
                    'required',
                    'string',
                    'unique:permissions,name,' . $permission->id
                ]
            ]
        );

        $permission->update([
            'name' => $request->f_Name
        ]);

        return redirect('permissions')->with('status', 'Permission Updated Succesfully');
    }

    public function destroy($permissionId)
    {
        $permission = Permission::find($permissionId);
        $permission->delete();

        return redirect('permissions')->with('status', 'Permission Deleted Succesfully');
    }
}
