<?php

use Illuminate\Support\Facades\Route;

Route::post('/call/token','CallController@token');
Route::post('/call/schedule','CallController@scheduleCall');
Route::post('/call/fail','CallController@failCall');
Route::post('/call/incoming','CallController@newCall');
Route::post('/call/fallback','CallController@fallback');

Route::middleware('auth:api')->apiResource('ticket','TicketController');
Route::middleware('auth:api')->post('ticket','TicketController@index')->name('ticket.index.post');
