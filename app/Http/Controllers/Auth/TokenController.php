<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

class TokenController extends Controller
{
    /**
     * header
     * @var array
     */
    private $header = [
        "type" => "token",
        "alg"  => "HS256"
    ];

    /**
     * the id of user
     * @var integer $userId
     */
    private $userId;

    /**
     * the permission of user
     * @var string $permission
     */
    private $permission;

    /**
     * openid
     * @var
     */
    private $openid;

    /**
     * TokenController constructor.
     */
    public function __construct()
    {
        $this->userId = -1;
        $this->permission = -1;
        $this->openid = '';
    }

    /**
     * create payload
     * @param $userId
     * @param $permission
     * @return array
     */
    private function payload($userId, $permission, $openid)
    {
        return [
            "iss"       => "https://api.changxiaoyuan.com",
            "iat"       => $_SERVER['REQUEST_TIME'],
            "exp"       => $_SERVER['REQUEST_TIME'] + 7200,
            "GivenName" => "CreatShare",
            "userId"  => $userId,
            "permission"=> $permission,
            'openid'    => $openid
        ];
    }

    /**
     * encode data
     * @param $data
     * @return string
     */
    private function encode($data)
    {
        return base64_encode(json_encode($data));
    }

    /**
     * generate a signature
     * @param $header
     * @param $payload
     * @param string $secret
     * @return string
     */
    private function signature($header, $payload, $secret = 'secret')
    {
        return hash_hmac('sha256', $header.$payload, $secret);
    }

    /**
     * generate a token
     * @param $userId
     * @param $permission
     * @return string
     */
    public function createToken($userId, $permission, $openid)
    {
        $header = $this->encode($this->header);
        $payload = $this->encode($this->payload($userId, $permission, $openid));
        $signature = $this->signature($header, $payload);

        return $header . '.' .$payload . '.' . $signature;
    }

    /**
     * check a token
     * @param $jwt
     * @param string $key
     * @return array|string
     */
    public function checkToken($key = 'secret')
    {
        $jwt = $this->getToken();
        $token = explode('.', $jwt);
        if (count($token) != 3)
            return 'token invalid';

        list($header64, $payload64, $sign) = $token;
        if ($this->signature($header64 , $payload64) !== $sign)
            return 'token invalid';
        $header = json_decode(base64_decode($header64), JSON_OBJECT_AS_ARRAY);
        $payload = json_decode(base64_decode($payload64), JSON_OBJECT_AS_ARRAY);

        if ($header['type'] != 'token' || $header['alg'] != 'HS256')
            return 'token invalid';
        if ($payload['iss'] != 'https://api.changxiaoyuan.com' || $payload['GivenName'] != 'CreatShare')
            return 'token invalid';

        if (isset($payload['exp']) && $payload['exp'] < time())
            return 'timeout';
        $this->userId = $payload['userId'];
        $this->permission = $payload['permission'];
        $this->openid = $payload['openid'];

        return 0;
    }

    /**
     * get a token
     * @return null
     */
    public function getToken()
    {
        $token = null;
        if (isset($_SERVER['HTTP_AUTHORIZATION']))
            $token = $_SERVER['HTTP_AUTHORIZATION'];
        return $token;
    }

    /**
     * get the id of member
     * @return int $userId
     */
    public function getUserId()
    {
        $this->checkToken();
        return $this->userId;
    }

    /**
     * get the permission of member
     * @return string permission
     */
    public function getUserPermission()
    {
        $this->checkToken();
        return $this->permission;
    }

    /**
     * get openid
     * @return string
     */
    public function getOpenid()
    {
        $this->checkToken();
        return $this->openid;
    }
}
