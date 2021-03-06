<?php

namespace App\Http\Controllers\Reg;

use App\Model\UserModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RegController extends Controller
{
    public function register(Request $request){
        $data=$request->input();
        if($data['u_pwd']!=$data['repwd']){
            $info=[
                'code'=>0,
                'msg'=>'密码与确认密码一致'
            ];
            echo json_encode($info);
            exit;
        }
        $pwd=md5($data['u_pwd']);
        $where=[
            'u_name'=>$data['u_name'],
        ];
        $userInfo=UserModel::where($where)->first();
        $where1=[
            'u_email'=>$data['u_email']
        ];
        $userInfo1=UserModel::where($where1)->first();
        $where2=[
            'u_phone'=>$data['u_phone']
        ];
        $userInfo2=UserModel::where($where2)->first();
        if(!empty($userInfo)){
            $info=[
                'code'=>0,
                'msg'=>'该用户名已存在，请重新填写'
            ];
            echo json_encode($info);
            exit;
        }else if(!empty($userInfo1)){
            $info=[
                'code'=>0,
                'msg'=>'该邮箱已存在，请重新填写'
            ];
            echo json_encode($info);
            exit;
        }else if(!empty($userInfo2)){
            $info=[
                'code'=>0,
                'msg'=>'该电话号已存在，请重新填写'
            ];
            echo json_encode($info);
            exit;
        }else{
            $arr=[
                'u_name'=>$data['u_name'],
                'u_pwd'=>$pwd,
                'u_email'=>$data['u_email'],
                'u_age'=>$data['u_age'],
                'u_phone'=>$data['u_phone']
            ];
            $rs=UserModel::insertGetId($arr);
            if($rs){
                $info=[
                    'code'=>1,
                    'msg'=>'注册成功'
                ];
                echo json_encode($info);
            }else{
                $info=[
                    'code'=>0,
                    'msg'=>'注册失败'
                ];
                echo json_encode($info);
            }
        }

    }
}
