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

Route::post('login', 'api\UserController@login');
Route::post('register', 'api\UserController@register');

Route::group(['middleware' => 'auth:api'], function(){
    Route::group(['prefix' => 'refunds'], function(){
        Route::post('', 'api\RefundsController@store');
        Route::put('{id}', 'api\RefundsController@update');
        Route::delete('{id}', 'api\RefundsController@destroy');
        Route::put('{id}/restore', 'api\RefundsController@restore');
        Route::get('report', 'api\RefundsController@report');
        Route::get('', 'api\RefundsController@index');
    });
});
