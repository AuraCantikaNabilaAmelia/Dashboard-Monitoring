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
        Route::get('/detail/{id}', 'App\Http\Controllers\DashboardController@detail')->name('st.detail');
        Route::get('/employee/{nip}', 'App\Http\Controllers\DashboardController@employeeDetail')->name('employee.detail');

        Route::get('/tindaklanjut', 'App\Http\Controllers\TindakLanjutController@index')->name('tindaklanjut.index');
        Route::get('/tindaklanjut/create', 'App\Http\Controllers\TindakLanjutController@create')->name('tindaklanjut.create');
        Route::post('/tindaklanjut', 'App\Http\Controllers\TindakLanjutController@store')->name('tindaklanjut.store');
        Route::get('/tindaklanjut/{id}', 'App\Http\Controllers\TindakLanjutController@show')->name('tindaklanjut.show');
        Route::get('/tindaklanjut/{id}/entry/create', 'App\Http\Controllers\TindakLanjutController@addEntryForm')->name('tindaklanjut.addEntryForm');
        Route::post('/tindaklanjut/{id}/entry', 'App\Http\Controllers\TindakLanjutController@addEntry')->name('tindaklanjut.addEntry');
        Route::get('/tindaklanjut/entry/{entry_id}/edit', 'App\Http\Controllers\TindakLanjutController@editEntryForm')->name('tindaklanjut.editEntryForm');
        Route::put('/tindaklanjut/entry/{entry_id}', 'App\Http\Controllers\TindakLanjutController@updateEntry')->name('tindaklanjut.updateEntry');
        Route::delete('/tindaklanjut/entry/{entry_id}', 'App\Http\Controllers\TindakLanjutController@deleteEntry')->name('tindaklanjut.deleteEntry');
        Route::post('/tindaklanjut/{id}/selesai', 'App\Http\Controllers\TindakLanjutController@markSelesai')->name('tindaklanjut.selesai');
        Route::post('/tindaklanjut/{id}/reopen', 'App\Http\Controllers\TindakLanjutController@reopen')->name('tindaklanjut.reopen');
        Route::post('/tindaklanjut/{id}/claim', 'App\Http\Controllers\TindakLanjutController@claim')->name('tindaklanjut.claim');
        Route::post('/tindaklanjut/{id}/update-status', 'App\Http\Controllers\TindakLanjutController@updateStatus')->name('tindaklanjut.update-status');
        Route::delete('/tindaklanjut/{id}', 'App\Http\Controllers\TindakLanjutController@destroy')->name('tindaklanjut.destroy');
    });
