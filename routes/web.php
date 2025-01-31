<?php

use Illuminate\Support\Facades\Route;

use Modules\Account\Http\Controllers\PaymentController;

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

Route::get('/payment/{invoice_id}', [PaymentController::class, 'payment'])->name('account_payment');
