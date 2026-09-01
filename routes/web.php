<?php

use App\Http\Controllers\Purchasing\PurchaseRequestController;
use App\Livewire;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});
/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'remember.expiration',
    'verified',
])->group(function () {
    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    /*
    |--------------------------------------------------------------------------
    | Organization
    |--------------------------------------------------------------------------
    */
    Route::get('/employees', Livewire\Employees\Index::class)
        ->name('employees');
    Route::get('/departments', Livewire\Departments\Index::class)
        ->name('departments');
    /*
    |--------------------------------------------------------------------------
    | Procurements
    |--------------------------------------------------------------------------
    */
    Route::prefix('procurements')->name('procurements.')->group(function () {
        Route::get('/categories', Livewire\Procurements\Categories::class)->name('categories');
        Route::get('/items', Livewire\Procurements\Items::class)->name('items');
        Route::get('/vendors', Livewire\Procurements\Vendors::class)->name('vendors');
        Route::get('/requests', Livewire\Procurements\Requests::class)->name('requests');
    });

    Route::prefix('audits')->name('audits.')->group(function () {
        Route::get('/requests', Livewire\Audits\Requests::class)->name('requests');
    });

    Route::prefix('warehouses')->name('warehouses.')->group(function () {
        Route::get('/warehouses', Livewire\Warehouses\Warehouses::class)->name('warehouses');
        Route::get('/inventory-management', Livewire\Warehouses\InventoryManagement::class)->name('inventory-management');
    });

    /*
    |--------------------------------------------------------------------------
    | Items
    |--------------------------------------------------------------------------
    */

    Route::get('/items', Livewire\Items::class)->name('items');
    Route::get('/items/category/{category:code}', Livewire\Items\Category::class)->name('items.category');
    Route::get('/wishlist', Livewire\Wishlist::class)->name('wishlist');
    Route::get('/purchase-requests', Livewire\PurchaseRequests::class)->name('purchase-requests');
    Route::get('/cart', Livewire\Cart::class)->name('cart');
    Route::get('/my-requests', Livewire\MyRequests::class)->name('my-requests');
    Route::get('/pending-approval', Livewire\PendingApproval::class)->name('pending-approval');
});