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
Route::get('accounts.statement/{id}', 'AccountController@statement')->name('accounts.statement');
Route::post('accounts.movements', 'AccountController@movements')->name('accounts.movements');
Route::post('accounts.xls_movements', 'AccountController@xls_movements')->name('accounts.xls_movements');
Route::post('accounts.rpt_movements', 'AccountController@rpt_movements')->name('accounts.rpt_movements');

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

//Contacts
Route::resource("contacts","ContactController");
Route::post('contacts.datatable', 'ContactController@datatable')->name('contacts.datatable');
Route::get('contacts.load/{id}', 'ContactController@load')->name('contacts.load');
Route::get('contacts.status/{id}', 'ContactController@status')->name('contacts.status');
Route::get('contacts.rpt_contacts', 'ContactController@rpt_contacts')->name('contacts.rpt_contacts');

//Countries
Route::resource("countries","CountryController");

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

//Expenses
Route::resource("expenses","ExpenseController");
Route::post('expenses.datatable', 'ExpenseController@datatable')->name('expenses.datatable');
Route::get('expenses.load/{id}', 'ExpenseController@load')->name('expenses.load');
Route::get('expenses.download/{id}', ['as' => 'expenses.download', 'uses' => 'ExpenseController@download_file']);

//ExpenseTypes
Route::resource("expense_types","ExpenseTypeController");
Route::get('expense_types.datatable', 'ExpenseTypeController@datatable')->name('expense_types.datatable');
Route::get('expense_types.load/{id}', 'ExpenseTypeController@load')->name('expense_types.load');
Route::get('expense_types.status/{id}', 'ExpenseTypeController@status')->name('expense_types.status');
Route::get('expense_types.rpt_expense_types', 'ExpenseTypeController@rpt_expense_types')->name('expense_types.rpt_expense_types');

//Events
Route::resource("events","EventController");
Route::post('events.drop/{id}', 'EventController@drop')->name('events.drop');
Route::get('events.load/{id}', 'EventController@load')->name('events.load');

//Facilities
Route::resource("facilities","FacilityController");
Route::get('facilities.datatable', 'FacilityController@datatable')->name('facilities.datatable');
Route::get('facilities.load/{id}', 'FacilityController@load')->name('facilities.load');
Route::get('facilities.status/{id}', 'FacilityController@status')->name('facilities.status');
Route::get('facilities.rpt_facilities', 'FacilityController@rpt_facilities')->name('facilities.rpt_facilities');
Route::post('facilities/{id}/reservations', 'FacilityController@reservations');

//Fees
Route::resource("fees","FeeController");
Route::post('fees.datatable', 'FeeController@datatable')->name('fees.datatable');
Route::get('fees.info/{id}', 'FeeController@info')->name('fees.info');
Route::get('fees.load/{id}', 'FeeController@load')->name('fees.load');
Route::get('fees.create_multiple', 'FeeController@create_multiple')->name('fees.create_multiple');
Route::post('fees.store_multiple', 'FeeController@store_multiple')->name('fees.store_multiple');

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

//Income
Route::resource("incomes","IncomeController");
Route::get('incomes.datatable', 'IncomeController@datatable')->name('incomes.datatable');
Route::get('incomes.load/{id}', 'IncomeController@load')->name('incomes.load');
Route::get('incomes.download/{id}', ['as' => 'incomes.download', 'uses' => 'IncomeController@download_file']);

//IncomeTypes
Route::resource("income_types","IncomeTypeController");
Route::get('income_types.datatable', 'IncomeTypeController@datatable')->name('income_types.datatable');
Route::get('income_types.load/{id}', 'IncomeTypeController@load')->name('income_types.load');
Route::get('income_types.status/{id}', 'IncomeTypeController@status')->name('income_types.status');
Route::get('income_types.rpt_income_types', 'IncomeTypeController@rpt_income_types')->name('income_types.rpt_income_types');

//Newsletters
Route::resource("newsletters","NewsletterController");
Route::post('newsletters.datatable', 'NewsletterController@datatable')->name('newsletters.datatable');
Route::get('newsletters.load/{id}', 'NewsletterController@load')->name('newsletters.load');
Route::get('newsletters.status/{id}', 'NewsletterController@status')->name('newsletters.status');
Route::get('newsletters.rpt_newsletters', 'NewsletterController@rpt_newsletters')->name('newsletters.rpt_newsletters');
Route::get('newsletters.download/{id}', ['as' => 'newsletters.download', 'uses' => 'NewsletterController@download_file']);

//Notifications
Route::get('emails', 'NotificationController@email')->name('emails');
Route::post('notifications.send_email', 'NotificationController@send_email')->name('notifications.send_email');
Route::get('billing', 'NotificationController@billing')->name('billing');

//Owners
Route::resource("owners","OwnerController");
Route::post('owners.status', ['as' => 'owners.status', 'uses' => 'OwnerController@status']);
Route::get('owners.datatable', 'OwnerController@datatable')->name('owners.datatable');
Route::get('owners.load/{id}', 'OwnerController@load')->name('owners.load');
Route::get('owners.rpt_owners', 'OwnerController@rpt_owners')->name('owners.rpt_owners');
Route::get('owners.xls_owners', 'OwnerController@xls_owners')->name('owners.xls_owners');

//Payments
Route::resource("payments","PaymentController");
Route::post('payments.datatable', 'PaymentController@datatable')->name('payments.datatable');
Route::get('payments.info/{id}', 'PaymentController@info')->name('payments.info');
Route::get('payments.load/{id}', 'PaymentController@load')->name('payments.load');
Route::get('payments.load_pending_fees/{id}', 'PaymentController@load_pending_fees')->name('payments.load_pending_fees');
Route::get('payments.download/{id}', ['as' => 'payments.download', 'uses' => 'PaymentController@download_file']);
Route::get('payments.load_confirm/{id}', 'PaymentController@load_confirm')->name('payments.load_confirm');
Route::post('payments.confirm/{id}', 'PaymentController@confirm')->name('payments.confirm');
Route::get('payments.rpt_payment/{id}', 'PaymentController@rpt_payment')->name('payments.rpt_payment');

//Posts
Route::resource("posts","PostController");
Route::get('posts.index/{id}', ['as' => 'posts.index', 'uses' => 'PostController@index']);

//Profile
Route::get('profile', ['as' => 'profile', 'uses' => 'ProfileController@edit']);
Route::post('profile.update', ['as' => 'profile.update', 'uses' => 'ProfileController@update']);

//Projects
Route::resource("projects","ProjectController");
Route::post('projects.datatable', 'ProjectController@datatable')->name('projects.datatable');
Route::get('projects.load/{id}', 'ProjectController@load')->name('projects.load');
Route::get('projects.load_progress/{id}', 'ProjectController@load_progress')->name('projects.load_progress');
Route::get('projects.load_finish/{id}', 'ProjectController@load_finish')->name('projects.load_finish');
Route::get('projects.detail/{id}', 'ProjectController@detail')->name('projects.detail');
Route::get('projects.gallery/{id}', 'ProjectController@gallery')->name('projects.gallery');
Route::post('projects.status/{id}', 'ProjectController@status')->name('projects.status');
Route::get('projects.rpt_project/{id}', 'ProjectController@rpt_project')->name('projects.rpt_project');
Route::get('projects.rpt_projects', 'ProjectController@rpt_projects')->name('projects.rpt_projects');
Route::get('projects.load_btn_status/{id}', 'ProjectController@load_btn_status')->name('projects.load_btn_status');

Route::get('projects.load_incomes/{id}', 'ProjectController@load_incomes')->name('projects.load_incomes');
Route::get('projects.load_expenses/{id}', 'ProjectController@load_expenses')->name('projects.load_expenses');

//ProjectActivities
Route::resource("project_activities","ProjectActivityController");
Route::get('project_activities.index/{id}', ['as' => 'project_activities.index', 'uses' => 'ProjectActivityController@index']);
Route::get('project_activities.load/{project}/{activity}', ['as' => 'project_activities.load', 'uses' => 'ProjectActivityController@load']);


//ProjectComments
Route::resource("project_comments","ProjectCommentController");
Route::get('project_comments.index/{id}', ['as' => 'project_comments.index', 'uses' => 'ProjectCommentController@index']);
Route::get('project_comments.load/{project}/{comment}', ['as' => 'project_comments.load', 'uses' => 'ProjectCommentController@load']);


//Properties
Route::resource("properties","PropertyController");
Route::get('properties.datatable', 'PropertyController@datatable')->name('properties.datatable');
Route::get('properties.load/{id}', 'PropertyController@load')->name('properties.load');
Route::get('properties.rpt_properties', 'PropertyController@rpt_properties')->name('properties.rpt_properties');
Route::get('properties.xls_properties', 'PropertyController@xls_properties')->name('properties.xls_properties');
Route::get('taxes', 'PropertyController@taxes')->name('properties.taxes');
Route::post('properties.update_taxes', 'PropertyController@update_taxes')->name('properties.update_taxes');
Route::get('statement/{id}', 'PropertyController@statement')->name('statement');
Route::get('properties.rpt_statement/{id}', 'PropertyController@rpt_statement')->name('properties.rpt_statement');
Route::get('properties.xls_statement/{id}', 'PropertyController@xls_statement')->name('properties.xls_statement');

//Replies
Route::resource("replies","ReplyController");

//Reservations
Route::resource("reservations","ReservationController");
Route::post('reservations.datatable', 'ReservationController@datatable')->name('reservations.datatable');
Route::get('reservations.load/{id}/{facility}', 'ReservationController@load')->name('reservations.load');
Route::get('reservations.status/{id}', 'ReservationController@status')->name('reservations.status');
Route::get('reservations.rpt_reservation', 'ReservationController@rpt_reservation')->name('reservations.rpt_reservation');
Route::post('reserve/{id}', 'ReservationController@reserve')->name('reserve');
Route::get('reservations.load_confirm/{id}', 'ReservationController@load_confirm')->name('reservations.load_confirm');
Route::post('reservations.confirm/{id}', 'ReservationController@confirm')->name('reservations.confirm');

//Setting
Route::get('global', ['as' => 'global', 'uses' => 'SettingController@global']);
Route::post('settings.update_global', ['as' => 'settings.update_global', 'uses' => 'SettingController@update_global']);
Route::get('settings', ['as' => 'settings', 'uses' => 'SettingController@condominium']);
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

//Transfer
Route::resource("transfers","TransferController");
Route::get('transfers.datatable', 'TransferController@datatable')->name('transfers.datatable');
Route::get('transfers.load/{id}', 'TransferController@load')->name('transfers.load');
Route::get('transfers.download/{id}', ['as' => 'transfers.download', 'uses' => 'TransferController@download_file']);

//Tools
Route::get('get_states/{id}', 'ToolController@get_states');

//Users
Route::resource("users","UserController");
Route::get('users.datatable', 'UserController@datatable')->name('users.datatable');
Route::get('users.load/{id}', 'UserController@load')->name('users.load');
Route::get('users.status/{id}', 'UserController@status')->name('users.status');
Route::get('users.rpt_users', 'UserController@rpt_users')->name('users.rpt_users');

//VisitTypes
Route::resource("visit_types","VisitTypeController");
Route::get('visit_types.datatable', 'VisitTypeController@datatable')->name('visit_types.datatable');
Route::get('visit_types.load/{id}', 'VisitTypeController@load')->name('visit_types.load');
Route::get('visit_types.status/{id}', 'VisitTypeController@status')->name('visit_types.status');
Route::get('visit_types.rpt_visit_types', 'VisitTypeController@rpt_visit_types')->name('visit_types.rpt_visit_types');

//Visitors
Route::resource("visitors","VisitorController");
Route::get('visitor_by_nit/{id}', 'VisitorController@visitor_by_nit')->name('visitor_by_nit');


//Visits
Route::resource("visits","VisitController");
Route::get('visits.datatable', 'VisitController@datatable')->name('visits.datatable');
Route::get('visits.load/{id}', 'VisitController@load')->name('visits.load');
Route::get('visits.download/{id}', ['as' => 'visits.download', 'uses' => 'VisitController@download_file']);

//Fix
Route::get('update_balance_accounts', 'FixController@update_balance_accounts');
Route::get('test_polimorf', 'FixController@test_polimorf');



