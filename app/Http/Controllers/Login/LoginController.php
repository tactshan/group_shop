<?php

namespace App\Http\Controllers\Login;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LoginController extends Controller
{
    //
	public function getSuccessToken()
	{
	    echo mt_rand('1111','5555');
   	}
   	public function check_login(Request $request)
    {
        $account = $request->input('account');
        $pwd = $request->input('pwd');
        echo $account;
        echo $pwd;
    }
}
