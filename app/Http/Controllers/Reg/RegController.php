<?php

namespace App\Http\Controllers\Reg;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RegController extends Controller
{
    public function register(Request $request){
        $data=$request->input();
        $pwd=$data['pwd'];
    }
}
