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
        //收藏列表
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
        }elseif($type=='order'){    //订单列表
            $where=[
                'uid'=>$uid
            ];
            $data=OrderModel::where($where)->where('order_status','!=',2)->orderBy('c_time','desc')->get()->toArray();
            foreach ($data as $k=>$v){
                $data[$k]['c_time']=date('Y-m-d H:i:s',$v['c_time']);
                if($v['order_status']==0){
                    $data[$k]['order_status']='待支付';
                }else if($v['order_status']==0){
                    $data[$k]['order_status']='已支付';
                }
            }
        }elseif($type=='cart'){    //购物车列表
            $where=[
                'uid'=>$uid
            ];
            $data=CartModel::where($where)->get()->toArray();
        }elseif ($type=='friend'){     //好友列表
            $friend_key='friend_redis:'.$uid;
            $user_info=Redis::zRange($friend_key, 0, -1, true);
            $data=[];
            foreach ($user_info as $k=>$v){
                $userWhere=[
                    'uid'=>$k,
                ];
                $userInfo=UserModel::where($userWhere)->first()->toArray();
                $data[]=$userInfo;
            }
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
    public function changepwd(Request $request)
    {
        $pwd = $request->input('u_pwd');
        $new_pwd = $request->input('new_pwd');
        $uid = $request->input('uid');
        $token = $request->input('token');
        $response = $this->checkToken($token, $uid);
        if ($response == 'true') {
            $where = [
                'uid' => $uid
            ];
            $res = UserModel::where($where)->first();
            if (empty($res)) {
                $arr = [
                    'code' => 400,
                    'msg' => '用户不存在'
                ];
                echo json_encode($arr);
                die;
            }
            if ($res->u_pwd !== $pwd) {
                $arr = [
                    'code' => 404,
                    'msg' => '原密码错误'
                ];
                echo json_encode($arr);
                die;
            }
            $up_where = [
                'uid' => $uid
            ];
            $up_data = [
                'u_pwd' => $new_pwd
            ];
            $res = UserModel::where($up_where)->update($up_data);
            if ($res) {
                $arr = [
                    'code' => 0,
                    'msg' => '修改成功'
                ];
                echo json_encode($arr);
            } else {
                $arr = [
                    'code' => 500,
                    'msg' => '修改失败'
                ];
                echo json_encode($arr);
            }
        } else {
            echo $response;
        }
    }
    /**
     * 进入好友个人中心
     */
    public function userCenter(Request $request){
        $user_id=$request->input('user_id');
        if(empty($user_id)){
            $info=[
                'code'=>50000,
                'msg'=>'该用户不存在'
            ];
            echo json_encode($info);die;
        }
        $where=[
            'uid'=>$user_id
        ];
        $userInfo=UserModel::where($where)->first()->toArray();
        $info=[
            'code'=>1,
            'msg'=>$userInfo
        ];
        echo json_encode($info);
    }

    /**
     * 添加好友
     */
    public function addFriend(Request $request){
        $uid=$request->input('uid');
        $user_id=$request->input('user_id');
        $token=$request->input('token');
        $response=$this->checkToken($token,$uid);
        if($response=='true'){
            $friend_key='friend_redis:'.$uid;
            $rs=Redis::zAdd($friend_key,1,$user_id);
            if($rs){
                $info=[
                    'code'=>1,
                    'msg'=>'添加成功'
                ];
                echo json_encode($info);
            }else{
                $info=[
                    'code'=>50001,
                    'msg'=>'添加失败'
                ];
                echo json_encode($info);
            }
        }else{
            echo json_encode($response);die;
        }
    }
}
