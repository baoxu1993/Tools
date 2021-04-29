<?php
/**
 * Created by PhpStorm.
 * User: zb
 * Date: 2021/4/29
 * Time: 16:55
 */

namespace baoxu1993\Tools;


class Config
{
    /**
     * 获取系统配置
     * @param string $key
     * @param string $default
     * @param bool   $parse
     * @return array|string
     */
    public static function getSystemConfig(string $key, string $default = '', $parse = true)
    {
        $params = explode('.', $key);
        if (empty($params)) {
//            autoException(10005);
            return $default;
        }

        $configIds = cache("SystemConfig:{$params[0]}");
        if (empty($configIds)) {
            $configIds = (new \App\Model\System\SystemConfig())::query()->where('type', $params[0])->pluck('id')->toArray();
            cache("SystemConfig:{$params[0]}", $configIds, -1);
        }

        $config = \App\Model\System\SystemConfig::cacheList($configIds);
        if (isset($params[1]) && !empty($params[1])) {
            $config = array_column($config, NULL, 'name');
            if (isset($config[$params[1]])) {
                return $config[$params[1]]['value'];
            } else {
                //autoException(10014, "参数{$key}不存在");
                return $default;
            }
        } else {
            $data = $config;
            if ($parse) {
                $data = [];
                foreach ($config as $k => $v) {
                    $data[$v['name']] = $v['value'];
                }
            }
            return $data;
        }
    }

    /**
     * 获取用户微信配置
     * @param int    $user_id
     * @param string $type
     * @return array
     * @throws \Exception
     */
    public static function getUserWxConfig(int $user_id, string $type = "officialAccount")
    {
        $returnConfig = [];
        switch ($type) {
            case 'officialAccount':
                //获取授权配置
                $configId = (new \App\Model\User\Wechat\UserWechatOpenplatformAuth())::query()->latest()
                    ->where('user_id', $user_id)
                    ->where('type', 1)
                    ->where('status', 1)
                    ->value('id');
                if ($configId) {
                    $config = \App\Model\User\Wechat\UserWechatOpenplatformAuth::cacheList($configId);
                    $returnConfig = [
                        'app_id' => $config['app_id'],
                        'refreshToken' => $config['refresh_token']
                    ];
                }
                break;
            case 'payment'://微信支付
                (new \App\Model\User\UserPluginUse())->checkPluginPaymentHas($user_id);
                $payType = \App\Model\User\Online\UserOnlinePay::cacheList($user_id);
                if (empty($payType['wechat_pay'])) {
                    autoException(10049);
                } else {
                    switch ($payType['wechat_type']) {
                        case 1://平台支付
                            $returnConfig = [];
                            break;
                        case 2://平台子商户
                            $config = \App\Model\User\Online\UserOnlinePayWechatSub::cacheList($user_id);
                            if (!empty($config)) {
                                $wechatConfig = \App\Model\User\Wechat\UserWechatOfficialaccount::cacheList($user_id);
                                $returnConfig = [
                                    'sub_app_id' => isset($wechatConfig['app_id']) ? $wechatConfig['app_id'] : '',
                                    'sub_mch_id' => $config['mch_id']
                                ];
                            }
                            break;
                        case 3://自有支付商户
                            $config = \App\Model\User\Online\UserOnlinePayWechatSelf::cacheList($user_id);
                            if (!empty($config)) {
                                $wechatConfig = \App\Model\User\Wechat\UserWechatOfficialaccount::cacheList($user_id);
                                $returnConfig = [
                                    'app_id' => $wechatConfig['app_id'],
                                    'key' => $config['mch_key'],
                                    'mch_id' => $config['mch_id']
                                ];
                            }
                            break;
                        default:
                            $returnConfig = [];
                            break;
                    }
                }
                break;
        }
        return $returnConfig;
    }
}