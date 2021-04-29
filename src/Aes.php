<?php
/**
 * Created by PhpStorm.
 * User: zb
 * Date: 2021/4/29
 * Time: 16:40
 */

namespace baoxu1993\Tools;


class Aes
{
    /**
     * @param string      $string
     * @param string|NULL $iv
     * @param bool        $iv_base64
     * @return array
     */
    public static function aesEncrypt(string $string, string $iv = NULL, bool $iv_base64 = false)
    {
        $encrypt_key = config("aes.encrypt_key");
        $encrypt_type = config("aes.encrypt_type");
        if ($iv === NULL) {
            $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($encrypt_type));
        }
        $encrypted = openssl_encrypt($string, $encrypt_type, $encrypt_key, 0, $iv);
        return [
            'encryptString' => urlencode($encrypted),
            'iv' => $iv_base64 ? base64_encode($iv) : $iv
        ];
    }

    /**
     * @param string $encryptString
     * @param string $iv
     * @param bool   $iv_base64
     * @return false|string
     */
    public static function aesDecrypt(string $encryptString, string $iv, bool $iv_base64 = false)
    {
        $encrypt_key = config("aes.encrypt_key");
        $encrypt_type = config("aes.encrypt_type");
        return openssl_decrypt($encryptString, $encrypt_type, $encrypt_key, 0, $iv_base64 ? base64_decode($iv) : $iv);
    }


    /**
     * 密码加密
     * @param string $pass
     * @return string
     * @throws \Exception
     */
    public static function PasswordEncrypt(string $pass)
    {
        $data = self::aesEncrypt($pass);
        return base64_encode(urldecode($data['encryptString']) . '::' . $data['iv']);
    }

    /**
     * 密码解密
     * @param string $pass
     * @return false|string
     * @throws \Exception
     */
    public static function PasswordDecrypt(string $pass)
    {
        list($encrypted_data, $iv) = explode('::', base64_decode($pass), 2);
        return self::aesDecrypt($encrypted_data, $iv);
    }
}