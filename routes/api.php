<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/call/token','CallController@token');
Route::post('/call/schedule','CallController@scheduleCall');
Route::post('/call/incoming','CallController@newCall');
Route::post('/call/fallback','CallController@fallback');

Route::middleware('auth:api')->apiResource('ticket','TicketController');
