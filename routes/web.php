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

/*Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');*/

//Landing
Route::get('/', function () {
    $countries=Country::orderBy('name')->pluck('name','id');
    $property_types=PropertyType::orderBy('name')->pluck('name','id');

    return view('landing')->with('messages', array())
    					  ->with('countries', $countries)
    					  ->with('property_types', $property_types);
});

//Accounts
Route::resource("accounts","AccountController");
Route::get('accounts.datatable', 'AccountController@datatable')->name('accounts.datatable');
Route::get('accounts.load/{id}', 'AccountController@load')->name('properties.load');
Route::get('accounts.status/{id}', 'AccountController@status')->name('accounts.status');
Route::get('accounts.rpt_accounts', 'AccountController@rpt_accounts')->name('accounts.rpt_accounts');

//Assets
Route::resource("assets","AssetController");
Route::get('assets.datatable', 'AssetController@datatable')->name('assets.datatable');
Route::get('assets.load/{id}', 'AssetController@load')->name('properties.load');
Route::get('assets.status/{id}', 'AssetController@status')->name('assets.status');
Route::get('assets.rpt_assets', 'AssetController@rpt_assets')->name('assets.rpt_assets');

//Cars
Route::resource("cars","CarController");
Route::get('cars.datatable', 'CarController@datatable')->name('cars.datatable');
Route::get('cars.load/{id}', 'CarController@load')->name('properties.load');
Route::get('cars.status/{id}', 'CarController@status')->name('cars.status');
Route::get('cars.rpt_cars', 'CarController@rpt_cars')->name('cars.rpt_cars');

//Contacts
Route::resource("contacts","ContactController");
Route::post('contacts.datatable', 'ContactController@datatable')->name('contacts.datatable');
Route::get('contacts.load/{id}', 'ContactController@load')->name('contacts.load');
Route::get('contacts.status/{id}', 'ContactController@status')->name('contacts.status');
Route::get('contacts.rpt_contacts', 'ContactController@rpt_contacts')->name('contacts.rpt_contacts');

//DocumentTypes
Route::resource("document_types","DocumentTypeController");
Route::get('document_types.datatable', 'DocumentTypeController@datatable')->name('document_types.datatable');
Route::get('document_types.load/{id}', 'DocumentTypeController@load')->name('document_types.load');
Route::get('document_types.status/{id}', 'DocumentTypeController@status')->name('document_types.status');
Route::get('document_types.rpt_document_types', 'DocumentTypeController@rpt_document_types')->name('document_types.rpt_document_types');

//Document
Route::resource("documents","DocumentController");
Route::post('documents.datatable', 'DocumentController@datatable')->name('documents.datatable');
Route::get('documents.load/{id}', 'DocumentController@load')->name('document_types.load');
Route::get('documents.visibility/{id}', 'DocumentController@visibility')->name('documents.visibility');
Route::get('documents.rpt_documents', 'DocumentController@rpt_documents')->name('documents.rpt_documents');
Route::get('documents.download/{id}', ['as' => 'documents.download', 'uses' => 'DocumentController@download_file']);

//Employees
Route::resource("employees","EmployeeController");
Route::post('employees.datatable', 'EmployeeController@datatable')->name('employees.datatable');
Route::get('employees.load/{id}', 'EmployeeController@load')->name('employees.load');
Route::get('employees.status/{id}', 'EmployeeController@status')->name('employees.status');
Route::get('employees.rpt_employees', 'EmployeeController@rpt_employees')->name('employees.rpt_employees');

//ExpenseTypes
Route::resource("expense_types","ExpenseTypeController");
Route::get('expense_types.datatable', 'ExpenseTypeController@datatable')->name('expense_types.datatable');
Route::get('expense_types.load/{id}', 'ExpenseTypeController@load')->name('expense_types.load');
Route::get('expense_types.status/{id}', 'ExpenseTypeController@status')->name('expense_types.status');
Route::get('expense_types.rpt_expense_types', 'ExpenseTypeController@rpt_expense_types')->name('expense_types.rpt_expense_types');

//Facilities
Route::resource("facilities","FacilityController");
Route::get('facilities.datatable', 'FacilityController@datatable')->name('facilities.datatable');
Route::get('facilities.load/{id}', 'FacilityController@load')->name('facilities.load');
Route::get('facilities.status/{id}', 'FacilityController@status')->name('facilities.status');
Route::get('facilities.rpt_facilities', 'FacilityController@rpt_facilities')->name('facilities.rpt_facilities');
Route::post('facilities/{id}/reservations', 'FacilityController@reservations');


//Home
//Route::get('/', 'HomeController@index');
Route::get('home', ['as' => 'home', 'uses' => 'HomeController@index']);

//Img
Route::get('user_avatar/{id}', 'ImgController@showUserAvatar');
Route::get('contact_avatar/{id}', 'ImgController@showContactAvatar');
Route::get('employee_avatar/{id}', 'ImgController@showEmployeeAvatar');
Route::get('condominium_logo/{id}', 'ImgController@showCondominiumLogo');
Route::get('document_photo/{id}', 'ImgController@showDocumentPhoto')->name('document_photo');
Route::get('facility_photo/{id}', 'ImgController@showFacilityPhoto');


//IncomeTypes
Route::resource("income_types","IncomeTypeController");
Route::get('income_types.datatable', 'IncomeTypeController@datatable')->name('income_types.datatable');
Route::get('income_types.load/{id}', 'IncomeTypeController@load')->name('income_types.load');
Route::get('income_types.status/{id}', 'IncomeTypeController@status')->name('income_types.status');
Route::get('income_types.rpt_income_types', 'IncomeTypeController@rpt_income_types')->name('income_types.rpt_income_types');

//Owners
Route::resource("owners","OwnerController");
Route::post('owners.status', ['as' => 'owners.status', 'uses' => 'OwnerController@status']);
Route::get('owners.datatable', 'OwnerController@datatable')->name('owners.datatable');
Route::get('owners.load/{id}', 'OwnerController@load')->name('owners.load');
Route::get('owners.rpt_owners', 'OwnerController@rpt_owners')->name('owners.rpt_owners');
Route::get('owners.xls_owners', 'OwnerController@xls_owners')->name('owners.xls_owners');

//Properties
Route::resource("properties","PropertyController");
Route::get('properties.datatable', 'PropertyController@datatable')->name('properties.datatable');
Route::get('properties.load/{id}', 'PropertyController@load')->name('properties.load');
Route::get('properties.rpt_properties', 'PropertyController@rpt_properties')->name('properties.rpt_properties');
Route::get('properties.xls_properties', 'PropertyController@xls_properties')->name('properties.xls_properties');
Route::get('taxes', 'PropertyController@taxes')->name('properties.taxes');
Route::post('properties.update_taxes', 'PropertyController@update_taxes')->name('properties.update_taxes');

//Reservations
Route::resource("reservations","ReservationController");
Route::post('reservations.datatable', 'ReservationController@datatable')->name('reservations.datatable');
Route::get('reservations.load/{id}', 'ReservationController@load')->name('reservations.load');
Route::get('reservations.status/{id}', 'ReservationController@status')->name('reservations.status');
Route::get('reservations.rpt_reservation', 'ReservationController@rpt_reservation')->name('reservations.rpt_reservation');
Route::post('reserve/{id}', 'ReservationController@reserve')->name('reserve');


//Setting
Route::get('settings.global', ['as' => 'settings.global', 'uses' => 'SettingController@global']);
Route::post('settings.update_global', ['as' => 'settings.update_global', 'uses' => 'SettingController@update_global']);
Route::get('settings.condominium', ['as' => 'settings.condominium', 'uses' => 'SettingController@condominium']);
Route::post('settings.update_condominium', ['as' => 'settings.update_condominium', 'uses' => 'SettingController@update_condominium']);

//SupplierCategories
Route::resource("supplier_categories","SupplierCategoryController");
Route::get('supplier_categories.datatable', 'SupplierCategoryController@datatable')->name('supplier_categories.datatable');
Route::get('supplier_categories.load/{id}', 'SupplierCategoryController@load')->name('supplier_categories.load');
Route::get('supplier_categories.status/{id}', 'SupplierCategoryController@status')->name('supplier_categories.status');
Route::get('supplier_categories.rpt_supplier_categories', 'SupplierCategoryController@rpt_supplier_categories')->name('supplier_categories.rpt_supplier_categories');

//Suppliers
Route::resource("suppliers","SupplierController");
Route::post('suppliers.datatable', 'SupplierController@datatable')->name('suppliers.datatable');
Route::get('suppliers.load/{id}', 'SupplierController@load')->name('suppliers.load');
Route::get('suppliers.status/{id}', 'SupplierController@status')->name('suppliers.status');
Route::get('suppliers.rpt_suppliers', 'SupplierController@rpt_suppliers')->name('suppliers.rpt_suppliers');

//Tools
Route::get('get_states/{id}', 'ToolController@get_states');

//*** Home ***
Auth::routes();

