<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\UserAdminController;
use App\Http\Controllers\Web\RolePermissionController;
use App\Http\Controllers\Web\UserAccessController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

});

Route::middleware(['auth', 'permission:admin.access'])->group(function () {
    // Users (Blade admin UI + UI token)
    Route::get('/admin/users', [UserAdminController::class, 'index'])
        ->name('admin.users.index');
    Route::get('/admin/users/ui-token', [UserAdminController::class, 'issueToken'])
        ->name('admin.users.ui-token');

    // Roles & Permissions page
    Route::get('/admin/access/roles-permissions', [RolePermissionController::class, 'index'])
        ->name('admin.access.roles-permissions');

    // Roles CRUD
    Route::post('/admin/access/roles', [RolePermissionController::class, 'storeRole'])
        ->name('admin.access.roles.store');
    Route::delete('/admin/access/roles/{role}', [RolePermissionController::class, 'destroyRole'])
        ->name('admin.access.roles.destroy');

    // Permissions CRUD  🔴 ez hiányzik nálad most
    Route::post('/admin/access/permissions', [RolePermissionController::class, 'storePermission'])
        ->name('admin.access.permissions.store');
    Route::delete('/admin/access/permissions/{permission}', [RolePermissionController::class, 'destroyPermission'])
        ->name('admin.access.permissions.destroy');

    // Role ↔ Permission assign/revoke
    Route::post('/admin/access/roles/{role}/permissions', [RolePermissionController::class, 'attachPermissionToRole'])
        ->name('admin.access.roles.permissions.attach');
    Route::delete('/admin/access/roles/{role}/permissions/{permission}', [RolePermissionController::class, 'detachPermissionFromRole'])
        ->name('admin.access.roles.permissions.detach');

    // Users ↔ Roles/Permissions
    Route::get('/admin/access/users', [UserAccessController::class, 'index'])
        ->name('admin.access.users');
    Route::post('/admin/access/users/{user}/roles', [UserAccessController::class, 'attachRole'])
        ->name('admin.access.users.roles.attach');
    Route::delete('/admin/access/users/{user}/roles/{role}', [UserAccessController::class, 'detachRole'])
        ->name('admin.access.users.roles.detach');
    Route::post('/admin/access/users/{user}/permissions', [UserAccessController::class, 'givePermission'])
        ->name('admin.access.users.permissions.give');
    Route::delete('/admin/access/users/{user}/permissions/{permission}', [UserAccessController::class, 'revokePermission'])
        ->name('admin.access.users.permissions.revoke');
});


require __DIR__.'/auth.php';
