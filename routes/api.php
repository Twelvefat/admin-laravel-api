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

Route::group(['prefix' => 'v1'], function(){
    Route::post('/login','Auth\AuthController@login');

    Route::group(['middleware' => 'jwt'], function(){
        Route::get('/me', 'UserController@index');
        Route::get('/logout','Auth\AuthController@logout');


        //user
        Route::get('/user','Dashboard\UserController@index');
        Route::post('/user/store','Dashboard\UserController@store');
        Route::get('/user/{id}','Dashboard\UserController@detail');
        Route::patch('/user/{id}','Dashboard\UserController@update');
        Route::delete('/user/{id}','Dashboard\UserController@destroy');

        // Permissions
        Route::get('/permissions','Dashboard\PermissionController@index');
        Route::get('/permissions/data','Dashboard\PermissionController@data');
        Route::post('/permissions/store','Dashboard\PermissionController@store');
        Route::get('/permissions/{id}','Dashboard\PermissionController@detail');
        Route::patch('/permissions/{id}','Dashboard\PermissionController@update');
        Route::delete('/permissions/{id}','Dashboard\PermissionController@destroy');

        // Role
        Route::get('/role','Dashboard\RoleController@index');
        Route::post('/role/store','Dashboard\RoleController@store');
        Route::get('/role/{id}','Dashboard\RoleController@detail');
        Route::patch('/role/{id}','Dashboard\RoleController@update');
        Route::delete('/role/{id}','Dashboard\RoleController@destroy');

    });
});
