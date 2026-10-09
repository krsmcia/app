<?php

use App\Http\Controllers\Purchasing\PurchaseRequestController;
use App\Livewire;
use Illuminate\Support\Facades\Route;

use Illuminate\Http\Request;
use App\Notifications\TestWebPushNotification;

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
        Common
    |--------------------------------------------------------------------------
    */
    Route::get('/dashboard', function () {return view('dashboard');})->name('dashboard');
    Route::get('/pending-approval', Livewire\PendingApproval::class)->name('pending-approval');
    Route::get('/items', Livewire\Items::class)->name('items');
    Route::get('/items/category/{category:code}', Livewire\Items\Category::class)->name('items.category');
    Route::get('/wishlist', Livewire\Wishlist::class)->name('wishlist');
    Route::get('/purchase-requests', Livewire\PurchaseRequests::class)->name('purchase-requests');
    Route::get('/cart', Livewire\Cart::class)->name('cart');
    Route::get('/my-requests', Livewire\MyRequests::class)->name('my-requests');
    Route::get('/ready-for-pickup', Livewire\ReadyForPickup::class)->name('ready-for-pickup');

    /*
    |--------------------------------------------------------------------------
    | Organization
    |--------------------------------------------------------------------------
    */
    Route::middleware(['department:hr'])->group(function () {
        Route::get('/employees', Livewire\Employees\Index::class)
            ->name('employees');
        Route::get('/departments', Livewire\Departments\Index::class)
            ->name('departments');
    });
    /*
    |--------------------------------------------------------------------------
    | Procurements
    |--------------------------------------------------------------------------
    */
    Route::middleware(['department:procurement'])->prefix('procurements')->name('procurements.')->group(function () {
        Route::get('/categories', Livewire\Procurements\Categories::class)->name('categories');
        Route::get('/items', Livewire\Procurements\Items::class)->name('items');
        Route::get('/vendors', Livewire\Procurements\Vendors::class)->name('vendors');
        Route::get('/requests', Livewire\Procurements\Requests::class)->name('requests');
        Route::middleware(['role:supervisor'])->group(function () {
            Route::get('/review-prices', Livewire\Procurements\ReviewPrices::class)->name('review-prices');
        });
        Route::get('/approved', Livewire\Procurements\Approved::class)->name('approved');
        Route::get('/purchased', Livewire\Procurements\Purchased::class)->name('purchased');
        Route::get('/ordered', Livewire\Procurements\Ordered::class)->name('ordered');
        Route::get('/liquidations', Livewire\Procurements\Liquidations::class)->name('liquidations');
        Route::get('/handover', Livewire\Procurements\Handover::class)->name('handover');
    });
    Route::middleware(['department:warehouse'])->prefix('warehouses')->name('warehouses.')->group(function () {
        Route::get('/warehouses', Livewire\Warehouses\Warehouses::class)->name('warehouses');
        Route::get('/inventory-management', Livewire\Warehouses\InventoryManagement::class)->name('inventory-management');
        Route::get('/stock-in/{code}', Livewire\Warehouses\StockIn::class)->name('stock-in');
        Route::get('/stock-out/{code}', Livewire\Warehouses\StockOut::class)->name('stock-out');
    });
    Route::middleware(['department:audit'])->prefix('audits')->name('audits.')->group(function () {
        Route::get('/requests', Livewire\Audits\Requests::class)->name('requests');
        Route::get('/inventories', Livewire\Audits\Inventories::class)->name('inventories');
        Route::get('/vendors', Livewire\Audits\Vendors::class)->name('vendors');
    });
    Route::middleware(['role:super-admin|head'])->group(function () {
        Route::get('/heads/requests', Livewire\Heads\Requests::class)->name('heads.requests');
    });
    Route::middleware(['role:super-admin|admin'])->group(function () {
        Route::get('/admins/requests', Livewire\Admins\Requests::class)->name('admins.requests');
    });
    Route::middleware(['department:accounting'])->prefix('accountings')->name('accountings.')->group(function () {
        Route::get('/requests', Livewire\Accountings\Requests::class)->name('requests');
        Route::get('/transactions', Livewire\Accountings\Transactions::class)->name('transactions');
        Route::get('/liquidations', Livewire\Accountings\Liquidations::class)->name('liquidations');
        Route::get('/liquidations/{user}', Livewire\Accountings\UserLiquidations::class)->name('user-liquidations');
    });




    Route::middleware('auth')->prefix('push')->name('push.')->group(function () {
        Route::get('/vapid-public-key', function () {
            return response()->json([
                'publicKey' => config('webpush.vapid.public_key'),
            ]);
        })->name('vapid-public-key');
        Route::post('/subscribe', function (Request $request) {
            $validated = $request->validate([
                'endpoint' => ['required', 'url', 'max:4096'],
                'keys.p256dh' => ['required', 'string', 'max:255'],
                'keys.auth' => ['required', 'string', 'max:255'],
            ]);
            $request->user()->updatePushSubscription(
                $validated['endpoint'],
                $validated['keys']['p256dh'],
                $validated['keys']['auth'],
            );
            return response()->json(['success' => true]);
        })->name('subscribe');
        Route::delete('/unsubscribe', function (Request $request) {
            $validated = $request->validate([
                'endpoint' => ['required', 'url', 'max:4096'],
            ]);
            $request->user()->deletePushSubscription(
                $validated['endpoint']
            );
            return response()->json(['success' => true]);
        })->name('unsubscribe');
    });
});