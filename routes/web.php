<?php

use Illuminate\Support\Facades\Route;

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

Route::group(['prefix' => 'github'], function () {
    Route::get('/', 'App\Http\Controllers\GithubController@test');
    Route::get('callback', 'App\Http\Controllers\GithubController@githubCallback');
    Route::get('sync', 'App\Http\Controllers\GithubSyncController@sync');
});