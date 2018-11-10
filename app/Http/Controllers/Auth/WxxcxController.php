<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Iwanli\Wxxcx\Wxxcx;
use Illuminate\Support\Facades\Validator;
use App\Http\Models\User;
use Carbon\Carbon;
use App\Http\Controllers\Auth\TokenController as Token;

class WxxcxController extends Controller
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
            'numeric'   => '数据不合法'
        ];
        return Validator::make($data, [
            'code'          => 'required|string',
            'encryptedData' => 'required',
            'iv'            => 'required'
        ], $message);
    }

    /**
     * 更新数据
     *
     * @param $data
     * @return mixed
     */
    public function updateOrCreate($data)
    {
        $user = User::where('openid', $data['openid'])->first();
        if (!$user) {
            $user = new User;
        }
        $user->openid = $data['openid'];
        $user->updated_at = $data['updated_at'];
        $user->save($data);

        return $user;
    }

    /**
     * 登录并获取用户信息
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function getWxUserInfo(Request $request)
    {
        if ($request->isMethod('post')) {
            $validator = $this->validator($request->all());
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
            }
            $code = request('code', '');
            $encryptedData = request('encryptedData', '');  // encryptedData 和 iv 在小程序端使用 wx.getUserInfo 获取
            $iv = request('iv', '');

            $user = $this->wxxcx->getLoginInfo($code);//根据 code 获取用户 session_key 等信息, 返回用户openid 和 session_key
            $openid = $user['openid'];
            $userInfo = $this->wxxcx->getUserInfo($encryptedData, $iv); // 获取用户信息
            $userInfo = json_decode($userInfo, true);
            $data = [
                'openid'        => $openid,
                'updated_at'    => Carbon::now()
            ];
            $user = $this->updateOrCreate($data);
            $permission = $user->permission;
            $userId = $user->id;

            $tokenObject = new Token();
            $token = $tokenObject->createToken($userId, $permission, $openid);

            return response()->json([
                'code'  => 2000,
                'data'  => "",
            ])->header('Authorization', $token);
        }
    }
}