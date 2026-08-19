<?php

use Illuminate\Support\Facades\Route;
use App\Livewire;
use App\Http\Controllers\Purchasing\PurchaseRequestController;
Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    Route::get('/empoyees', Livewire\Employees\Index::class)->name('employees');
    Route::get('/departments', Livewire\Departments\Index::class)->name('departments');
    Route::get('/procurements/categories', Livewire\Procurements\Categories::class)->name('procurements.categories');
    Route::get('/procurements/items', Livewire\Procurements\Items::class)->name('procurements.items');
    Route::get('/procurements/vendors', Livewire\Procurements\Vendors::class)->name('procurements.vendors');
    //Route::get('/purchasing/request', Livewire\Purchasing\Request\Index::class)->name('purchasing.request');
    Route::get('/purchasing/request/create',[PurchaseRequestController::class, 'create'])->name('purchasing.request.create');
    Route::post('/purchasing/request',[PurchaseRequestController::class, 'store'])->name('purchasing.request.store');
});
