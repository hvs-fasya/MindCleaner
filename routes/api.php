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
Route::post('v1/register_remote', 'Api\v1\Auth\AuthController@register_remote');
Route::post('v1/get_access_token', 'Api\v1\Auth\AuthController@get_access_token');
//Route::get('login', 'Api\v1\Auth\AuthController@login');
Route::get('v1/refresh_token', 'Api\v1\Auth\AuthController@refresh_token');

Route::group([
    'prefix' => 'v1',
    'middleware' => 'jwt.auth',
        ], function () {

    Route::get('logout_remote', 'Api\v1\Auth\AuthController@logout_remote');
    Route::get('user', function () {
        return 'authenticated';
    });
});