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

Route::namespace('System')->middleware(['AppToken'])->group(function() {
    //用户
    Route::get('system/user/listpage', 'UserController@listpage');
    Route::post('system/user/add', 'UserController@add');
    Route::post('system/user/edit', 'UserController@edit');
    Route::get('system/user/remove', 'UserController@remove');
    Route::get('system/user/batchRemove', 'UserController@batchRemove');
    //角色
    Route::get('system/role/total', 'RoleController@total');
    Route::get('system/role/listpage', 'RoleController@listpage');
    Route::post('system/role/add', 'RoleController@add');
    Route::post('system/role/edit', 'RoleController@edit');
    Route::get('system/role/remove', 'RoleController@remove');
    Route::get('system/role/batchRemove', 'RoleController@batchRemove');
    //操作日志
    Route::get('system/log', 'LogController@listpage');
});

Route::namespace('System')->group(function(){
    Route::get('system/user/info', 'UserController@info');
    Route::post('system/user/login', 'UserController@login');
    Route::post('system/user/logout', 'UserController@logout');
});
