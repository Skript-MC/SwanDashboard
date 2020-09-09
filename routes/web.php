<?php

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

Route::get('/', 'HomeController@index')->name('home');

Route::get('profile', 'ProfileController@self')->name('profile');

Route::get('users', 'UserController@index')->name('users');
Route::get('users/{id}', 'UserController@edit')->name('user-edit');
Route::post('users/{id}/edit', 'UserController@postEdit');

Route::get('sentry', 'SentryController@index')->name('sentry');

Route::get('config/dashboard', 'ConfigController@dashboard')->name('dashboard-config');
Route::post('config/dashboard/edit', 'ConfigController@postDashboard');

Route::get('login', 'AuthController@redirect');
Route::get('login/callback', 'AuthController@callback');
Route::get('logout', 'AuthController@logout');

