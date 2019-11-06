<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

use App\Model\User;

$router->get('/', function () use ($router) {
    return $router->app->version();
});
// 注册
$router->post('/index/user/register',['uses'=>'UserController@register']);
// 登录
$router->post('/index/user/login',['uses'=>'UserController@login']);

// 需要登录后才能访问的接口
$router->group(['middleware'=>'auth'],function() use ($router){

    // 注销
    $router->post('/index/user/logout',['uses'=>'UserController@logout']);

    // 获取用户名
    $router->get('/index/user/get_nick_name',['uses'=>'UserController@getNickName']);

    // 获取所有问题列表
    $router->get('/index/question/getAllQuestions',['uses'=>'QuestionController@getAllQuestions']);

    // 提交新的问题
    $router->post('/index/question/createQuestion',['uses'=>'QuestionController@createQuestion']);
});

$router->get('/test',function(){
//    $user = new User();
//    $result = $user->create('hellotest','asdjasldkj@qq.com','123456');
    return password_hash('123456',PASSWORD_BCRYPT);
//    return $result;
});