<?php

use Illuminate\Support\Facades\Route;

Route::get('chart_of_account/recordselect', 'ChartOfAccountController@recordselect');
Route::get('ledger/recordselect', 'LedgerController@recordselect');
Route::get('rate/recordselect', 'RateController@recordselect');
Route::get('invoice/fetchdata', 'InvoiceController@fetchData');
