<?php


namespace baoxu1993\Tools;
//
//use App\Model\Sms\SmsLog;
//use App\Model\Sms\SmsTemplate;
//use App\Model\User\Sms\UserSmsSign;
use GuzzleHttp\Client;
use Overtrue\EasySms\EasySms;

class SendSms
{
    private static $template = [];
    private static $smsConfig;
    private static $content = "";

    private static function getTemplate($code)
    {
        self::$template = SmsTemplate::query()->latest()->where('code', $code)->where('status', 1)->first();
        self::$template = empty(self::$template) ? false : self::$template->toArray();
        return self::$template;
    }

    private static function getConfig()
    {
        self::$smsConfig = getSystemConfig(self::$template['type_id']);
        return empty(self::$smsConfig) ? false : self::$smsConfig;
    }

    /**
     * 获取短信方式对应的配置
     * @return array
     */
    private static function getSmsConfig()
    {
        $data = [
            self::$smsConfig['gateway'] => [
                'access_key_id' => self::$smsConfig['access_key'],
                'access_key_secret' => self::$smsConfig['access_secret'],
                'sign_name' => self::$smsConfig['sign_name']
            ],
            'errorlog' => [
                'file' => '/tmp/easy-sms.log',
            ]
        ];

        $config = [
            // HTTP 请求的超时时间（秒）
            'timeout' => 5.0,
            // 默认发送配置
            'default' => [
                // 默认可用的发送网关
                'gateways' => [self::$smsConfig['gateway']],
            ],
            // 可用的网关配置
            'gateways' => $data,
        ];
        return $config;
    }

    /**
     * 格式参数
     * @param     $data
     * @param int $user_id
     * @return array
     */
    private static function formatData($data, $user_id = 0)
    {
        $sign =
            (new UserSmsSign())::query()->latest()->where('user_id', $user_id)->where('status', 1)->value('sign')
            ?? "万能表单";
        $content = "【{$sign}】" . self::$template['content'];

        if (self::$smsConfig['gateway'] == 'submail') {
            $data['project'] = self::$template['template'];
            $data['signatures'] = $sign;
            foreach ((array)$data as $k => $v) {
                $content = str_replace("@var({$k})", $v, $content);
            }
            $data['content'] = self::$content = $content;
        } else {
            $content = "【{$sign}】" . self::$template['content'];
            foreach ((array)$data as $k => $v) {
                $content = str_replace('${' . $k . '}', $v, $content);
            }
            self::$content = $content;
        }

        return [
            'template' => self::$template['template'],
            'data' => $data
        ];
    }

    /**
     * @param       $mobile
     * @param       $templateCode
     * @param array $data
     * @param int   $user_id
     * @param int   $create_time
     * @return bool
     * @throws \Exception
     */
    public static function send($mobile, $templateCode, $data = [], $user_id = 0, $create_time = 0)
    {
        try {
            if (!self::getTemplate($templateCode)) {
                return false;
            }

            if (!self::getConfig()) {
                return false;
            }

            //获取sdk所需配置
            $config = self::getSmsConfig();
            $params = self::formatData($data, $user_id);

            if (!is_array($mobile)) {
                $mobile = [$mobile];
            }

            foreach ($mobile as $phone) {
                if (self::$smsConfig['gateway'] == 'submail' && empty(self::$template['template'])) {
                    $params['appid'] = $config['gateways'][self::$smsConfig['gateway']]['app_id'];
                    $params['to'] = $mobile;
                    $result = self::clientPost($params);
                } else {
                    $easySms = new EasySms($config);
                    $result = $easySms->send($phone, $params)[self::$smsConfig['gateway']];
                }

                //记录发送日志
                $SmsLog = (new SmsLog());
                $SmsLog->user_id = $user_id;
                $SmsLog->phone = $phone;
                $SmsLog->create_time = $create_time;
                $SmsLog->template_code = $templateCode;
                $SmsLog->template_id = self::$template['template'];
                $SmsLog->params = $params;
                $SmsLog->send_time = time();
                $SmsLog->status = $result['status'] === 'success' ? 1 : 2;
                $SmsLog->response_params = $result;
                $SmsLog->content = self::$content;
                $SmsLog->save();
            }

            return true;
        }
        catch (\Exception $exception) {
            //rlog($exception->getExceptions());
            //autoException($exception);
        }
    }

    /**
     * 发起请求
     * @param array  $form_params
     * @param string $type
     * @param string $body
     * @return mixed
     */
    private static function clientPost($form_params = [], $type = 'message/send', $body = '')
    {
        $options = [
            'headers' => [
//				'content-type' => 'application/json'
            ],
            'form_params' => $form_params,
            'body' => $body
        ];
        $config = [
            'base_uri' => 'https://api.mysubmail.com/',
            'timeout' => 2.0,
            'verify' => false,
        ];
        $client = new Client($config);
        $response = $client->post($type, $options);
        $responseBody = $response->getBody();
        $content = $responseBody->getContents();
        return json_decode($content, true);
    }
}
