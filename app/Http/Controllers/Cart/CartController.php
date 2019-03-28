<?php

namespace App\Http\Controllers\Cart;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\CartModel;
use App\Model\GoodsModel;

class CartController extends Controller
{
    //
    public function addCart(Request $request){
        $uid=$request->input('uid');
        $token=$request->input('token');
        $respon=$this->checkToken($token,$uid);
        if($respon=='true'){
            $goods_id=$request->input('goods_id');
            if(empty($goods_id)){
                $response=[
                    'code'=>403,
                    'msg'=>'商品不存在'
                ];
                echo json_encode($response);die;
            }
            $buy_num=$request->input('buy_num');
            $total_price=$request->input('total_price');
            $goods_info=GoodsModel::where(['goods_id'=>$goods_id])->first();
            if(!empty($goods_info)){
                $where=[
                    'goods_id'=>$goods_id,
                    'uid'=>$uid
                ];
                $res=CartModel::where($where)->first();
                if($res){
                    $data=[
                        'buy_num'=>$res->buy_num+$buy_num,
                        'total_price'=>$res->total_price+$total_price
                    ];
                    $where=[
                        'goods_id'=>$goods_id,
                        'uid'=>$uid
                    ];
                    $res1=CartModel::where($where)->update($data);
                    if($res1){
                        $response=[
                            'code'=>0,
                            'msg'=>'商品已存在，添加成功'
                        ];
                        echo json_encode($response);die;
                    }
                }else{
                    $data=[
                        'goods_id'=>$goods_id,
                        'goods_name'=>$goods_info->goods_name,
                        'goods_price'=>$goods_info->goods_price,
                        'buy_num'=>$buy_num,
                        'total_price'=>$total_price,
                        'ctime'=>time(),
                        'uid'=>$uid
                    ];
                    $res2=CartModel::insert($data);
                    if($res2){
                        $response=[
                            'code'=>0,
                            'msg'=>'添加成功',

                        ];
                        echo json_encode($response);die;
                    }else{
                        $response=[
                            'code'=>500,
                            'msg'=>'添加失败'
                        ];
                        echo json_encode($response);die;
                    }
                }
            }else{
                $response=[
                    'code'=>404,
                    'msg'=>'商品信息不存在'
                ];
                echo json_encode($response);die;
            }
        }else{
            echo $respon;die;
        }
    }
    public function list(Request $request){
        $uid=$request->input('uid');
        $token=$request->input('token');
        $response=$this->checkToken($token,$uid);
        if($response=='true'){
            $arr=CartModel::where(['uid'=>$uid])->get()->toArray();
            $data=[
                'data'=>$arr
            ];
            if(!empty($data)){
                echo json_encode($data);
            }else{
                $arr=[
                    'code'=>502,
                    'msg'=>'购物车为空'
                ];
                echo json_encode($arr);
            }
        }else{
            echo $response;
        }
    }
    public function delCart(Request $request){
        $cart_id=$request->input('cart_id');
        if(empty($cart_id)){
            $arr=[
                'code'=>404,
                'msg'=>'购物车商品不存在'
            ];
            echo json_encode($arr);die;
        }
        $res=CartModel::where(['id'=>$cart_id])->delete();
        if($res){
            $arr=[
                'code'=>0,
                'msg'=>'删除成功'
            ];
            echo json_encode($arr);
        }else{
            $arr=[
                'code'=>500,
                'msg'=>'删除失败'
            ];
            echo json_encode($arr);die;
        }
    }
}
