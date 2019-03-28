<?php

namespace App\Http\Controllers\Center;

use App\Model\CartModel;
use App\Model\GoodsModel;
use App\Model\OrderModel;
use App\Model\UserModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis;

class CenterController extends Controller
{
    /**
     * 个人中心首页
     * @param Request $request
     */
    public function index(Request $request){
        $uid=$request->input('uid');
        $token=$request->input('token');
        $response=$this->checkToken($token,$uid);
        if($response!='true'){
            echo $response;
        }else{
            $where=[
                'uid'=>$uid,
            ];
            $userInfo=UserModel::where($where)->first()->toArray();
            if($userInfo){
                $info=[
                    'code'=>1,
                    'msg'=>$userInfo
                ];
                echo json_encode($info);
            }else{
                $info=[
                    'code'=>40010,
                    'msg'=>'非法登录'
                ];
                return  json_encode($info);
            }
        }
    }

    //功能模块
    public function  effect(Request $request){
        $uid=$request->input('uid');
        $type=$request->input('type');
        if($type=='collection'){
            $collection_key='collect_number_uid:'.$uid;
            $collectInfo=Redis::zRange($collection_key, 0, -1, true);
            $data=[];
            foreach ($collectInfo as $k=>$v){
                $goodsWhere=[
                    'goods_id'=>$k,
                ];
                $goodsInfo=GoodsModel::where($goodsWhere)->first()->toArray();
                $data[]=$goodsInfo;
            }
        }elseif($type=='order'){
            $where=[
                'uid'=>$uid
            ];
            $data=OrderModel::where($where)->where('order_status','!=',2)->get()->toArray();
            foreach ($data as $k=>$v){
                $data[$k]['c_time']=date('Y-m-d H:i:s',$v['c_time']);
            }
        }elseif($type=='cart'){
            $where=[
                'uid'=>$uid
            ];
            $data=CartModel::where($where)->get()->toArray();
        }else{
            $collection_key='goods_give_a_like:'.$uid;
            $collectInfo=Redis::zRange($collection_key, 0, -1, true);
            $data=[];
            foreach ($collectInfo as $k=>$v){
                $goodsWhere=[
                    'goods_id'=>$k,
                ];
                $goodsInfo=GoodsModel::where($goodsWhere)->first()->toArray();
                $data[]=$goodsInfo;
            }
        }
        if($data){
            $info=[
                'type'=>$type,
                'code'=>1,
                'msg'=>$data
            ];
            echo json_encode($info);
        }else{
            $info=[
                'code'=>40111,
                'msg'=>"您还没有进行此操作",
            ] ;
            echo json_encode($info);
        }
    }
}
