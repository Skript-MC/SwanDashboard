<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ConfigController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SentryController;
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

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('profile', [ProfileController::class, 'home'])->name('profile');

Route::get('users', [UserController::class, 'index'])->name('users');
Route::get('users/{id}', [UserController::class, 'edit'])->name('user-edit');
Route::post('users/{id}/edit', [UserController::class, 'postEdit']);

Route::get('sentry', [SentryController::class, 'index'])->name('sentry');

Route::get('config/dashboard', [ConfigController::class, 'dashboard'])->name('dashboard-config');
Route::post('config/dashboard/edit', [ConfigController::class, 'postDashboard']);

Route::get('login', [AuthController::class, 'redirect']);
Route::get('login/callback', [AuthController::class, 'callback']);
Route::get('logout', [AuthController::class, 'logout']);

