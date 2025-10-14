<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\UserAdminController;

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


     Route::get('/admin/users', [UserAdminController::class, 'index'])
        ->middleware('permission:users.read')
        ->name('admin.users.index');

    // A token kiadás is auth + users.read alatt (UI csak olvasáshoz is kellhet token az API híváshoz)
    Route::get('/admin/users/ui-token', [UserAdminController::class, 'issueToken'])
        ->middleware('permission:users.read')
        ->name('admin.users.ui-token');


});

require __DIR__.'/auth.php';
