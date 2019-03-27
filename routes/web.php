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

Route::post('/register',"Reg\RegController@register");
Route::post('/goodslist',"Goods\GoodsController@goodsList");

//登录
$router->post('group_shop/login','Login\LoginController@check_login');

//商品详情页
$router->post('group_shop/goods_detail',"Goods\GoodsController@goods_detail");

//购物车
$router->post('cart/list','Cart\CartController@list');


