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
Route::post('order/add', "Order\AddOrderController@addOrder")->middleware('web', 'check.login');
// 用户查询自己的订单
Route::get('order/find', "Order\FindOrderController@findOrder");

// 管理员查询当天订单
Route::get('order/find_today', 'Admin\FindTodayOrderController@findTodayOrder');
// 管理员修改订单状态
Route::post('order/modify', 'Admin\ModifyOrderController@modifyOrder');
// 管理员导出Excel
Route::get('excel/export', 'Excel\ExcelController@export');

