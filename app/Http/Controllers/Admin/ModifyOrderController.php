<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Models\Order;
use App\Http\Controllers\Auth\TokenController as Token;

class ModifyOrderController extends Controller
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
        ];

        return Validator::make($data, [
            'orderId' => 'required|numeric',
            'status' => 'required|numeric',

        ], $message);
    }

    /**
     * update info
     * @param array $data
     * @return mixed
     */
    protected function update($orderId, $status)
    {
        $order = Order::where('id', $orderId)->first();
        $order->status = $status;
        $order->save();

        return 0;
    }

    public function modifyOrder(Request $request)
    {
        if ($request->isMethod('post')) {
            $tokenObject = new Token();
            $userId = $tokenObject->getUserId();
            $openid = $tokenObject->getOpenid();
            $permission = $tokenObject->getUserPermission();
            if ($permission != 1) {
                return response()->json([
                    'code'  => 4003,
                    'msg'   => '没有权限'
                ]);
            }
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
            $status  = request('status');
            $orderId = request('orderId');
            $ret = $this->update($orderId, $status);
            if ($ret == 0) {
                return response()->json([
                    'code'  => 2000,
                    'data'  => ''
                ]);

            } else {
                return response()->json([
                    'code' => 5000,
                    'msg'  => '未知错误'
                ]);
            }
        }
    }
}
