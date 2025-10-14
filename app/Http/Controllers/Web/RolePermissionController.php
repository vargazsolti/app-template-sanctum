<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionController extends Controller
{
    public function index()
    {
        return view('admin.roles_permissions.index', [
            'roles' => Role::with('permissions')->orderBy('name')->get(),
            'permissions' => Permission::orderBy('name')->get(),
            'allPermissions' => Permission::orderBy('name')->get(),
        ]);
    }

    public function storeRole(Request $request)
    {
        $data = Validator::make($request->all(), [
            'name' => ['required','string','max:100','unique:roles,name'],
        ])->validate();

        $role = Role::create(['name' => $data['name']]);
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        activity()
            ->performedOn($role)
            ->causedBy($request->user())
            ->withProperties(['ip'=>$request->ip(),'user_agent'=>$request->userAgent()])
            ->log('role_created');

        return back()->with('status', 'Role created.');
    }

    public function destroyRole(Role $role)
    {
        $role->delete();
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        activity()->performedOn($role)->causedBy(auth()->user())
            ->withProperties(['ip'=>request()->ip(),'user_agent'=>request()->userAgent()])
            ->log('role_deleted');

        return back()->with('status', 'Role deleted.');
    }

    public function storePermission(Request $request)
    {
        $data = Validator::make($request->all(), [
            'name' => ['required','string','max:150','unique:permissions,name'],
        ])->validate();

        $perm = Permission::create(['name' => $data['name']]);
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        activity()->performedOn($perm)->causedBy($request->user())
            ->withProperties(['ip'=>$request->ip(),'user_agent'=>$request->userAgent()])
            ->log('permission_created');

        return back()->with('status', 'Permission created.');
    }

    public function destroyPermission(Permission $permission)
    {
        $permission->delete();
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        activity()->performedOn($permission)->causedBy(auth()->user())
            ->withProperties(['ip'=>request()->ip(),'user_agent'=>request()->userAgent()])
            ->log('permission_deleted');

        return back()->with('status', 'Permission deleted.');
    }

    public function attachPermissionToRole(Request $request, Role $role)
    {
        $data = Validator::make($request->all(), [
            'permission' => ['required','string','exists:permissions,name'],
        ])->validate();

        $role->givePermissionTo($data['permission']);
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        activity()->performedOn($role)->causedBy($request->user())
            ->withProperties([
                'ip'=>$request->ip(),
                'user_agent'=>$request->userAgent(),
                'permission'=>$data['permission']
            ])->log('role_permission_attached');

        return back()->with('status', "Permission '{$data['permission']}' assigned to role '{$role->name}'.");
    }

    public function detachPermissionFromRole(Role $role, Permission $permission)
    {
        $role->revokePermissionTo($permission);
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        activity()->performedOn($role)->causedBy(auth()->user())
            ->withProperties([
                'ip'=>request()->ip(),
                'user_agent'=>request()->userAgent(),
                'permission'=>$permission->name
            ])->log('role_permission_detached');

        return back()->with('status', "Permission '{$permission->name}' revoked from role '{$role->name}'.");
    }
}
