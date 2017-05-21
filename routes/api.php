<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['prefix' => 'v1/'], function()
{
	Route::resource('users', 'UserController', ['only' => [
    	'index', 'show', 'store', 'destroy'
	]]);

	// Route::resource('tasks', 'TaskController');

	Route::resource('tasks', 'TaskController', ['only' => [
    	'index', 'show', 'store', 'destroy', 'update'
	]]);
});
