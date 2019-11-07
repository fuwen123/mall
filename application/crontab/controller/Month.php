<?php

namespace app\crontab\controller;

/**
 * ============================================================================
 * DSMall多用户商城
 * ============================================================================
 * 版权所有 2014-2028 长沙德尚网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.csdeshang.com
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用 .
 * 不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * 定时器
 */
class Month extends BaseCron {

    /**
     * 默认方法
     */
    public function index(){
        //生成平台月账单统计
        $this->_create_orderstatis();
    }

    private function _create_orderstatis(){
        $max_time=strtotime(date('Y-m-01 0:0:0', TIMESTAMP))-1;//上个月最后一天
        
        $os_month=db('orderstatis')->order('os_month desc')->value('os_month');
        if($os_month){
            //有生成过平台账单则查看最新的账单时间到上个月期间是否有结算单
            $start_time= strtotime($os_month.'01 0:0:0 + 1 month');

        }else{
            //没生成过平台账单则生成最旧的结算单时间到上个月期间的平台账单
            $ob_createdate=db('orderbill')->order('ob_startdate asc')->value('ob_startdate');
            if($ob_createdate){
                $start_time= strtotime(date('Y-m-01 0:0:0',$ob_createdate));
            }
        }
        
        
        if(isset($start_time)){
            for($i=1;$i<=100;$i++){
                $end_time=strtotime(date('Y-m-01 0:0:0', $start_time)." +1 month")-1;
                if($end_time>$max_time){
                    break;
                }
                $orderbill_sum=db('orderbill')->where(array('ob_enddate'=>array('between',[$start_time,$end_time])))->field('SUM(ob_order_totals) AS os_order_totals,SUM(ob_shipping_totals) AS os_shipping_totals,SUM(ob_order_return_totals) AS os_order_returntotals,SUM(ob_commis_totals) AS os_commis_totals,SUM(ob_commis_return_totals) AS os_commis_returntotals,SUM(ob_store_cost_totals) AS os_store_costtotals,SUM(ob_vr_order_totals) AS os_vr_order_totals,SUM(ob_vr_commis_totals) AS os_vr_commis_totals,SUM(ob_vr_inviter_totals) AS os_vr_inviter_totals,SUM(ob_result_totals) AS os_result_totals,SUM(ob_inviter_totals) AS os_inviter_totals,SUM(ob_vr_order_return_totals) AS os_vr_order_return_totals,SUM(ob_vr_commis_return_totals) AS os_vr_commis_return_totals')->find();
                if($orderbill_sum){
                    db('orderstatis')->insert(array(
                        'os_month'=>date('Ym',$start_time),
                        'os_createdate'=>TIMESTAMP,
                        'os_order_totals'=>floatval($orderbill_sum['os_order_totals']),
                        'os_shipping_totals'=>floatval($orderbill_sum['os_shipping_totals']),
                        'os_order_returntotals'=>floatval($orderbill_sum['os_order_returntotals']),
                        'os_commis_totals'=>floatval($orderbill_sum['os_commis_totals']),
                        'os_commis_returntotals'=>floatval($orderbill_sum['os_commis_returntotals']),
                        'os_store_costtotals'=>floatval($orderbill_sum['os_store_costtotals']),
                        'os_vr_order_totals'=>floatval($orderbill_sum['os_vr_order_totals']),
                        'os_vr_commis_totals'=>floatval($orderbill_sum['os_vr_commis_totals']),
                        'os_vr_inviter_totals'=>floatval($orderbill_sum['os_vr_inviter_totals']),
                        'os_result_totals'=>floatval($orderbill_sum['os_result_totals']),
                        'os_inviter_totals'=>floatval($orderbill_sum['os_inviter_totals']),
                        'os_vr_order_return_totals'=>floatval($orderbill_sum['os_vr_order_return_totals']),
                        'os_vr_commis_return_totals'=>floatval($orderbill_sum['os_vr_commis_return_totals']),
                    ));
                }else{
                    db('orderstatis')->insert(array('os_month'=>date('Ym',$start_time),'os_createdate'=>TIMESTAMP));
                }
                $start_time=$end_time+1;
            }
            if($end_time<$max_time){
                $this->redirect('month/index');
            }
        }
    }
    
}
?>
