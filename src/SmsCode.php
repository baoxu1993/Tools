<?php
/**
 * Created by PhpStorm.
 * User: zb
 * Date: 2021/4/29
 * Time: 16:24
 */

namespace baoxu1993\Tools;


class SmsCode
{
    /**
     * 生成短信验证码
     * @param        $phone
     * @param string $scene
     * @param int    $user_id
     * @param int    $seconds
     * @param int    $length
     * @throws \Exception
     */
    public function makeSmsCode($phone, $scene = "default", $user_id = 0, $seconds = 300, $length = 4)
    {
        $code = getRand($length, '0123456789');
        $cacheKey = "SmsVerifyCode:{$scene}:{$phone}";

        //验证上次发送时间
        if (cache($cacheKey)) {
            $lastKey = "SmsVerifyCodeTime:{$scene}:{$phone}";
            if (cache($lastKey)) {
                autoException(3);
            }
            cache($lastKey, time(), 60);
        }
        cache($cacheKey, $code, $seconds);
        QueuePush("User_SendSmsVerifyCodeJob", [
            'phone' => $phone,
            'code' => $code,
            'user_id' => $user_id,
            'create_time' => time()
        ]);
    }

    /**
     * 校验短信验证码
     * @param        $phone
     * @param        $code
     * @param string $scene
     * @throws \Exception
     */
    public function checkSmsCode($phone, $code, $scene = "default")
    {
        $cacheKey = "SmsVerifyCode:{$scene}:{$phone}";
        $cacheCode = cache($cacheKey);
        if (!$cacheCode || $cacheCode != (int)$code) {
            autoException(4);
        }

        cacheDel($cacheKey);
    }
}