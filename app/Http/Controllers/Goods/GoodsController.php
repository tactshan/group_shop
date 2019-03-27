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
//        $token=$request->input('token');
//        $uid=$request->input('uid');
//        $response=$this->checkToken($token,$uid);
//        if($response=='true'){
            $key="goods";
            $goodsInfo=Redis::hget($key,'goodsInfo');
            if(!empty($goodsInfo)){
                $data=unserialize($goodsInfo);
            }else{
                $data=GoodsModel::all()->toArray();
                $goodsArr=serialize($data);
                Redis::hset($key,'goodsInfo',$goodsArr);
                Redis::expire($key,86400);
            }
            $info=[
                'data'=>$data
            ];
            if(!empty($info)){
                echo json_encode($info);
            }
//        }else{
//            echo $response;
//        }


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
            $key="goods_id:".$goods_id;
            $goods_info=Redis::hget($key,'goods');
            if(empty($goods_id)){
                $data=unserialize($goods_info);
            }else{
                $where=[
                    'goods_id'=>$goods_id
                ];
                $data = GoodsModel::where($where)->first()->toArray();
                $goodsArr=serialize($data);
                Redis::hset($key,'goods',$goodsArr);
            }
            //判断商品是否存在
            if(empty($data)){
                $response=[
                    'code'=>50010,
                    'msg'=>'商品不存在！'
                ];
                echo json_encode($response);die;
            }
            $data['uid']=$uid;
            echo json_encode($data);
        }else{
            echo $res;
        }
    }
}
