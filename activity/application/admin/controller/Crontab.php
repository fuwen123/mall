<?php
/**
 * tpshop
 * ============================================================================
 * 版权所有 2015-2027 深圳搜豹网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.tp-shop.cn
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用 .
 * 不允许对程序代码以任何形式任何目的的再发布。
 * 采用最新Thinkphp5助手函数特性实现单字母函数M D U等简写方式
 * ============================================================================
 * Author: 当燃
 * Date: 2015-09-09
 */
namespace app\admin\controller;

use think\Db;
use think\Log;

class Crontab extends Base
{
    /**
     * 发货超时单
     * 订单产生超过24小时提醒一次，48小时提醒一次
     * 注：需要配置定时任务
     */
    public function DeliveryTimeout()
    {
        $onetime1 = strtotime(date("Y-m-d H:i:00", strtotime('-1 day')));
        $onetime2 = strtotime(date("Y-m-d H:i:59", strtotime('-1 day')));
        $twotime1 = strtotime(date("Y-m-d H:i:00", strtotime('-2 day')));
        $twotime2 = strtotime(date("Y-m-d H:i:59", strtotime('-2 day')));

        $num = db('order')
            ->where("pay_time >= $onetime1 and pay_time <= $onetime2")
            ->whereOr("pay_time >= $twotime1 and pay_time <= $twotime2")
            ->where(" (pay_status=1 OR pay_code='cod') AND shipping_status !=1 AND order_status in(0,1)")
            ->count();

        $scene = 8;
        $params['num'] = $num;
        $mobiles = tpCache("sms")['delivery_timeout_sms_enable'];
        $mobileArr = explode("|", $mobiles);
        if($num > 0) {
            if (count($mobileArr) > 0) {
                foreach($mobileArr as $v) {
                    $resp = sendSms($scene, $v, $params, '');
                    Log::info($resp);
                }
            }
        }
    }
}
