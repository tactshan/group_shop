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
        $this->checkToken($token,$uid);
        $arr=CartModel::where(['uid'=>$uid])->get()->toArray();
        if(!empty($arr)){
            echo json_encode($arr);
        }
    }
}
