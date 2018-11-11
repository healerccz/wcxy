<?php

namespace App\Http\Controllers\Order;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Models\Order;
use App\Http\Controllers\Auth\TokenController as Token;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class FindOrderController extends Controller
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
            'numeric' => '数据不合法',
        ];

        return Validator::make($data, [
            'page' => 'required|numeric',
        ], $message);
    }

    /**
     * 用户查询自己的订单
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function findOrder(Request $request) {
        if ($request->isMethod('get')) {
            $tokenObject = new Token();
            $userId = $tokenObject->getUserId();
            $openid = $tokenObject->getOpenid();
            $permission = $tokenObject->getUserPermission();
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
            $page = request('page');
            $page = $page <= 1 ? 1 : $page;
            $pageSize = 2;
            $offset = ($page - 1) * $pageSize;
            $orders = DB::table('orders')
                    ->where('user_id', $userId)
                    ->offset($offset)->limit($pageSize)
                    ->get();

            $data = array();
            for ($i = 0; $i < count($orders); ++$i) {
                $data[$i]['id'] = $orders[$i]->id;
                $data[$i]['time'] = $orders[$i]->time;
                $data[$i]['dormitory'] = $orders[$i]->dormitory;
                $data[$i]['room'] = $orders[$i]->room;
                $data[$i]['mobile'] = $orders[$i]->mobile;
                $data[$i]['note'] = $orders[$i]->note;
                $data[$i]['createdAt'] = $orders[$i]->created_at;
                $data[$i]['status'] = strtotime($orders[$i]->status);
            }
            return response()->json([
                'code'  => 2000,
                'data'  => $data
            ]);
        }
    }
}
