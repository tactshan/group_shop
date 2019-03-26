<?php

namespace App\Http\Controllers\Login;

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
   	public function check_login()
    {
        echo 'ok';
        echo 'ok';
        echo 'ok';
        echo 'ok';
        echo 'ok';
    }
}
