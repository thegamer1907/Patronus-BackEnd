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

    // Protected route
    Route::get('/users', 'JwtAuthenticateController@index');
    Route::post('/deleteuser', 'UserController@deleteuser');
});

Route::group(['middleware' => ['ability:user|superuser|bankmanager,create_user']], function()
{
    // Protected route
    Route::post('/changepassword', 'UserController@changepassword');
    Route::get('/overview', 'UserController@overview');
    Route::post('/addbenificiary', 'UserController@addbenificiary');
    Route::get('/viewbenificiary', 'UserController@viewbenificiary');
    Route::post('/deletebenificiary', 'UserController@deletebenificiary');
    Route::post('/filecomplaint', 'UserController@filecomplaint');
    Route::post('/starttransfer', 'UserController@starttransfer');
    Route::post('/completetransfer', 'UserController@completetransfer');
});

Route::group(['middleware' => ['ability:bankmanager|superuser,create_user']], function()
{
    Route::post('/resolvecomplaint', 'UserController@resolvecomplaint');
    Route::get('/viewcomplaint', 'UserController@viewcomplaint');
    Route::get('/getallroles', 'UserController@getallroles');
    Route::post('/blockaccount', 'UserController@blockaccount');
    Route::post('/unblockaccount', 'UserController@unblockaccount');
    Route::get('/getallblocked', 'UserController@getallblocked');
    // Route to create a new role
    Route::post('/role', 'JwtAuthenticateController@createRole');
    // Route to create a new permission
    Route::post('/permission', 'JwtAuthenticateController@createPermission');
    // Route to assign role to user
    Route::post('/assign-role', 'JwtAuthenticateController@assignRole');
    // Route to attache permission to a role
    Route::post('/attach-permission', 'JwtAuthenticateController@attachPermission');
});


//Login Route
Route::post('/login', 'JwtAuthenticateController@authenticate');
Route::post('/forgotpassword', 'UserController@forgot');
