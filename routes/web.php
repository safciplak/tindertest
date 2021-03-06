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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/', 'TestController@index');
Route::get('show', 'TestController@show');
Route::get('like', 'TestController@like');
Route::get('unlike', 'TestController@unlike');
Auth::routes();

Route::get('home', 'HomeController@index')->name('home');


// OAuth Routes
Route::get('auth/{provider}', 'AuthController@redirectToProvider');
Route::get('auth/{provider}/callback', 'AuthController@handleProviderCallback');