<?php

namespace App\Http\Controllers\Order;

use App\Model\CartModel;
use App\Model\DetailModel;
use App\Model\GoodsModel;
use App\Model\OrderModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OrderController extends Controller
{
    public function createOrder(Request $request){
        $uid=$request->input('uid');
        $token=$request->input('token');
        //验证是否登录
        $response=$this->checkToken($token,$uid);
        if($response!="true"){
            echo $response;
        }else{
            //生成订单
            $cartWhere=[
                'uid'=>$uid
            ];
            $cartInfo=CartModel::where($cartWhere)->get();
            $goodsInfo=[];
            foreach ($cartInfo as $k=>$v){
                $goods_id=$v->goods_id;
                $goodsWhere=[
                    'goods_id'=>$goods_id
                ];
                $goodsArr=GoodsModel::where($goodsWhere)->first();
                $goodsArr['buy_number']=$v->buy_num;
                $goodsInfo[]=$goodsArr;
            }
            if(empty($goodsInfo)){
                $info=[
                    'code'=>40011,
                    'msg'=>"购物车没有商品",
                ];
                echo json_encode($info);exit;
            }

            //生成订单号
            $order_sn = OrderModel::generateOrderSN();
            $order_amount = 0;

            foreach($goodsInfo as $k=>$v){
                //计算订单价格 = 商品数量 * 单价
                $order_amount += $v->goods_price * $v->buy_number;
            }
            $data=[
                'order_num'      => $order_sn,
                'uid'           =>$uid,
                'c_time'      => time(),
                'total_amont'  => $order_amount
            ];
            $order_id = OrderModel::insertGetId($data);
            foreach ($goodsInfo as $k=>$v){
                //减少库存
                $goodsWhere=[
                    'goods_id'=>$v->goods_id
                ];
                $goodsUpdate=[
                    'goods_num'=>$v->goods_num-$v->buy_number
                ];
                $res=GoodsModel::where($goodsWhere)->update($goodsUpdate);
                $arr=[
                    'order_id'=>$order_id,
                    'goods_id'=>$v->goods_id,
                    'goods_name'=>$v->goods_name,
                    'buy_num'=>$v->buy_number,
                    'c_time'=>time(),
                    'uid'=>$uid,

                ];
                $result=DetailModel::insert($arr);
            }

            if($order_id){
                //清空购物车
                CartModel::where(['uid'=>$uid])->delete();
                $info=[
                    'code'=>1,
                    'msg'=>"下单成功",
                ];
                echo json_encode($info);
            }else{
                $info=[
                    'code'=>0,
                    'msg'=>"下单失败",
                ];
                echo json_encode($info);
            }

        }

    }

    //订单列表展示
    public function orderList(Request $request){
        $uid=$request->input('uid');
        $token=$request->input('token');
        //验证是否登录
        $response=$this->checkToken($token,$uid);
        if($response!="true"){
            echo $response;
        }else{
            $where=[
                'uid'=>$uid
            ];
            $orderInfo=OrderModel::where($where)->get();
            if(empty($orderInfo)){
                $info=[
                    'code'=>40111,
                    'msg'=>"您还没有订单",
                ];
                echo json_encode($info);
            }else{
                $info=[
                    'data'=>$orderInfo
                ];
                echo json_encode($info);
            }
        }
    }
}
