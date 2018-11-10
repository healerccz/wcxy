<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Auth\TokenController as Token;
use Illuminate\Support\Facades\DB;

class FindTodayOrderController extends Controller
{
    /**
     * 管理员查询当天订单
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function findTodayOrder(Request $request) {
        if ($request->isMethod('get')) {
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
            $page = request('page');
            $page = $page <= 1 ? 1 : $page;
            $pageSize = 2;
            $offset = ($page - 1) * $pageSize;
            $data = time();
            $orders = DB::table('orders')
                ->where('create_at', '=', date('d'))
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
                $data[$i]['createdAt'] = strtotime($orders[$i]->created_at);
                $data[$i]['status'] = strtotime($orders[$i]->status);
            }
            return response()->json([
                'code'  => 2000,
                'data'  => $data
            ]);
        }
    }
}
