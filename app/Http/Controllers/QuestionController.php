<?php

namespace App\Http\Controllers;

use App\Model\Questions;
use App\Model\User;
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
        $total = Questions::count();
        $pageCount = ceil($total/$pageSize);

        // step 3. 获取问题列表
        $questions = Questions::skip($pageSize*($page-1))->take($pageSize)->select('id','content as question','time','user_id as userName')->get();

        foreach ($questions as &$question){
            $userId = $question->userName;
            if($userId===0){
                $question->userName = '匿名';
                continue;
            }
            $question->userName = User::where('id',$userId)->first()->name;
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

    public function createQuestion(Request $request){
        // step 1. 验证数据
        $content = $request->input('question');
        $isAnonymous = boolval($request->input('isAnonymous'));
        $validator = Validator::make($request->all(),[
            'question' => ['required','min:4'],
            'isAnonymous' => ['required','boolean']
        ],[
            'question.required' => '问题内容不能为空',
            'question.min' => '问题长度不能小于4',
            'isAnonymous.required' => '前端参数不正确'
        ]);
        if($validator->fails()){
            return [
                'code' => '0001',
                'msg' => $validator->errors()->first()
            ];
        }

        // step 2. 插入数据
        $time = date('Y-m-d H:i:s');
        $question = new Questions();
        $question->content = $content;
        $question->time = $time;
        $question->user_id = $isAnonymous?'0':$_SESSION['id'];
        $question->save();

        // step 3. 返回数据
        return [
            'code' => '0000',
            'msg' => '成功创建问题',
            'data' => [
                "content" => $content,
                "id" => $question->id,
                "time" => $time,
                "userName" => $isAnonymous?'匿名':$_SESSION['name']
            ]
        ];
    }
}
