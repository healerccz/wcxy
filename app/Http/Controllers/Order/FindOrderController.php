<?php

namespace App\Http\Controllers\Order;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Models\Order;

class FindOrderController extends Controller
{
    // 用户查找自己的所有卡片
    public function findOrder(Request $request) {
        if ($request->isMethod('post')) {
            $userId = 3;
            $packages = Order::where('user_id', $userId)->get();
            $data = array();
            for ($i = 0; $i < count($packages); ++$i) {
                $data[$i]['id'] = $packages[$i]->getAttribute('id');
                $data[$i]['start_time'] = $packages[$i]->getAttribute('start_time');
                $data[$i]['end_time'] = $packages[$i]->getAttribute('end_time');
                $data[$i]['dormitory'] = $packages[$i]->getAttribute('dormitory');
                $data[$i]['mobile'] = $packages[$i]->getAttribute('mobile');
                $data[$i]['note'] = $packages[$i]->getAttribute('note');
                $data[$i]['createdAt'] = strtotime($packages[$i]->getAttribute('created_at'));
                $data[$i]['status'] = strtotime($packages[$i]->getAttribute('status'));
            }
            return response()->json([
                'code'  => 2000,
                'data'  => $data
            ]);
        }
    }
}
