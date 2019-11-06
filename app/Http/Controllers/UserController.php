<?php

namespace App\Http\Controllers;

use App\Model\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

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

        $validator = Validator::make($request->all(),[
            'email' => ['required','email'],
            'password' => ['required','min:6'],
            'nickName' => ['required']
        ],[
            'email.required' => '邮箱不能为空',
            'email.email' => '邮箱格式不正确',
            'password.required' => '密码不能为空',
            'password.min' => '密码不能少于6位数',
            'nickName.required' => '昵称不能为空'
        ]);
        if($validator->fails()){
            return [
                'code' => '0001',
                'msg' => $validator->errors()->first()
            ];
        }

        // step 2. 判断一下这个邮箱是否已经被注册过了
//        $result = DB::table('user')->where('email',$email)->exists();
        $result = User::isExist($email);
        if($result){
            return [
                'code' => '0001',
                'msg' => '该邮箱已被注册'
            ];
        }

        // step 3. 创建新的账号
        $user = new User();
        $user->name = $nickName;
        $user->email = $email;
        $user->password = md5($password);
        $result = $user->save();
//        $result = DB::table('user')->insert([
//            [
//                'name' => $nickName,
//                'email' => $email,
//                'password' => md5($password)
//            ]
//        ]);
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

    public function login(Request $request){
        // step 1. 验证数据
        $email = $request->input('email');
        $password = $request->input('password');
        $validator = Validator::make($request->all(),[
            'email' => ['required','email'],
            'password' => ['required']
        ],[
            'email.required' => '邮箱不能为空',
            'email.email' => '邮箱格式不正确',
            'password.required' => '密码不能为空'
        ]);
        if($validator->fails()){
            return [
                'code' => '0001',
                'msg' => $validator->errors()->first()
            ];
        }

        // step 2. 验证用户存不存在
        $result = User::isExist($email);
        if(!$result){
            return [
                'code' => '0002',
                'msg' => '账号或密码错误'
            ];
        }

        // step 3. 验证用户密码是否正确
        $result = User::where(['email'=>$email,'password'=>md5($password)])->get();
        if(count($result)===0){
            return [
                'code' => '0003',
                'msg' => '账号或密码错误'
            ];
        }

        // step 4. 写入 session
        session_start();
        $_SESSION['name'] = $result[0]->name;
        $_SESSION['id'] = $result[0]->id;
        setcookie('user',$result[0]->id."::".$result[0]->name,time()+7*24*60*60,'/');
        return [
            'code' => '0000',
            'msg' => '登录成功'
        ];
    }

    public function getNickName(Request $request){

        return [
            'code' => '0000',
            'msg' => '获取用户名成功',
            'data' => $_SESSION['name']
        ];
    }

    public function logout(){
        session_destroy();
        session_unset();
        setcookie('user','',time()-1000,'/');
        return [
            'code' => '0000',
            'msg' => '登出成功!'
        ];
    }
}
