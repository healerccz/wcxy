<?php

namespace App\Http\Controllers\Excel;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Excel;
use Illuminate\Support\Facades\Validator;

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
            'orderId' => 'required|numeric',
            'status' => 'required|numeric',

        ], $message);
    }

    public function exportExcel(Request $request)
    {

    }
}
