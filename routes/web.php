<?php

use Illuminate\Support\Facades\Route;
use App\Models\Country;
use App\Models\PropertyType;

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


Auth::routes();
Route::get('/', 'HomeController@index')->name('home');

//Assets
Route::resource("assets","AssetController");
Route::get('assets.datatable', 'AssetController@datatable')->name('assets.datatable');
Route::get('assets.load/{id}', 'AssetController@load')->name('properties.load');
Route::get('assets.status/{id}', 'AssetController@status')->name('assets.status');
Route::get('assets.rpt_assets', 'AssetController@rpt_assets')->name('assets.rpt_assets');

//Condominiums
Route::resource("condominiums","CondominiumController");
Route::get('demos', 'CondominiumController@demos')->name('demos');
Route::post('condominiums.datatable', 'CondominiumController@datatable')->name('condominiums.datatable');
Route::get('condominiums.load/{id}', 'CondominiumController@load')->name('condominiums.load');
Route::get('condominiums.status/{id}', 'CondominiumController@status')->name('condominiums.status');
Route::get('condominiums.permanent/{id}', 'CondominiumController@permanent')->name('condominiums.permanent');
Route::get('condominiums.rpt_condominiums', 'CondominiumController@rpt_condominiums')->name('condominiums.rpt_condominiums');
Route::get('condominiums.rpt_demos', 'CondominiumController@rpt_demos')->name('condominiums.rpt_demos');
Route::post('condominiums/{id}/events', 'CondominiumController@events');

//Employees
Route::resource("employees","EmployeeController");
Route::post('employees.datatable', 'EmployeeController@datatable')->name('employees.datatable');
Route::get('employees.load/{id}', 'EmployeeController@load')->name('employees.load');
Route::get('employees.status/{id}', 'EmployeeController@status')->name('employees.status');
Route::get('employees.rpt_employees', 'EmployeeController@rpt_employees')->name('employees.rpt_employees');

//Expenses
Route::resource("expenses","ExpenseController");
Route::post('expenses.datatable', 'ExpenseController@datatable')->name('expenses.datatable');
Route::get('expenses.load/{id}', 'ExpenseController@load')->name('expenses.load');
Route::post('expenses.rpt_expenses', 'ExpenseController@rpt_expenses')->name('expenses.rpt_expenses');
Route::get('expenses.download/{id}', ['as' => 'expenses.download', 'uses' => 'ExpenseController@download_file']);

//ExpenseTypes
Route::resource("expense_types","ExpenseTypeController");
Route::get('expense_types.datatable', 'ExpenseTypeController@datatable')->name('expense_types.datatable');
Route::get('expense_types.load/{id}', 'ExpenseTypeController@load')->name('expense_types.load');
Route::get('expense_types.status/{id}', 'ExpenseTypeController@status')->name('expense_types.status');
Route::get('expense_types.rpt_expense_types', 'ExpenseTypeController@rpt_expense_types')->name('expense_types.rpt_expense_types');


//Home
//Route::get('/', 'HomeController@index');
//*** Home ***
Route::get('home', ['as' => 'home', 'uses' => 'HomeController@index']);

//Img
Route::get('company_logo/{id}', 'ImgController@showCompanyLogo');
Route::get('user_avatar/{id}', 'ImgController@showUserAvatar');
Route::get('contact_avatar/{id}', 'ImgController@showContactAvatar');
Route::get('employee_avatar/{id}', 'ImgController@showEmployeeAvatar');
Route::get('condominium_logo/{id}', 'ImgController@showCondominiumLogo');
Route::get('document_image/{id}', 'ImgController@showDocumentImage')->name('document_image');
Route::get('income_image/{id}', 'ImgController@showIncomeImage')->name('income_image');
Route::get('expense_image/{id}', 'ImgController@showExpenseImage')->name('expense_image');
Route::get('payment_image/{id}', 'ImgController@showPaymentImage')->name('payment_image');
Route::get('transfer_image/{id}', 'ImgController@showTransferImage')->name('transfer_image');
Route::get('facility_photo/{id}', 'ImgController@showFacilityPhoto');
Route::get('newsletter_image/{id}', 'ImgController@showNewsletterImage')->name('newsletter_image');
Route::get('visit_image/{id}', 'ImgController@showVisitImage')->name('visit_image');

//Profile
Route::get('profile', ['as' => 'profile', 'uses' => 'ProfileController@edit']);
Route::post('profile.update', ['as' => 'profile.update', 'uses' => 'ProfileController@update']);

//Setting
Route::get('global', ['as' => 'global', 'uses' => 'SettingController@global']);
Route::post('settings.update_global', ['as' => 'settings.update_global', 'uses' => 'SettingController@update_global']);
Route::get('settings', ['as' => 'settings', 'uses' => 'SettingController@condominium']);
Route::post('settings.update_condominium', ['as' => 'settings.update_condominium', 'uses' => 'SettingController@update_condominium']);

//Users
Route::resource("users","UserController");
Route::get('users.datatable', 'UserController@datatable')->name('users.datatable');
Route::get('users.load/{id}', 'UserController@load')->name('users.load');
Route::get('users.status/{id}', 'UserController@status')->name('users.status');
Route::get('users.rpt_users', 'UserController@rpt_users')->name('users.rpt_users');


