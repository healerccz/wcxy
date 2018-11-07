<?php

namespace App\Http\Controllers\Order;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Models\Order;

class AddOrderController extends Controller
{
    /**
    * Get a validator for an incoming registration request.
    *
    * @param  array  $data
    * @return \Illuminate\Contracts\Validation\Validator
    */
    protected function validator(array $data)
    {
        $message = [
            'required' => '数据不能为空',
            'size' => '数据不合法',
            'string' => '数据不合法',
            'regex:/^1+[35678]+\\d{9}/' => '数据不合法'
        ];

        return Validator::make($data, [
            'start_time' => 'required|string|size:1',
            'end_time' => 'required|string|size:1',
            'dormitory' => 'required|string|size:2',
            'mobile' => ['regex:/^1+[35678]+\\d{9}/'],
            'note' => 'required|string',
        ], $message);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Http\Models\Order
     */
    protected function create(array $data)
    {
        return Order::create($data);
    }

    /**
     * 用户注册
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        if ($request->isMethod('post')) {
            $validator = $this->validator($request->all());
            if ($validator->fails()) {
                $errors = $validator->errors()->first();
                if ($errors == "数据不能为空") {
                    $code = 1001;
                } else if ($errors == "数据不合法") {
                    $code = 1002;
                } else if ($errors == "用户已存在") {
                    $code = 1003;
                } else {
                    $code = 5001;
                }
                return response()->json([
                    'code' => $code,
                    'msg' => $errors
                ]);
            }
            //判断密码是否一致
            $password = request('password');
            $passwordConfirm = request('passwordConfirm');
            if ($password != $passwordConfirm) {
                return response()->json([
                    'code'  => 3001,
                    'msg'   => '密码不一致'
                ]);
            }
            $mobile = request('mobile');
            $code_request = request('code');
            $code = session(base64_encode($mobile));
//            $code = Redis::get('name');
//            var_dump($code);
//            var_dump($code_request);
            // 判断验证码是否正确
            if ($code == $code_request) {
                session()->put(base64_encode($mobile), null);
                $data = [
                    'mobile'    => $mobile,
                    'password'  => bcrypt($password)
                ];
                $res = $this->create($data);
                if (!$res) {
                    return response()->json([
                        'code' => 5001,
                        'msg' => '未知错误'
                    ]);
                }
                return response()->json([
                    'code' => 2000,
                    'data' => $mobile
                ]);
            } else {
                return response()->json([
                    'code'  => 3002,
                    'msg'   => '验证码错误'
                ]);
            }
        }
    }
}
