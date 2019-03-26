<?php

namespace App\Http\Controllers\Goods;

use App\Model\GoodsModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis;

class GoodsController extends Controller
{
    public function goodsList(Request $request){
        //验证是否登录
        $token=$request->input('token');
        $uid=$request->input('uid');
        $key='redis_token_str:'.$uid;
        $redis_token=Redis::hget($key,'utoken');
        if(empty($uid)){
            $data=[
                'code'=>40001,
                'msg'=>'你还没有登录，请先登录'
            ];
            echo json_encode($data);
            exit;
        }
        if(empty($token)){
            $data=[
                'code'=>40001,
                'msg'=>'你还没有登录，请先登录'
            ];
            echo json_encode($data);
            exit;
        }elseif ($token!=$redis_token){
            $data=[
                'code'=>40010,
                'msg'=>'非法登录'
            ];
            echo json_encode($data);
            exit;
        }

        $info=GoodsModel::all()->toArray();
        if(!empty($info)){
            echo json_encode($info);
        }
    }

    //商品详情页
    public function goods_detail(Request $request)
    {
        $goods_id = $request->input('goods_id');
        $uid = $request->input('uid');
        $token = $request->input('token');
        //防非法
        $res = $this->checkToken($token,$uid);
        if($res=='true'){
            $where=[
                'goods_id'=>$goods_id
            ];
            $data = GoodsModel::where($where)->first()->toArray();
            //判断商品是否存在
            if(empty($data)){
                $response=[
                    'code'=>50010,
                    'msg'=>'商品不存在！'
                ];
                echo json_encode($response);die;
            }
            echo json_encode($data);
        }else{
            echo $res;
        }
    }
}
