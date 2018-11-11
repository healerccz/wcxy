<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class IsTokenValidController extends Controller
{
    public function isTokenValid(Request $request)
    {
        if ($request->isMethod('get')) {
            return response()->json([
                'code'  => 2000,
                'data'  => ''
            ]);
        }
    }
}
