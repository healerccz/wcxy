<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Iwanli\Wxxcx\Wxxcx;
use Illuminate\Support\Facades\Validator;
use App\Http\Models\User;
use Carbon\Carbon;

class LoginController extends Controller
{
    protected $wxxcx;

    function __construct(Wxxcx $wxxcx)
    {
        $this->wxxcx = $wxxcx;
    }

    /**
     * 验证数据
     *
     * @param $data
     * @return mixed
     */
    public function validator($data)
    {
        $message = [
            'required'  => '数据不能为空',
            'string'   => '数据不合法'
        ];
        return Validator::make($data, [
            'code'  => 'required|string',
        ]);
    }

    /**
     * 登录
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function login(Request $request)
    {
        if ($request->isMethod('post')) {
            /*$validator = $this->validator($request->all());
            if ($validator->fails()) {
                $errors = $validator->errors->first();
                if ($errors == '数据不能空') {
                    $code = 1001;
                } else if ($errors == '数据不合法') {
                    $code = 1002;
                } else {
                    $code = 5000;
                }
                return response()->json([
                    'code'  => $code,
                    'msg'   => $errors
                ]);
            }*/
            $code = request('code', '');

            $user = $this->wxxcx->getLoginInfo($code);//根据 code 获取用户 session_key 等信息, 返回用户openid 和 session_key
//            $openid = $user['openid'];

//            $sessionKey = md5($openid . time());
//            var_dump($sessionKey);
//            session()->put($sessionKey, $openid);
//            session()->save();
//            var_dump(session($sessionKey));

            return response()->json([
                'code'  => 2000,
                'data'  => $user
            ]);
        }
    }
}