<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

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
Route::get('/run-migrations', function () {
    return Artisan::call('migrate', ["--path" => "database/migrations", "--force" => true]);
});

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

	/* access users */
	Route::get('/access-users/role', 'App\Http\Controllers\Superadmin\AccessUsersController@role')->name('backend.access-users.role');
	Route::get('/access-users/add-role', 'App\Http\Controllers\Superadmin\AccessUsersController@add_role')->name('backend.access-users.add-role');
	Route::get('/access-users/edit-role/{id}', 'App\Http\Controllers\Superadmin\AccessUsersController@add_role')->name('backend.access-users.edit-role');
	Route::get('/access-users/delete-role/{id}', 'App\Http\Controllers\Superadmin\AccessUsersController@delete_role')->name('backend.access-users.delete-role');
	Route::post('/access-users/save-role', 'App\Http\Controllers\Superadmin\AccessUsersController@save_role')->name('backend.access-users.save-role');
	Route::get('/access-users/admin-management', 'App\Http\Controllers\Superadmin\AccessUsersController@admin_management')->name('backend.access-users.admin-management');
	Route::get('/access-users/add-admin', 'App\Http\Controllers\Superadmin\AccessUsersController@add_admin')->name('backend.access-users.add-admin');
	Route::get('/access-users/edit-admin/{id}', 'App\Http\Controllers\Superadmin\AccessUsersController@add_admin')->name('backend.access-users.edit-admin');
	Route::get('/access-users/delete-admin/{id}', 'App\Http\Controllers\Superadmin\AccessUsersController@delete_admin')->name('backend.access-users.delete-admin');
	Route::post('/access-users/save-admin', 'App\Http\Controllers\Superadmin\AccessUsersController@save_admin')->name('backend.access-users.save-admin');

	/* Monitoring client */
	Route::get('/monitoring/list', 'App\Http\Controllers\Superadmin\MonitoringClientController@list')->name('backend.monitoring.list');
	Route::get('/monitoring/detail/{id}', 'App\Http\Controllers\Superadmin\MonitoringClientController@detail')->name('backend.monitoring.detail');

	Route::get('/not-found', 'App\Http\Controllers\Superadmin\DashboardController@not_found')->name('backend.not-found');
});

/* ADMINUSER AUTH */
Route::group(['middleware' => 'auth'], function () {
	/* Clientuser */
	Route::get('/users/list', 'App\Http\Controllers\Adminuser\AccessUsersController@index')->name('adminuser.access-users.list');
	Route::post('/users/invite', 'App\Http\Controllers\Adminuser\AccessUsersController@create_user')->name('adminuser.access-users.create');
	Route::post('/users/move-group', 'App\Http\Controllers\Adminuser\AccessUsersController@move_group')->name('adminuser.access-users.move-group');
	Route::get('/users/resend-email/{encodedEmail}', 'App\Http\Controllers\Adminuser\AccessUsersController@resend_email')->name('adminuser.access-users.resend-email');
	Route::get('users/disable-user/{encodedEmail}','App\Http\Controllers\Adminuser\AccessUsersController@disable_user')->name('adminuser.access-users.disable-user');
	Route::get('users/enable-user/{encodedEmail}','App\Http\Controllers\Adminuser\AccessUsersController@enable_user')->name('adminuser.access-users.enable-user');

	/* Document */
	Route::get('documents/list', 'App\Http\Controllers\Adminuser\DocumentController@index')->name('adminuser.documents.list');
});


Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Auth::routes(['verify' => true]);
Route::group(['middleware' => ['auth', 'verified']], function () {
	Route::get('/project/create-new-project', 'App\Http\Controllers\Adminuser\FirstProjectController@create_first_project')->name('create-new-project');
	Route::post('/project/save-first-project', 'App\Http\Controllers\Adminuser\FirstProjectController@save_first_project')->name('project.save-first-project');
	Route::group(['middleware' => ['verify_project']], function () {
		Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
		Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
		Route::post('change-main-project', 'App\Http\Controllers\Adminuser\ProjectController@change_main_project')->name('project.change-main-project');
		
		/* Companies */
		Route::get('/company/list', 'App\Http\Controllers\Adminuser\CompanyController@index')->name('company.list-company');
		Route::get('/company/detail/{id}', 'App\Http\Controllers\Adminuser\CompanyController@detail_company')->name('company.detail-company');
		Route::post('/company/save', 'App\Http\Controllers\Adminuser\CompanyController@save')->name('company.save-company');
		Route::get('/company/delete/{id}', 'App\Http\Controllers\Adminuser\CompanyController@delete')->name('company.delete-company');
		Route::get('/company/disable/{id}', 'App\Http\Controllers\Adminuser\CompanyController@disable_company')->name('company.disable-company');
		Route::post('/company/get-detail-company', 'App\Http\Controllers\Adminuser\CompanyController@get_detail_company')->name('company.get-detail-company');

		/* Project */
		Route::get('/project/list-project', 'App\Http\Controllers\Adminuser\ProjectController@list_project')->name('project.list-project');
		Route::get('/project/detail-project/{id}', 'App\Http\Controllers\Adminuser\ProjectController@detail_project')->name('project.detail-project');
		Route::post('/project/detail-role-users', 'App\Http\Controllers\Adminuser\ProjectController@detail_role_users')->name('project.detail-role-users');
		Route::get('/project/create-project', 'App\Http\Controllers\Adminuser\ProjectController@create_project')->name('project.create-project');
		Route::get('/project/edit-project/{id}', 'App\Http\Controllers\Adminuser\ProjectController@create_project')->name('project.edit-project');
		Route::post('/project/save-project', 'App\Http\Controllers\Adminuser\ProjectController@save_project')->name('project.save-project');
		Route::get('/project/delete-project/{id}', 'App\Http\Controllers\Adminuser\ProjectController@delete_project')->name('project.delete-project');
	});
});