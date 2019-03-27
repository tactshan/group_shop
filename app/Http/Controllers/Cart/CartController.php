<?php

namespace App\Http\Controllers\Cart;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\CartModel;

class CartController extends Controller
{
    //
    public function list(Request $request){
        $uid=$request->input('uid');
        $token=$request->input('token');
        $response=$this->checkToken($token,$uid);
        if($response=='true'){
            $arr=CartModel::where(['uid'=>$uid])->get()->toArray();
            $data=[
                'data'=>$arr
            ];
            if(!empty($data)){
                echo json_encode($data);
            }
        }else{
            echo $response;
        }
    }
}
