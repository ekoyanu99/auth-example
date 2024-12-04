<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

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

    Route::resource('users', App\Http\Controllers\UserController::class);
    Route::get('users/{userId}/delete', [App\Http\Controllers\UserController::class, 'destroy']);

    Route::get('/user/json', [\App\Http\Controllers\UserController::class, 'data'])->name('user.data');

    // Route::post('/user/json', [\App\Http\Controllers\UserController::class, 'data'])->name('user.data');
});


Route::middleware(['auth', 'role:superadmin|admin'])->group(function () {

    Route::resource('permissions', App\Http\Controllers\PermissionController::class);
    Route::get('permissions/{permissionId}/delete', [App\Http\Controllers\PermissionController::class, 'destroy']);

    Route::resource('roles', App\Http\Controllers\RoleController::class);
    Route::get('roles/{roleId}/delete', [App\Http\Controllers\RoleController::class, 'destroy']);
    // ->middleware('permission:delete');

    Route::get('roles/{roleId}/give-permission', [App\Http\Controllers\RoleController::class, 'addPermissionToRole']);
    Route::put('roles/{roleId}/give-permission', [App\Http\Controllers\RoleController::class, 'givePermissionToRole']);
});

// Route::resource('permissions', App\Http\Controllers\PermissionController::class)->middleware(['auth', 'verified']);
// Route::get('permissions/{permissionId}/delete', [App\Http\Controllers\PermissionController::class, 'destroy'])->middleware(['auth', 'verified']);

// Route::resource('roles', App\Http\Controllers\RoleController::class)->middleware(['auth', 'verified']);
// Route::get('roles/{roleId}/delete', [App\Http\Controllers\RoleController::class, 'destroy'])->middleware(['auth', 'verified']);

require __DIR__ . '/auth.php';
