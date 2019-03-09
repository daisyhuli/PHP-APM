<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/



Route::group(['middleware' => ['web']],function() {
    Route::any ('/register',"UserController@getInsert");
    Route::any ('/login',"UserController@checkStatus");
    Route::any ('/userInfo',"UserController@viewStatus");
    Route::any ('/logout',"UserController@logOut");
});
