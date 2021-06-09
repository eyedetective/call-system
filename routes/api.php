<?php

use Illuminate\Support\Facades\Route;

Route::post('/call/token','CallController@token');
Route::post('/call/schedule','CallController@scheduleCall');
Route::post('/call/fail','CallController@failCall');
Route::post('/call/incoming','CallController@newCall');
Route::post('/call/fallback','CallController@fallback');
Route::post('/topic/list','TopicController@list');

Route::middleware('auth:api')->apiResource('ticket','TicketController');
Route::middleware('auth:api')->post('user/available','UserController@setAvailable')->name('user.available');
Route::middleware('auth:api')->post('ticket','TicketController@index')->name('ticket.index.post');
