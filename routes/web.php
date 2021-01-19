<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CustomersReportController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InvoiceAchiveController;
use App\Http\Controllers\InvoiceAttachmentsController;
use App\Http\Controllers\InvoicesController;
use App\Http\Controllers\InvoicesDetailsController;
use App\Http\Controllers\InvoicesReportController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SectionsController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});

Auth::routes();
// Auth::routes(['register' => false]);

Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::resource('invoices', InvoicesController::class);
Route::resource('sections', SectionsController::class);
Route::resource('products', ProductsController::class);

Route::resource('invoice_attachments', InvoiceAttachmentsController::class);

// Route::resource('InvoicesDetails', InvoicesDetailsController::class);

Route::get('/section/{id}', [InvoicesController::class, 'getProducts']);

Route::get('/invoices_details/{id}', [InvoicesDetailsController::class, 'edit']);

Route::get('download/{invoice_number}/{file_name}', [InvoicesDetailsController::class, 'getFile']);

Route::get('view_file/{invoice_number}/{file_name}', [InvoicesDetailsController::class, 'openFile']);

Route::post('delete_file', [InvoicesDetailsController::class, 'destroy'])->name('delete_file');

Route::get('/edit_invoice/{id}', [InvoicesController::class, 'edit']);

Route::get('/status_show/{id}', [InvoicesController::class, 'show'])->name('status_show');

Route::post('/status_update/{id}', [InvoicesController::class, 'statusUpdate'])->name('status_update');

Route::resource('archive', InvoiceAchiveController::class);

Route::get('/invoice_paid', [InvoicesController::class, 'invoicePaid']);
Route::get('/invoice_unpaid', [InvoicesController::class, 'invoiceUnpaid']);
Route::get('/invoice_partial', [InvoicesController::class, 'invoicePartial']);
Route::get('print_invoice/{id}', [InvoicesController::class, 'printInvoice']);
Route::get('export_invoices', [InvoicesController::class, 'export']);

Route::group(['middleware' => ['auth']], function () {
    Route::resource('roles', RoleController::class);
    Route::resource('users', UserController::class);
});

Route::get('/invoices_report', [InvoicesReportController::class, 'index']);
Route::post('/invoices_report', [InvoicesReportController::class, 'searchInvoices']);

Route::get('/customers_report', [CustomersReportController::class, 'index'])->name("customers_report");
Route::post('/search_customers', [CustomersReportController::class, 'searchCustomers']);

Route::get('/markAsReadAll', [InvoicesController::class, 'markAsReadAll'])->name("markAsReadAll");

Route::get('unreadNotifications_count', [InvoicesController::class, 'unreadNotifications_count'])->name('unreadNotifications_count');

Route::get('unreadNotifications', [InvoicesController::class, 'unreadNotifications'])->name('unreadNotifications');

Route::get('/{page}', [AdminController::class, 'index']);
