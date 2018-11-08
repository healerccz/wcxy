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

// 用户登录并获取用户信息
Route::post('login', "Auth\WxxcxController@getWxUserInfo");
// 用户下单
Route::post('order/add', "Order\AddOrderController@addOrder")->middleware('check.login');
// 用户查询自己的订单
Route::post('order/find', "Order\FindOrderController@findOrder");