<?php
/**
 * Created by PhpStorm.
 * User: zb
 * Date: 2021/4/29
 * Time: 16:23
 */

namespace baoxu1993\Tools;


class Time
{
    /**
     * 获取日期为星期几
     * @param $day
     * @return string
     */
    public static function getOneDayWeek($day)
    {
        $weekarray = array("日", "一", "二", "三", "四", "五", "六");
        return "星期" . $weekarray[date("w", strtotime("{$day}"))];
    }


    /**
     * int型天格式化
     * @param $day
     * @return string
     */
    public static function intDayFormat($day)
    {
        return substr($day, 0, 4) . "-" . substr($day, 4, 2) . "-" . substr($day, 6);
    }

    /**
     * 获取指定天的int型日期
     * @param string $time 时间戳
     * @return int
     */
    public static function getOneDayIntDate($time = '')
    {
        return (int)(empty($time) ? date('Ymd') : date('Ymd', $time));
    }

    /**
     * 获取指定天的起始结束时间
     * @param string $time 时间戳
     * @return array
     */
    public static function getOneDayStartAndEndTime($time = '')
    {
        $day = (int)(empty($time) ? date('Ymd') : date('Ymd', $time));
        return [
            'startTime' => strtotime("{$day} 00:00:00"),
            'endTime' => strtotime("{$day} 23:59:59"),
        ];
    }

    /**
     * 计算特定时间到当前时间的间隔年月
     * @param string $startDate
     * @param string $endDate
     * @param array  $flag
     * @return string
     */
    public static function calculateYearInterval($startDate = '', $endDate = '', $flag = [])
    {
        $endDate = empty($endDate) ? time() : $endDate;
        $startDate = empty($startDate) ? time() : $startDate;
        if ($endDate <= $startDate) {
            return '';
        }

        $start = (int)date("Ymd", $startDate);
        $end = (int)date("Ymd", $endDate);

        $startYear = (int)substr($start, 0, 4);
        $startMonth = (int)substr($start, 4, 2);
        $startDay = (int)substr($start, 6, 2);
        $endYear = (int)substr($end, 0, 4);
        $endMonth = (int)substr($end, 4, 2);
        $endDay = (int)substr($end, 6, 2);

        $year = 0;
        $month = 0;
        $day = 0;

        //计算满多少周年
        $year = $endYear - $startYear - 1;
        //开始月份日期大于结束的月份日期，说明有一年未满一周年
        if (intval("{$startMonth}{$startDay}") != intval("{$endMonth}{$endDay}")) {
            //计算除周年外的月
            //首年的月
            $month1 = 12 - $startMonth;
            //结束年的月
            $month2 = $endMonth - 1;

            $month = $month1 + $month2;

            //计算剩余的天数
            $lastday = date('d', strtotime("{$startYear}-{$startMonth}-01 +1 month -1 day"));
            $day = $lastday - $startDay + $endDay;

            //反推天、月
            if ($day >= 30) {
                $month += 1;
                $day -= 30;
            }

            if ($month >= 12) {
                $year += 1;
                $month -= 12;
            }
        } else {
            $year += 1;
        }

        $elapse = '';

        $unitArr = array(
            '年' => 'year',
            '月' => 'month',
            '天' => 'day',
            '小时' => 'hour',
            '分' => 'minute',
            '秒' => 'second'
        );

        foreach ($unitArr as $cn => $u) {
            if (in_array($u, $flag)) {
                if (empty($$u) && empty($elapse)) {

                } else {
                    $elapse .= $$u . $cn;
                }
            }
        }

        return $elapse;
    }

    /**
     * 计算特定时间到当前时间的间隔天
     * @param string $startDate
     * @param string $endDate
     * @return string
     */
    public static function calculateDayInterval($startDate, $endDate = '')
    {
        $endDate = empty($endDate) ? time() : $endDate;
        if ($endDate <= $startDate) {
            return 0;
        }
        return ceil(($endDate - $startDate) / 86400);
    }

    /**
     * 获取微妙
     * @return float
     */
    public static function getMicroTime()
    {
        list($msec, $sec) = explode(' ', microtime());
        return (float)sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);
    }


    /**
     * 获取时间范围的开始/结束时间
     * @param int  $type
     * @param bool $unix
     * @return array
     */
    public static function getTimeRange(int $type, $unix = true)
    {
        switch ($type) {
            case 1://当天
                $day = date("Y-m-d");
                $time = [
                    'start' => strtotime("{$day} 00:00:00"),
                    'end' => strtotime("{$day} 23:59:59")
                ];
                break;
            case 2://当周
                $day = date("Y-m-d");
                $w = date('w', strtotime($day));
                $week_start = date('Y-m-d', strtotime("$day -" . ($w ? $w - 1 : 6) . ' days'));
                $week_end = date('Y-m-d', strtotime("$week_start +6 days"));
                $time = [
                    'start' => strtotime("{$week_start} 00:00:00"),
                    'end' => strtotime("{$week_end} 23:59:59")
                ];
                break;
            case 3://当月
                $start = strtotime(date("Y-m-01"));
                $time = [
                    'start' => $start,
                    'end' => strtotime("+1 month -1 seconds", $start)
                ];
                break;
            case 4://当年
                $start = strtotime(date("Y-01-01"));
                $time = [
                    'start' => $start,
                    'end' => strtotime("+1 year -1 seconds", $start)
                ];
                break;
            default:
                $time = [];
                break;
        }

        if ($time && $unix === false) {
            $time = [
                'start' => date("Y-m-d H:i:s", $time['start']),
                'end' => date("Y-m-d H:i:s", $time['end'])
            ];
        }
        return $time;
    }
}