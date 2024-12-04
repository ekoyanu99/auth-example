<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    public function data()
    {
        // return DataTables::of(User::query())->toJson();
        return DataTables::of(User::query())
            // ->addColumn('action', function ($user) {
            //     return '<a href="users/' . $user->id . '/edit" class="items-center px-4 py-2 bg-blue-800 dark:bg-blue-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-blue-800 uppercase tracking-widest hover:bg-blue-700 dark:hover:bg-blue focus:bg-blue-700 dark:focus:bg-blue active:bg-blue-900 dark:active:bg-blue-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-blue-800 transition ease-in-out duration-150">Edit</a>
            //     <a href="users/' . $user->id . '/delete" class="items-center px-4 py-2 bg-red-800 dark:bg-red-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-red-800 uppercase tracking-widest hover:bg-red-700 dark:hover:bg-red focus:bg-red-700 dark:focus:bg-red active:bg-red-900 dark:active:bg-red-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-red-800 transition ease-in-out duration-150">Delete</a>';
            // })
            ->addColumn('action', 'role-permission.user.datatable')
            ->addColumn('role', function ($user) {
                return $user->roles->pluck('name')->implode(', ');
            })
            ->rawColumns(['action'])
            ->make(true);
        // DB::select('SELECT users.id as id, users.name as name, users.email as email, roles.name as role FROM users, model_has_roles, roles WHERE model_has_roles.model_id = users.id AND roles.id = model_has_roles.role_id')
    }

    public function index()
    {
        // $users = User::get()->where('id', '!=', 1);
        $users = User::get();
        return view('role-permission.user.index', [
            'users' => $users
        ]);
    }

    public function create()
    {
        $roles = Role::pluck('name', 'name')->all();
        return view('role-permission.user.create', [
            'roles' => $roles
        ]);
    }

    public function store(Request $request)
    {
        $request->validate(
            [
                'f_Name' => [
                    'required',
                    'string',
                    'max:255'
                ],
                'f_Email' => [
                    'required',
                    'email',
                    'max:255',
                    'unique:users,email'
                ],
                'f_Password' => [
                    'required',
                    'string',
                    'min:8',
                    'max:20',
                ],
                'f_Roles' => [
                    'required',
                ]
            ]
        );

        $user = User::create([
            'name' => $request->f_Name,
            'email' => $request->f_Email,
            'password' => Hash::make($request->f_Password),
        ]);

        $user->syncRoles($request->f_Roles);

        return redirect('users')->with('status', 'User Created Succcesfully with Roles');
    }

    public function edit(User $user)
    {
        $roles = Role::pluck('name', 'name')->all();
        // $roles = DB::table('roles')->get();
        $userRoles = $user->roles->pluck('name', 'name')->all();
        return view('role-permission.user.edit', [
            'user' => $user,
            'roles' => $roles,
            'userRoles' => $userRoles,
        ]);
    }

    public function update(Request $request, User $user)
    {
        $request->validate(
            [
                'f_Name' => [
                    'required',
                    'string',
                    'max:255'
                ],
                'f_Password' => [
                    'nullable',
                    'string',
                    'min:8',
                    'max:20',
                ],
                'f_Roles' => [
                    'required',
                ]
            ]
        );

        $data = [
            'name' => $request->f_Name,
            'email' => $request->f_Email,
        ];

        if (!empty($request->f_Password)) {
            $data += [
                'password' => Hash::make($request->f_Password),
            ];
        }

        $user->update($data);
        $user->syncRoles($request->f_Roles);

        return redirect('users')->with('status', 'User Updated Succcesfully with Roles');
    }

    public function destroy($userId)
    {
        $user = User::findOrFail($userId);
        $user->delete();

        return redirect('users')->with('status', 'User Deleted Succcesfully with Roles');
    }
}
