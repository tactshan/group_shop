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
        $response=$this->checkToken($token,$uid);
        if($response=='true'){
            $data=GoodsModel::all()->toArray();
            $info=[
                'data'=>$data
            ];
            if(!empty($info)){
                echo json_encode($info);
            }
        }else{
            echo $response;
        }


    }
}
