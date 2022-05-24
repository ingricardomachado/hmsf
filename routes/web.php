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
Route::get('home', ['as' => 'home', 'uses' => 'HomeController@index']);

Route::get('load_graph_om/{year}/{month}', ['as' => 'load_graph_om', 'uses' => 'HomeController@load_graph_om']);
Route::get('load_graph_mm/{year}/{month}', ['as' => 'load_graph_mm', 'uses' => 'HomeController@load_graph_mm']);
Route::get('load_graph_oy/{year}', ['as' => 'load_graph_oy', 'uses' => 'HomeController@load_graph_oy']);
Route::get('load_graph_my/{year}', ['as' => 'load_graph_my', 'uses' => 'HomeController@load_graph_my']);



//Centers
Route::resource("centers","CenterController");
Route::post('centers.datatable', 'CenterController@datatable')->name('centers.datatable');
Route::get('centers.load/{id}', 'CenterController@load')->name('centers.load');
Route::get('centers.status/{id}', 'CenterController@status')->name('centers.status');
Route::get('centers.rpt_centers', 'CenterController@rpt_centers')->name('centers.rpt_centers');

//Comments
Route::resource("comments","CommentController");
Route::get('comments.index/{id}', 'CommentController@index')->name('comment.index');

//Companies
Route::resource("companies","CompanyController");
Route::get('companies.datatable', 'CompanyController@datatable')->name('companies.datatable');
Route::get('companies.load/{id}', 'CompanyController@load')->name('companies.load');
Route::get('companies.status/{id}', 'CompanyController@status')->name('companies.status');
Route::get('companies.rpt_companies', 'CompanyController@rpt_companies')->name('companies.rpt_companies');

//Customers
Route::resource("customers","CustomerController");
Route::post('customers.datatable', 'CustomerController@datatable')->name('customers.datatable');
Route::get('customers.load/{id}', 'CustomerController@load')->name('customers.load');
Route::get('customers.status/{id}', 'CustomerController@status')->name('customers.status');
Route::get('customers.rpt_customers', 'CustomerController@rpt_customers')->name('customers.rpt_customers');


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

//Operations
Route::resource("operations","OperationController");
Route::post('operations.datatable', 'OperationController@datatable')->name('operations.datatable');
Route::get('operations.load/{id}', 'OperationController@load')->name('operations.load');
Route::get('operations.status/{id}', 'OperationController@status')->name('operations.status');
Route::post('operations.rpt_operations', 'OperationController@rpt_operations')->name('operations.rpt_operations');

Route::get('operations.load_status/{id}', 'OperationController@load_status')->name('operations.load_status');
Route::post('operations.status/{id}', 'OperationController@status')->name('operations.status');
Route::get('operations.load_comments/{id}', 'OperationController@load_comments')->name('operations.load_comments');


//Partner
Route::resource("partners","PartnerController");
Route::get('partners.datatable', 'PartnerController@datatable')->name('partners.datatable');
Route::get('partners.load/{id}', 'PartnerController@load')->name('partners.load');
Route::get('partners.status/{id}', 'PartnerController@status')->name('partners.status');
Route::get('partners.rpt_partners', 'PartnerController@rpt_partners')->name('partners.rpt_partners');
Route::get('partners.upload_xls', 'PartnerController@upload_xls')->name('partners.upload_xls');

//Profile
Route::get('profile', ['as' => 'profile', 'uses' => 'ProfileController@edit']);
Route::post('profile.update', ['as' => 'profile.update', 'uses' => 'ProfileController@update']);

//Setting
Route::get('settings', ['as' => 'settings', 'uses' => 'SettingController@index']);
Route::post('settings.update', ['as' => 'settings.update', 'uses' => 'SettingController@update']);

//Users
Route::resource("users","UserController");
Route::get('users.datatable', 'UserController@datatable')->name('users.datatable');
Route::get('users.load/{id}', 'UserController@load')->name('users.load');
Route::get('users.status/{id}', 'UserController@status')->name('users.status');
Route::get('users.rpt_users', 'UserController@rpt_users')->name('users.rpt_users');

//Fix
Route::get('upload_partners', 'FixController@upload_partners')->name('upload_partners');
Route::get('upload_customers', 'FixController@upload_customers')->name('upload_customers');
Route::get('upload_companies', 'FixController@upload_companies')->name('upload_companies');
Route::get('upload_operations', 'FixController@upload_operations')->name('upload_operations');

