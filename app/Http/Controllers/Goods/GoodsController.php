<?php

namespace App\Http\Controllers\Goods;

use App\Model\GoodsModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis;

class GoodsController extends Controller
{
    //商品列表页
    public function goodsList(Request $request)
    {
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
    }

    //商品详情页
    public function goods_detail(Request $request)
    {
        $goods_id = $request->input('goods_id');
            $access_key="goods_access";
            $num=Redis::hGet($access_key,"$goods_id");
            if(empty($num)){
                Redis::hSet($access_key,"$goods_id",'1');
            }else{
                $num+=1;
                Redis::hSet($access_key,"$goods_id",$num);
            }
            $access_num=$num=Redis::hGet($access_key,"$goods_id");
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
            if (empty($data)) {
                $response = [
                    'code' => 50010,
                    'msg' => '商品不存在！'
                ];
                echo json_encode($response);die;
            }
            $data['access_num']=$access_num;
            echo json_encode($data);
    }

    //商品点赞
    public function give_a_like(Request $request)
    {
        $goods_id = $request->input('goods_id');
        $uid = $request->input('uid');
        $token = $request->input('token');
        //验证防非法
        $response=$this->checkToken($token,$uid);
        if($response=='true'){
            //根据商品id增加点赞数量
            $like_key = 'goods_give_a_like:'.$uid;
            $name = $goods_id;
            //查询有序集合中是否存在点赞
            $num = Redis::zScore($like_key,$name);
            if($num){
                //做累加
                $res = Redis::zIncrby($like_key,1,$name);
            }else{
                //做新增
                $res = Redis::zAdd($like_key,1,$name);
            }
            if(!$res){
                $response=[
                  'code'=>0,
                    'msg'=>'success'
                ];
                echo json_encode($response);
            }
        }else{
            echo $response;
        }

    }

}
