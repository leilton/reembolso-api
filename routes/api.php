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
        Route::post('{id}/upload', 'api\RefundsController@fileupload');
        Route::put('{id}', 'api\RefundsController@update');
        Route::put('{id}/approve', 'api\RefundsController@approve');
        Route::put('{id}/block', 'api\RefundsController@block');
        Route::delete('{id}', 'api\RefundsController@destroy');
        Route::put('{id}/restore', 'api\RefundsController@restore');
        Route::get('report', 'api\RefundsController@report');
        Route::get('', 'api\RefundsController@index');
    });
});
