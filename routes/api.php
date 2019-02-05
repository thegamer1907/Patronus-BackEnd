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

// Route to create a new role
Route::post('/role', 'JwtAuthenticateController@createRole');
// Route to create a new permission
Route::post('/permission', 'JwtAuthenticateController@createPermission');
// Route to assign role to user
Route::post('/assign-role', 'JwtAuthenticateController@assignRole');
// Route to attache permission to a role
Route::post('/attach-permission', 'JwtAuthenticateController@attachPermission');

Route::get('/', 'UserController@check');

// API route group that we need to protect
Route::group(['middleware' => ['ability:admin,create-users']], function()
{
    // Protected route
    Route::get('/users', 'JwtAuthenticateController@index');
});

// Authentication route
Route::post('/authenticate', 'JwtAuthenticateController@authenticate');
