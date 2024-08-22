<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegistrationController;
use App\Http\Controllers\Customer;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout']);
Route::post('/register', [RegistrationController::class, 'register']);


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::group(['middleware' => ['auth:sanctum']], function () {

    Route::get('/app-data', [UserController::class, 'index']);

    Route::resource('products', ProductController::class);
    Route::resource('orders', OrderController::class);
    Route::resource('customers', CustomerController::class);

    Route::post('/import/products', [ProductController::class, 'importProducts']);
    Route::get('/export/products', [ProductController::class, 'exportProducts']);

    Route::post('/import/customers', [CustomerController::class, 'importCustomers']);
    Route::get('/export/customers', [CustomerController::class, 'exportCustomers']);

    Route::post('/import/orders', [OrderController::class, 'importOrders']);
    Route::get('/export/orders', [OrderController::class, 'exportOrders']);
});
