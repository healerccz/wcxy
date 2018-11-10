<?php

namespace App\Http\Controllers\Order;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Models\Order;
use App\Http\Controllers\Auth\TokenController as Token;

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
            'time' => 'required|string|size:1',
            'dormitory' => 'required|string|min:1|max:2',
            'room' => 'required|string|size:3',
            'mobile' => ['required', 'regex:/^1+[35678]+\\d{9}/'],
            'note' => 'nullable|string',
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
     * 用户下单
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addOrder(Request $request)
    {
        if ($request->isMethod('post')) {
            $validator = $this->validator($request->all());
            if ($validator->fails()) {
                $errors = $validator->errors()->first();
                if ($errors == "数据不能为空") {
                    $code = 1001;
                } else if ($errors == "数据不合法") {
                    $code = 1002;
                } else {
                    $code = 5000;
                }
                return response()->json([
                    'code' => $code,
                    'msg' => $errors
                ]);
            }
            $tokenObject = new Token();
            $userId = $tokenObject->getUserId();
            $openid = $tokenObject->getOpenid();
            $permission = $tokenObject->getUserPermission();

            $time = request('time');
            $dormitory = request('dormitory');
            $mobile = request('mobile');
            $note = request('note');
            $room = request('room');
            $data = [
                'time'      => $time,
                'dormitory' => $dormitory,
                'mobile'    => $mobile,
                'note'      => $note,
                'status'    => 0,
                'user_id'   => $userId,
                'room'      => $room
            ];
            $ret = $this->create($data);
            if ($ret) {
                return response()->json([
                    'code' => 2000,
                    'data' => ''
                ]);
            } else {
                return response()->json([
                    'code'  => 5000,
                    'msg'   => '未知错误'
                ]);
            }
        }
    }
}
