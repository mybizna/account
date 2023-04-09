<?php

use Illuminate\Support\Facades\Route;

Route::get('chart_of_account/recordselect', 'ChartOfAccountController@recordselect');
Route::get('ledger/recordselect', 'LedgerController@recordselect');
Route::get('rate/recordselect', 'RateController@recordselect');
Route::get('invoice/fetchdata', 'InvoiceController@fetchData');
Route::post('invoice/savedata', 'InvoiceController@saveData');
Route::get('invoice/reconcileinvoices', 'InvoiceController@reconcileInvoices');
Route::get('ledger/chartsummary/{slug}', 'LedgerController@chartsummary');
