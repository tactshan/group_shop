<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Redis;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;



    public function checkToken($token,$uid){
        $key='redis_token_str:'.$uid;
        $redis_token=Redis::hget($key,'utoken');
        if(empty($uid)){
            $data=[
                'code'=>40001,
                'msg'=>'你还没有登录，请先登录'
            ];
            return json_encode($data);
        }
        if(empty($token)){
            $data=[
                'code'=>40001,
                'msg'=>'你还没有登录，请先登录'
            ];
            return json_encode($data);
        }elseif ($token!=$redis_token){
            $data=[
                'code'=>40010,
                'msg'=>'非法登录'
            ];
            return  json_encode($data);
        }
        return true;
    }
}
