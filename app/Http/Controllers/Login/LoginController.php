<?php

namespace App\Http\Controllers\Login;

use App\Model\UserModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis;

class LoginController extends Controller
{
    //
	public function getAccessToken($id)
	{
        $str=time().$id.mt_rand(11111111111,99999999999);
        $str=md5($str);
        $token=substr($str,1,20);
        $redis_token="redis_token_str:".$id;
        Redis::hset($redis_token,'utoken',$token);
        return $token;
   	}
   	public function check_login(Request $request)
    {
        $account = $request->input('account');
        $pwd = $request->input('pwd');
        $e=strpos($account,'@');
        if(!empty($e)){
            $where=[
                'u_email'=>$account
            ];
        }else if(empty($e)&&11==strlen($account)){
            $where=[
                'u_phone'=>$account
            ];
        }else{
            $where=[
                'u_name'=>$account
            ];
        }
        $data = UserModel::where($where)->first();
        //var_dump($where);die;
        if(empty($data) || $data->u_pwd!==md5($pwd)){
            $resopnse=[
              'code'=>50001,
              'msg'=>'账号或密码错误1！'
            ];
            echo json_encode($resopnse);die;
        }
       //验证通过，生成token
        $token = $this->getAccessToken($data->uid);
        $uid = $data->uid;
        $resopnse=[
          'code'=>0,
          'msg'=>'success',
            'token'=>$token,
            'uid'=>$uid
        ];
        echo json_encode($resopnse);
    }
}
