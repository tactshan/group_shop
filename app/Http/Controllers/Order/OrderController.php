<?php

namespace App\Http\Controllers\Order;

use App\Model\CartModel;
use App\Model\DetailModel;
use App\Model\GoodsModel;
use App\Model\OrderModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

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
                    'goods_price'=>$v->goods_price,
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
                'uid'=>$uid,
            ];
            $orderInfo=OrderModel::where($where)->where('order_status','!=',2)->get()->toArray();
            if(empty($orderInfo)){
                $info=[
                    'code'=>40111,
                    'msg'=>"您还没有订单",
                ];
                echo json_encode($info);
            }else{
                foreach ($orderInfo as $k=>$v){
                    $orderInfo[$k]['c_time']=date('Y-m-d',$v['c_time']);
                }
                $info=[
                    'code'=>1,
                    'data'=>$orderInfo
                ];
                echo json_encode($info);
            }
        }
    }

    //订单详情页
    public function orderDetail(Request $request)
    {
        //验证用户身份
        $uid=$request->input('uid');
        $token=$request->input('token');
        $response=$this->checkToken($token,$uid);
        if($response=='true'){
            //根据订单号查询订单表获取订单id
            $order_num=$request->input('order_num');
            if(empty($order_num)){
                $response=[
                  'code'=>50050,
                  'msg'=>'订单号为空'
                ];
                echo json_encode($response);die;
            }
            $order_where=[
                'order_num'=>$order_num
            ];
            $order_data=OrderModel::where($order_where)->first();
            if(empty($order_data)){
                $response=[
                    'code'=>50051,
                    'msg'=>'订单数据为空'
                ];
                echo json_encode($response);die;
            }
            $order_id = $order_data->order_id;
            //根据订单id查询订单详情表
            $order_detail_where=[
                'order_id'=>$order_id
            ];
            $detail_data = DetailModel::where($order_detail_where)->get()->toArray();
            if(empty($detail_data)){
                $response=[
                    'code'=>50052,
                    'msg'=>'订单数据为空'
                ];
                echo json_encode($response);die;
            }
            foreach($detail_data as $k=>$v){
                $v['order_num']=$order_num;
                $response_detail_data[]=$v;
            }
            $data=[
                'code'=>0,
                'msg'=>'success',
              'data'=>$response_detail_data
            ];
            echo json_encode($data);
        }else{
            echo $response;
        }

    }

    //订单删除
    public function orderDelete(Request $request)
    {
        $order_id=$request->input('order_id');
        if(empty($order_id)){
            $response=[
              'code'=>50501,
              'msg'=>'订单号不能为空！'
            ];
            echo json_encode($response);die;
        }
        $where=[
          'order_id'=>$order_id
        ];
        $res=OrderModel::where($where)->update(['order_status'=>2]);
        if($res){
            $response=[
              'code'=>0,
              'msg'=>'success'
            ];
            echo json_encode($response);die;
        }
    }
    
}
