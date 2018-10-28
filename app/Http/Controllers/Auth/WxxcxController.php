<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Iwanli\Wxxcx\Wxxcx;
use Illuminate\Support\Facades\Validator;
use App\Http\Models\User;
use Carbon\Carbon;

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
            'code'          => 'required|numeric',
            'encryptedData' => 'required',
            'iv'            => 'required'
        ]);
    }

    /**
     * 更新数据
     *
     * @param $data
     * @return mixed
     */
    public function updateOrCreate($data)
    {
        $user = User::where('openid', $data['openid']);
        if (!$user) {
            $user = new User;
        }
        $user->save($data);
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
            $data = [
                'openid'        => $openid,
                'nick_name'     => $userInfo['nickName'] ? $userInfo['nickName'] : '',
                'city'          => $userInfo['city'] ? $userInfo['city'] : '',
                'province'      => $userInfo['province'] ? $userInfo['province'] : '',
                'avatar_url'    => $userInfo['avatarUrl'] ? $userInfo['avatarUrl'] : '',
                'union_id'      => $userInfo['unionId'] ? $userInfo['unionId'] : '',
                'updated_at'    => Carbon::now()
            ];
            $this->updateOrCreate($data);
            $sessionKey = md5($data['openid'] . $data['nick_name'] . time());
//            var_dump($sessionKey);
            session()->put($sessionKey, 1);
            session()->save();
//            var_dump(session($sessionKey));

            return response()->json([
                'nick_name'     => $userInfo['nickName'] ? $userInfo['nickName'] : '',
                'city'          => $userInfo['city'] ? $userInfo['city'] : '',
                'province'      => $userInfo['province'] ? $userInfo['province'] : '',
                'avatar_url'    => $userInfo['avatarUrl'] ? $userInfo['avatarUrl'] : '',
                'union_id'      => $userInfo['unionId'] ? $userInfo['unionId'] : '',
                'updated_at'    => Carbon::now()
            ]);
        }
    }
}