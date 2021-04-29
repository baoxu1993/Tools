<?php
/**
 * Created by PhpStorm.
 * User: zb
 * Date: 2021/4/29
 * Time: 16:53
 */

namespace baoxu1993\Tools;


class HttpRequest
{
    /**
     * POST请求
     * @param string $url
     * @param array  $params
     * @param array  $header
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function post(string $url, array $params, array $header = [])
    {
        $client = new \GuzzleHttp\Client();
        $headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json; charset=UTF-8'
        ];
        if (!empty($header)) {
            $headers = array_merge($headers, $header);
        }

        $json = [];
        if (!empty($params)) {
            $json = $params;
        }
        $resp = $client->request('POST', $url, [
            'headers' => $headers,
            'json' => $json
        ]);
        return [
            'httpStatusCode' => $resp->getStatusCode(),
            'responseData' => $resp->getBody()->getContents()
        ];
    }
}