<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes(['register' => false]);
Route::get('logout', 'Auth\LoginController@logout')->name('logout.get');

Route::post(
    '/token',
    ['uses' => 'TokenController@newToken', 'as' => 'new-token']
);
Route::get(
    '/support/call',
    ['uses' => 'CallController@newCall', 'as' => 'new-call']
);
Route::get(
    '/support/callback',
    ['uses' => 'CallController@fallback', 'as' => 'fallback-call']
);
Route::get(
    '/support/fallback',
    ['uses' => 'CallController@fallback', 'as' => 'fallback-call-status']
);

Route::get(
    '/setup',
    ['uses' => 'Twilio\ApplicationController@update']
);


Route::prefix('twilio')->group(function () {
    Route::prefix('task')->group(function () {
        Route::get('/', 'Twilio\TaskController@create');
        Route::post('/assignment', 'Twilio\TaskController@assignment');
        Route::get('/accept', 'Twilio\TaskController@accept');
    });
    Route::prefix('call')->group(function () {
        Route::get('/incoming-call', 'Twilio\CallController@incoming');
        Route::get('/enqueue', 'Twilio\CallController@enqueue');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/home', 'HomeController@index')->name('dashboard');
    Route::get('/installation', 'WidgetController@index')->name('installation');
    Route::get('/inbound',['uses' => 'InboundController@index', 'as' => 'inbound']);
    Route::resource('user', 'UserController');
    Route::get('user/{id}/active', 'UserController@restore')->name('user.restore');
    Route::get('user/{user}/inactive', 'UserController@destroy')->name('user.delete');
    Route::resource('topic', 'TopicController');
    Route::get('topic/{id}/active', 'TopicController@restore')->name('topic.restore');
    Route::get('topic/{topic}/inactive', 'TopicController@destroy')->name('topic.delete');
});
