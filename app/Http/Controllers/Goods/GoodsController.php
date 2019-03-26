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
}
