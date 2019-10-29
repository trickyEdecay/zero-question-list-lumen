<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function register(Request $request){
        // step 1. 验证数据
        $nickName = $request->input('nickName');
        $email = $request->input('email');
        $password = $request->input('password');

        // TODO: 验证数据

        // step 2. 判断一下这个邮箱是否已经被注册过了
        $result = DB::table('user')->where('email',$email)->exists();
        if($result){
            return [
                'code' => '0001',
                'msg' => '该邮箱已被注册'
            ];
        }

        // step 3. 创建新的账号
        $result = DB::table('user')->insert([
            [
                'name' => $nickName,
                'email' => $email,
                'password' => md5($password)
            ]
        ]);
        // 为什么会有这个错呢？
        if(!$result){
            return [
                'code' => '0002',
                'msg' => '注册时发生了未知的错误'
            ];
        }
        return [
            'code' => '0000',
            'msg' => '注册成功'
        ];

    }
    //
}
