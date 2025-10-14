<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserAccessController extends Controller
{
    public function index(Request $request)
    {
        $users = User::query()
            ->with(['roles', 'permissions'])
            ->orderBy('email')
            ->paginate(15)
            ->withQueryString();

        return view('admin.users.access', [
            'users' => $users,
            'allRoles' => Role::orderBy('name')->get(),
            'allPermissions' => Permission::orderBy('name')->get(),
        ]);
    }

    public function attachRole(Request $request, User $user)
    {
        $data = Validator::make($request->all(), [
            'role' => ['required','string','exists:roles,name'],
        ])->validate();

        $user->assignRole($data['role']);

        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        return back()->with('status', "Role '{$data['role']}' assigned to {$user->email}.");
    }

    public function detachRole(User $user, Role $role)
    {
        $user->removeRole($role);

        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        return back()->with('status', "Role '{$role->name}' removed from {$user->email}.");
    }

    public function givePermission(Request $request, User $user)
    {
        $data = Validator::make($request->all(), [
            'permission' => ['required','string','exists:permissions,name'],
        ])->validate();

        $user->givePermissionTo($data['permission']);

        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        return back()->with('status', "Permission '{$data['permission']}' given to {$user->email}.");
    }

    public function revokePermission(User $user, Permission $permission)
    {
        $user->revokePermissionTo($permission);

        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        return back()->with('status', "Permission '{$permission->name}' revoked from {$user->email}.");
    }
}
