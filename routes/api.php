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
    Route::post('update_user_remote', 'Api\v1\Auth\AuthController@update_user_remote');
    //event_types
    Route::get('get_event_types_remote', 'Api\v1\EventTypeController@get_event_types_remote');
    Route::post('add_event_type_remote', 'Api\v1\EventTypeController@add_event_type_remote');
    Route::delete('destroy_event_type_remote/{id?}/{description?}', 'Api\v1\EventTypeController@destroy_event_type_remote');
    Route::post('update_event_type_remote',
        'Api\v1\EventTypeController@update_event_type_remote');
    //spheres
    Route::get('get_spheres_remote', 'Api\v1\SphereController@get_spheres_remote');
    Route::post('add_sphere_remote', 'Api\v1\SphereController@add_sphere_remote');
    Route::delete('destroy_sphere_remote/{id?}/{description?}', 'Api\v1\SphereController@destroy_sphere_remote');
    Route::post('update_sphere_remote',
        'Api\v1\SphereController@update_sphere_remote');
    //events
    Route::post('add_event_remote', 'Api\v1\EventController@add_event_remote');
    Route::get('user', function () {
        return 'authenticate';
    });
});