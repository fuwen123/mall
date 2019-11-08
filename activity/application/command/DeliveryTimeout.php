<?php
/**
 * 发货超时单
 * 订单产生超过24小时提醒一次，48小时提醒一次
 * 注：需要配置定时任务
 */
namespace app\command;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\Log;

class DeliveryTimeout extends Command
{
    protected function configure()
    {
        $this->setName('delivery_timeout')->setDescription('发货超时单--短信通知');
    }

    protected function execute(Input $input, Output $output)
    {
        $output->writeln("delivery_timeout:");
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