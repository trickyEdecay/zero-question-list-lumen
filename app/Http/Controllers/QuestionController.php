<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class QuestionController extends Controller
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

    public function getAllQuestions(Request $request){
        // step 1. 验证数据
        $page = $request->input('page',1);
        $validator = Validator::make($request->all(),[
            'page' => ['numeric'],
        ],[
            'page.numeric' => '客户端数据有误',
        ]);
        if($validator->fails()){
            return [
                'code' => '0001',
                'msg' => $validator->errors()->first()
            ];
        }

        // step 2. 计算到底有多少个页面
        $pageSize = 10;
        $total = DB::table('questions')->count();
        $pageCount = ceil($total/$pageSize);

        // step 3. 获取问题列表
        $questions = DB::table('questions')->skip($pageSize*($page-1))->take($pageSize)->select('id','content as question','time','user_id as userName')->get();

        foreach ($questions as &$question){
            $userId = $question->userName;
            if($userId===0){
                $question->userName = '匿名';
                continue;
            }
            $question->userName = DB::table('user')->where('id',$userId)->first()->name;
        }

        // step 4. 组装返回结果
        return [
            'code' => '0000',
            'msg' => '获取成功',
            'data' => [
                'pageCount' => $pageCount,
                'questions' => $questions
            ]
        ];
    }
}
