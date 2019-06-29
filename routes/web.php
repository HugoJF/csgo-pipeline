<?php

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

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::prefix('pipes')->name('pipes.')->group(function () {
	Route::get('create', 'PipeController@create')->name('create');
	Route::get('by-key/{key}', 'PipeController@viewByKey')->name('view-by-key');
	Route::get('{pipe}', 'PipeController@view')->name('view');
	Route::get('{pipe}/edit', 'PipeController@edit')->name('edit');

	Route::patch('{pipe}', 'PipeController@update')->name('update');

	Route::post('store', 'PipeController@store')->name('store');

	Route::delete('{pipe}', 'PipeController@destroy')->name('destroy');

});

Route::prefix('pipes/{pipe}/lines')->name('lines.')->group(function () {
	Route::get('create', 'LineController@create')->name('create');

	Route::post('/', 'LineController@store')->name('store');
});

Route::prefix('lines')->name('lines.')->group(function () {
	Route::delete('{line}', 'LineController@destroy')->name('destroy');
});
Route::prefix('servers')->name('servers.')->group(function () {
	Route::get('create', 'ServerController@create')->name('create');
	Route::get('{server}', 'ServerController@view')->name('view');
	Route::get('{server}/edit', 'ServerController@edit')->name('edit');

	Route::post('/', 'ServerController@store')->name('store');

	Route::patch('{server}', 'ServerController@update')->name('update');

	Route::delete('{server}', 'ServerController@destroy')->name('destroy');
});