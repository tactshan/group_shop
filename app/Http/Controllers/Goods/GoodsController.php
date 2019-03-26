<?php

namespace App\Http\Controllers\Goods;

use App\Model\GoodsModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GoodsController extends Controller
{
    public function goodsList(Request $request){
        $info=GoodsModel::all()->toArray();
        if(!empty($info)){
            echo json_encode($info);
        }
    }
}
