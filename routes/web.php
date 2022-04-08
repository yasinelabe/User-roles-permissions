<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// not authorized route
Route::get('/not_authorized', function () {
    return view('not_authorized');
})->name('not_authorized');

// auth routes
Route::controller(AuthController::class)->group(function () {
    Route::get('/login', 'login')->name('login');
    Route::post('/verify_login', 'postLogin');
    Route::get('/logout', 'logout');
});

// Dashboard routes
Route::get('/', DashboardController::class . '@dashboard')->name('home');


// User routes
Route::controller(UserController::class)->prefix('users')->name('users')->group(function () {
    Route::get('/', 'index')->name('.index');
    Route::get('/create', 'create')->name('.create');
    Route::post('/store', 'store')->name('.store');
    Route::get('/{user}', 'show')->name('.show');
    Route::get('/{user}/edit', 'edit')->name('.edit');
    Route::post('/{user}/update', 'update')->name('.update');
    Route::get('/{user}/delete', 'destroy')->name('.delete');
});

// Roles routes
Route::controller(RolesController::class)->prefix('roles')->name('roles')->group(function () {
    Route::get('/', 'index')->name('.index');
    Route::get('/create', 'create')->name('.create');
    Route::post('/store', 'store')->name('.store');
    Route::get('/{role}', 'show')->name('.show');
    Route::get('/{role}/edit', 'edit')->name('.edit');
    Route::post('/{role}/update', 'update')->name('.update');
    Route::get('/{role}/delete', 'destroy')->name('.delete');
});

// Role permissions routes
Route::controller(RolePermissionController::class)->prefix('role_permissions')->name('role_permissions')->group(function () {
    Route::get('/{role}', 'index')->name('.index');
    Route::post('/{role}/store', 'store')->name('.store');
});