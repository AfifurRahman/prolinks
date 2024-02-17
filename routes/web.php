<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes(['verify' => true]);

/* SUPERADMIN */
Route::get('/backend/login', 'App\Http\Controllers\Superadmin\Auth\LoginController@index')->name('backend-login');
Route::post('/backend/process-login', 'App\Http\Controllers\Superadmin\Auth\LoginController@process_login')->name('process-login-backend');
Route::post('/backend/logout', 'App\Http\Controllers\Superadmin\Auth\LoginController@logout')->name('backend-logout');
Route::get('/create-password/{token}', 'App\Http\Controllers\Adminuser\Auth\AuthController@create_password')->name('create-password');
Route::post('/save-password/{token}/{email}', 'App\Http\Controllers\Adminuser\Auth\AuthController@save_password')->name('create-new-password');

/* SUPERADMIN AUTH*/
Route::group(['middleware' => 'auth_backend', 'prefix' => 'backend'], function () {
	Route::get('/dashboard/', 'App\Http\Controllers\Superadmin\DashboardController@dashboard')->name('backend.dashboard');
	Route::get('/profile', 'App\Http\Controllers\Superadmin\ProfileController@profile')->name('backend.profile');
	Route::post('/save-profile', 'App\Http\Controllers\Superadmin\ProfileController@save_profile')->name('backend.save-profile');
	Route::post('/change-password', 'App\Http\Controllers\Superadmin\ProfileController@change_password')->name('backend.change-password');

	/* pricing */
	Route::get('/pricing/list', 'App\Http\Controllers\Superadmin\PricingController@index')->name('backend.pricing.list');
	Route::get('/pricing/add', 'App\Http\Controllers\Superadmin\PricingController@add')->name('backend.pricing.add');
	Route::get('/pricing/edit/{id}', 'App\Http\Controllers\Superadmin\PricingController@add')->name('backend.pricing.edit');
	Route::post('/pricing/save', 'App\Http\Controllers\Superadmin\PricingController@save')->name('backend.pricing.save');
	Route::get('/pricing/delete/{id}', 'App\Http\Controllers\Superadmin\PricingController@delete')->name('backend.pricing.delete');

	/* client */
	Route::get('/client/list', 'App\Http\Controllers\Superadmin\ClientController@index')->name('backend.client.list');
	Route::get('/client/add', 'App\Http\Controllers\Superadmin\ClientController@add')->name('backend.client.add');
	Route::get('/client/edit/{id}', 'App\Http\Controllers\Superadmin\ClientController@add')->name('backend.client.edit');
	Route::post('/client/save', 'App\Http\Controllers\Superadmin\ClientController@save')->name('backend.client.save');
	Route::get('/client/delete/{id}', 'App\Http\Controllers\Superadmin\ClientController@delete')->name('backend.client.delete');
	Route::get('/client/send-email/{id}', 'App\Http\Controllers\Superadmin\ClientController@send_email')->name('backend.client.send-email');
});

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
