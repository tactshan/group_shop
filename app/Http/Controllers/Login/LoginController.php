<?php

namespace App\Http\Controllers\Login;

use App\Model\UserModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LoginController extends Controller
{
    //
	public function getAccessToken($id)
	{
        $str=time().$id.mt_rand(111111,999999);
        $token=substr($str,10,20);
        return $token;
   	}
   	public function check_login(Request $request)
    {
        $account = $request->input('account');
        $pwd = $request->input('pwd');
        $where=[
          'u_name'=>$account
        ];
        $data = UserModel::where($where)->first();
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
