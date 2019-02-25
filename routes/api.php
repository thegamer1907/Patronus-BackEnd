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


Route::get('/', 'UserController@check');
Route::post('/signup','UserController@signup');

// API route group that we need to protect
Route::group(['middleware' => ['ability:superuser,create_user']], function()
{

// Route to create a new role
    Route::post('/role', 'JwtAuthenticateController@createRole');
// Route to create a new permission
    Route::post('/permission', 'JwtAuthenticateController@createPermission');
// Route to assign role to user
    Route::post('/assign-role', 'JwtAuthenticateController@assignRole');
// Route to attache permission to a role
    Route::post('/attach-permission', 'JwtAuthenticateController@attachPermission');
    // Protected route
    Route::get('/users', 'JwtAuthenticateController@index');
});

Route::group(['middleware' => ['ability:user|superuser,create_user']], function()
{
    // Protected route
    Route::post('/changepassword', 'UserController@changepassword');
});



//Login Route
Route::post('/login', 'JwtAuthenticateController@authenticate');
Route::post('/forgotpassword', 'UserController@forgot');
