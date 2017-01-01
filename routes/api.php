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
Route::post('get_access_token', 'Auth\LoginController@get_access_token');
//Route::get('login', 'Auth\LoginController@login');
Route::get('refresh_token', 'Auth\LoginController@refresh_token');

Route::group([
    'middleware' => 'jwt.auth',
        ], function () {

    Route::get('logout', 'Auth\LoginController@logout');
    Route::get('user', function () {
        return 'authenticated';
    });
});