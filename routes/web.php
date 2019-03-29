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
$router->post('cart/addcart','Cart\CartController@addCart');
$router->post('cart/delcart','Cart\CartController@delCart');
//生成订单
$router->post('/addorder','Order\OrderController@createOrder');
//订单列表展示
$router->post('/orderlist','Order\OrderController@Orderlist');
//订单详情页
$router->post('/order_detail','Order\OrderController@orderDetail');

//删除订单
$router->post('/order_delete','Order\OrderController@orderDelete');
//商品点赞

$router->post('/give_a_like','Order\GoodsController@give_a_like');
$router->post('/collect','Goods\GoodsController@collect');

$router->post('/give_a_like','Goods\GoodsController@give_a_like');
//个人中心
$router->post('/center','Center\CenterController@index');
//修改密码
$router->post('/changepwd','Center\CenterController@changepwd');
//功能模块
$router->post('/effect','Center\CenterController@effect');
//添加好友
$router->post('/addfriend','Center\CenterController@addFriend');
//用户个人中心
$router->post('/usercenter','Center\CenterController@userCenter');

