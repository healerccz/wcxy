<?php

namespace App\Http\Controllers\Excel;

use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ExcelController extends Controller
{
    public function export($tableName, $sheetName, $data)
    {
        Excel::create($tableName, function ($excel) use ($data, $sheetName) {
            $excel->sheet($sheetName, function ($sheet) use ($data) {
                $sheet->rows(data);
            });
        })->export('xls');
    }
}
