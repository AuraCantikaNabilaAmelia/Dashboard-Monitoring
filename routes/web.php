<?php

use Illuminate\Support\Facades\Route;

Route::get('/', 'App\Http\Controllers\AuthController@showLogin');
Route::get('/login', 'App\Http\Controllers\AuthController@showLogin')->name('login');
Route::post('/login', 'App\Http\Controllers\AuthController@login');
Route::post('/logout', 'App\Http\Controllers\AuthController@logout')->name('logout');

Route::prefix('dashboard')
    ->middleware('auth')
    ->group(function () {
        Route::get('/', 'App\Http\Controllers\DashboardController@index');
        Route::get('/st', 'App\Http\Controllers\DashboardController@stList');
        Route::get('/lhp', 'App\Http\Controllers\DashboardController@lhpList');
        Route::get('/daily', 'App\Http\Controllers\DashboardController@daily');
        Route::get('/monthly', 'App\Http\Controllers\DashboardController@monthly')->name('dashboard.monthly');
        Route::get('/bidwas', 'App\Http\Controllers\DashboardController@bidwas');
        Route::get('/bidwas/{id}', 'App\Http\Controllers\DashboardController@bidwasDetail')->name('bidwas.detail');
        Route::get('/detail/{id}', 'App\Http\Controllers\DashboardController@detail')->name('st.detail');
        Route::get('/employee/{nip}', 'App\Http\Controllers\DashboardController@employeeDetail')->name('employee.detail');

        Route::get('/tindaklanjut', 'App\Http\Controllers\TindakLanjutController@index')->name('tindaklanjut.index');
        Route::get('/tindaklanjut/{id}', 'App\Http\Controllers\TindakLanjutController@show')->name('tindaklanjut.show');
        Route::post('/tindaklanjut/{id}/entry', 'App\Http\Controllers\TindakLanjutController@addEntry')->name('tindaklanjut.addEntry');
        Route::delete('/tindaklanjut/entry/{id}', 'App\Http\Controllers\TindakLanjutController@deleteEntry')->name('tindaklanjut.deleteEntry');

        // Export Routes
        Route::get('/export/st/excel', 'App\Http\Controllers\DashboardController@exportStExcel')->name('export.st.excel');
        Route::get('/export/st/pdf', 'App\Http\Controllers\DashboardController@exportStPdf')->name('export.st.pdf');
        Route::get('/export/lhp/excel', 'App\Http\Controllers\DashboardController@exportLhpExcel')->name('export.lhp.excel');
        Route::get('/export/lhp/pdf', 'App\Http\Controllers\DashboardController@exportLhpPdf')->name('export.lhp.pdf');
        Route::get('/export/bidwas/excel', 'App\Http\Controllers\DashboardController@exportBidwasExcel')->name('export.bidwas.excel');
        Route::get('/export/bidwas/pdf', 'App\Http\Controllers\DashboardController@exportBidwasPdf')->name('export.bidwas.pdf');
        Route::get('/export/daily/excel', 'App\Http\Controllers\DashboardController@exportDailyExcel')->name('export.daily.excel');
        Route::get('/export/daily/pdf', 'App\Http\Controllers\DashboardController@exportDailyPdf')->name('export.daily.pdf');
        Route::get('/export/monthly/excel', 'App\Http\Controllers\DashboardController@exportMonthlyExcel')->name('export.monthly.excel');
        Route::get('/export/monthly/pdf', 'App\Http\Controllers\DashboardController@exportMonthlyPdf')->name('export.monthly.pdf');
        Route::get('/export/bidwas-detail/{id}/excel', 'App\Http\Controllers\DashboardController@exportBidwasDetailExcel')->name('export.bidwas_detail.excel');
        Route::get('/export/bidwas-detail/{id}/pdf', 'App\Http\Controllers\DashboardController@exportBidwasDetailPdf')->name('export.bidwas_detail.pdf');
    });
