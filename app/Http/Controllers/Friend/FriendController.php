<?php

namespace App\Http\Controllers\Friend;

use App\Model\UserModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis;

class FriendController extends Controller
{
    public function Mutual_friend(Request $request)
    {
        $login_uid=$request->input('login_uid');
        $friend_uid=$request->input('friend_uid');
        $login_key='friend_redis:'.$login_uid;
        $friend_key='friend_redis:'.$friend_uid;
        //进入个人信息列表获取当前用户id
        $data = Redis::zInter('mutual_friend', [$login_key,$friend_key]);
        //获取两个id的共同好友
        $a_data=Redis::zRange('mutual_friend',0,-1,true);
        $data=[];
        foreach ($a_data as $k=>$v){
            $userWhere=[
                'uid'=>$k,
            ];
            $goodsInfo=UserModel::where($userWhere)->first()->toArray();
            $data[]=$goodsInfo;
        }
        echo json_encode($data);

    }
}
