<?php

return [
	/**
	 * 小程序APPID
	 */
    'appid' => 'wxdeb85021df04d56f',
    /**
     * 小程序Secret
     */
    'secret' => 'a67e4bf9f9bf47685d421fff309fea5b',
    /**
     * 小程序登录凭证 code 获取 session_key 和 openid 地址，不需要改动
     */
    'code2session_url' => "https://api.weixin.qq.com/sns/jscode2session?appid=%s&secret=%s&js_code=%s&grant_type=authorization_code",
];
