<?php

namespace App\Http\Controllers\Excel;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Auth\TokenController as Token;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ExportExcelController extends Controller
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
            'numeric' => '数据不能为空',
            'required' => '数据不合法',
        ];

        return Validator::make($data, [
            'endTime'   => 'required|numeric',
            'startTime' => 'required|numeric',

        ], $message);
    }

    public function exportExcel(Request $request)
    {
        if ($request->isMethod('post')) {
            $tokenObject = new Token();
            $userId = $tokenObject->getUserId();
            $openid = $tokenObject->getOpenid();
            $permission = $tokenObject->getUserPermission();
            $permission = 1;    // 测试
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
            $startTime = request('startTime');
            $endTime = request('endTime');
            if ($startTime >= $endTime) {
                return response()->json([
                    'code' => 1002,
                    'msg' => '数据不合法'
                ]);
            }
            $orders = DB::table('orders')
                ->whereDate('created_at', '>=', date('Y-m-d', $startTime))
                ->whereDate('created_at', '<=', date('Y-m-d',$endTime))
                ->where('status', 1)
                ->get();
            $data[0] = [
                '序号', '下单时间', '公寓', '宿舍号', '送水时间段', '手机号'
            ];
            for ($i = 1; $i < count($orders) + 1; ++$i) {
                $data[$i][0] = $i;
                $data[$i][1] = $orders[$i - 1]->created_at;
                $data[$i][1] = $this->getDormitory($orders[$i - 1]->dormitory);
                $data[$i][2] = $orders[$i - 1]->room;
                $data[$i][3] = $this->getPeirod($orders[$i - 1]->time);
                $data[$i][4] = $orders[$i - 1]->mobile;
            }
            $start = date('Y-m-d', $startTime);
            $end = date('Y-m-d', $endTime);
            $excelName = $start . '--' . $end;
            $sheet = 'sheet1';

            Excel::create($excelName,function($excel) use ($data, $sheet){
                $excel->sheet($sheet, function($sheet) use ($data){
                    $sheet->rows($data);
                });
            })->export('xls');
        }
    }

    /**
     * 获取宿舍名
     * @param $id
     * @return string
     */
    private function getDormitory($id)
    {
        $dormitoryName = '';
        switch ($id) {
            case(1):
                $dormitoryName = '安悦南楼';
                break;
            case(2):
                $dormitoryName = '安悦北楼';
                break;
            case(3):
                $dormitoryName = '安美南楼';
                break;
            case(4):
                $dormitoryName = '安美北楼';
                break;
            case(5):
                $dormitoryName = '长思1号公寓';
                break;
            case(6):
                $dormitoryName = '长思2号公寓';
                break;
            case(7):
                $dormitoryName = '长思3号公寓';
                break;
            case(8):
                $dormitoryName = '长思4号公寓';
                break;
            case(9):
                $dormitoryName = '长思5号公寓';
                break;
            case(10):
                $dormitoryName = '长思6号公寓';
                break;
            case(11):
                $dormitoryName = '长智1号公寓';
                break;
            case(12):
                $dormitoryName = '长智2号公寓';
                break;
            case(13):
                $dormitoryName = '长智3号公寓';
                break;
            case(14):
                $dormitoryName = '长智4号公寓';
                break;
            case(15):
                $dormitoryName = '长智5号公寓';
                break;
            case(16):
                $dormitoryName = '长智6号公寓';
                break;
            default:
                $dormitoryName = '';
        }
        return $dormitoryName;
    }

    /**
     * 获取送水时间段
     * @param $id
     * @return string
     */
    private function getPeirod($id)
    {
        $period = '';
        switch ($id) {
            case(1):
                $period = '8:00 -- 9:00';
                break;
            case(2):
                $period = '12:00 -- 14:00';
                break;
            case(3):
                $period = '17:00 -- 19:00';
                break;
            default:
                $period = '';
        }
        return $period;
    }
}
