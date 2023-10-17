<?php

use Illuminate\Support\Facades\Route;

$rights = ["account_invoice_edit", "account_invoice_edit_own", "account_invoice_add", "account_invoice_add_own"];

Route::get('chart_of_account/recordselect', 'ChartOfAccountController@recordselect', )->rights($rights);
Route::get('ledger/recordselect', 'LedgerController@recordselect', )->rights($rights);
Route::get('rate/recordselect', 'RateController@recordselect', )->rights($rights);
Route::get('invoice/fetchdata', 'InvoiceController@fetchData', )->rights($rights);
Route::post('invoice/savedata', 'InvoiceController@saveData', )->rights($rights);
Route::get('invoice/reconcileinvoices', 'InvoiceController@reconcileInvoices', )->rights($rights);
Route::get('chart_of_account/summation/{slug}', 'ChartOfAccountController@chartsummary', )->rights($rights);
