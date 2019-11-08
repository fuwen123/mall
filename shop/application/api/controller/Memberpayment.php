<?php

namespace app\api\controller;
use think\Lang;
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
 * 支付控制器
 */
class Memberpayment extends MobileMember
{
    public function _initialize()
    {
        parent::_initialize();
        Lang::load(APP_PATH . 'home/lang/'.config('default_lang').'/buy.lang.php');
    }
    private function use_predeposit($order_info, $post,$virtual=0){
        if($virtual){
            if (floatval($order_info['rcb_amount']) > 0 || floatval($order_info['pd_amount']) > 0) {
                return $order_info;
            }
            $logic_buy = model('buyvirtual', 'logic');
        }else{
            if (floatval($order_info[0]['rcb_amount']) > 0 || floatval($order_info[0]['pd_amount']) > 0) {
                return $order_info;
            }
            $logic_buy = model('buy_1', 'logic');
        }
        if (empty($post['password'])) {
            return $order_info;
        }
        $member_model = model('member');
        $buyer_info = $member_model->getMemberInfoByID($this->member_info['member_id']);
        if ($buyer_info['member_paypwd'] == '' || $buyer_info['member_paypwd'] != md5($post['password'])) {
            return $order_info;
        }
        if ($buyer_info['available_rc_balance'] == 0) {
            $post['rcb_pay'] = null;
        }
        if ($buyer_info['available_predeposit'] == 0) {
            $post['pd_pay'] = null;
        }
        

        try {
            $member_model->startTrans();
            
 
            if (!empty($post['rcb_pay'])) {
                $order_info = $logic_buy->rcbPay($order_info, $post, $buyer_info);
            }

            if (!empty($post['pd_pay'])) {
                $order_info = $logic_buy->pdPay($order_info, $post, $buyer_info);
            }

            $member_model->commit();
        } catch (Exception $e) {
            $member_model->rollback();
            exit($e->getMessage());
        }

        return $order_info;
    }
    private function get_order_info($result){
        //计算本次需要在线支付的订单总金额
        $pay_amount = 0;
        $pay_order_id_list = array();
        if (!empty($result['data']['order_list'])) {
            foreach ($result['data']['order_list'] as $order_info) {
                if ($order_info['order_state'] == ORDER_STATE_NEW) {
                    $pay_amount += $order_info['order_amount'] - $order_info['pd_amount'] - $order_info['rcb_amount'];
                    $pay_order_id_list[] = $order_info['order_id'];
                }
            }
        }

        if ($pay_amount == 0) {
            $result['data']['pay_end']=1;
        }else {
            $result['data']['pay_end']=0;
        }
        $result['data']['api_pay_amount'] = ds_price_format($pay_amount);
        //临时注释
        if(!empty($pay_order_id_list)){
            $update = model('order')->editOrder(array('payment_time' => TIMESTAMP), array('order_id' => array('in', $pay_order_id_list)));
//            if (!$update) {
//                exit('更新订单信息发生错误，请重新支付');//因为微信支付时会重定向获取openid所以会更新两次
//            }
        }
        //如果是开始支付尾款，则把支付单表重置了未支付状态，因为支付接口通知时需要判断这个状态
        if (isset($result['data']['if_buyer_repay'])) {
            $update = model('order')->editOrderpay(array('api_paystate' => 0), array('pay_id' => $result['data']['pay_id']));
            if (!$update) {
                exit(lang('order_pay_fail'));
            }
            $result['data']['api_paystate'] = 0;
        }
        return $result;
    }
    private function get_vr_order_info($result){
        //计算本次需要在线支付的订单总金额
        $pay_amount = 0;
        if ($result['data']['order_state'] == ORDER_STATE_NEW) {
            $pay_amount += $result['data']['order_amount'] - $result['data']['pd_amount'] - $result['data']['rcb_amount'];
        }

        if ($pay_amount == 0) {
            $result['data']['pay_end']=1;
        }else{
            $result['data']['pay_end']=0;
        }

        $result['data']['api_pay_amount'] = ds_price_format($pay_amount);
        //临时注释
        //$update = model('order')->editOrder(array('api_pay_time'=>TIMESTAMP),array('order_id'=>$result['data']['order_id']));
        //if(!$update) {
        //    return array('error' => '更新订单信息发生错误，请重新支付');
        //}       
        //计算本次需要在线支付的订单总金额
        $pay_amount = $result['data']['order_amount'] - $result['data']['pd_amount'] - $result['data']['rcb_amount'];
        $result['data']['api_pay_amount'] = ds_price_format($pay_amount);

        return $result;
    }
    /**
     * @api {POST} api/Memberpayment/pay_new 实物订单支付
     * @apiVersion 1.0.0
     * @apiGroup MemberPayment
     *
     * @apiHeader {String} X-DS-KEY 用户授权token
     *
     * @apiParam {String} pay_sn 支付单号
     * @apiParam {Int} password 支付密码
     * @apiParam {Int} rcb_pay 充值卡支付金额
     * @apiParam {Int} pd_pay 预存款支付金额
     * @apiParam {String} payment_code 支付方式名称代码
     *
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     */
    public function pay_new() {
        //H5 相关接口的调用
        @header("Content-type: text/html; charset=UTF-8");
        $pay_sn = input('param.pay_sn');
        $payment_code = input('param.payment_code');
        if (!preg_match('/^\d{20}$/', $pay_sn)) {
            ds_json_encode(10001,lang('param_error'));
        }
        $sub_payment_code='';
        if(strpos($payment_code,'allinpay_h5')!==false){
            $sub_payment_code=str_replace('allinpay_h5_','',$payment_code);
            $payment_code='allinpay_h5';
        }
        $logic_payment = model('payment', 'logic');
        $result = $logic_payment->getPaymentInfo($payment_code);
        if (!$result['code']) {
            ds_json_encode(10001, $result['msg']);
        }
        $payment_info = $result['data'];
        $payment_info['payment_config']['sub_payment_code']=$sub_payment_code;
        //计算所需支付金额等支付单信息
        $result = $logic_payment->getRealOrderInfo($pay_sn, $this->member_info['member_id']);
        if (!$result['code']) {
            ds_json_encode(10001, $result['msg']);
        }
        if ($result['data']['api_paystate'] || empty($result['data']['api_pay_amount'])) {
            ds_json_encode(12001, '该订单不需要支付');
        }
        $result['data']['order_list']=$this->use_predeposit($result['data']['order_list'],input('param.'),0);
        $result=$this->get_order_info($result);
        if ($result['data']['pay_end'] == 1) {
            //站内支付了全款
            ds_json_encode(12001, '', 'success');
            exit;
        }
        //第三方API支付
        $this->_api_pay($result['data'], $payment_info);
    }

    /**
     * 虚拟订单支付
     */
    public function vr_pay_new() {
        //H5 相关接口的调用
        @header("Content-type: text/html; charset=UTF-8");
        
        $pay_sn = input('param.pay_sn');
        $payment_code = input('param.payment_code');

        if (!preg_match('/^\d{20}$/', $pay_sn)) {
            ds_json_encode(10001, lang('param_error'));
        }
        $sub_payment_code='';
        if(strpos($payment_code,'allinpay_h5')!==false){
            $sub_payment_code=str_replace('allinpay_h5_','',$payment_code);
            $payment_code='allinpay_h5';
        }
        $logic_payment = model('payment', 'logic');
        $result = $logic_payment->getPaymentInfo($payment_code);
        if (!$result['code']) {
            ds_json_encode(10001, $result['msg']);
        }
        $payment_info = $result['data'];
        $payment_info['payment_config']['sub_payment_code']=$sub_payment_code;
        //计算所需支付金额等支付单信息
        $result = $logic_payment->getVrOrderInfo($pay_sn, $this->member_info['member_id']);
        if (!$result['code']) {
            ds_json_encode(10001, $result['msg']);
        }

        if ($result['data']['order_state'] != ORDER_STATE_NEW || empty($result['data']['api_pay_amount'])) {
            ds_json_encode(12001, lang('no_payment_required_this_order'));
        }
        $result['data']=$this->use_predeposit($result['data'],input('param.'),1);
        $result=$this->get_vr_order_info($result);
        if ($result['data']['pay_end'] == 1) {
                ds_json_encode(12001, '', 'success');
            }
        //转到第三方API支付
        $this->_api_pay($result['data'], $payment_info);
    }
    
    /**
     * @api {POST} api/Memberpayment/pd_pay 账户充值
     * @apiVersion 1.0.0
     * @apiGroup MemberPayment
     *
     * @apiHeader {String} X-DS-KEY 用户授权token
     *
     * @apiParam {String} pay_sn 支付单号
     * @apiParam {String} payment_code 支付方式名称代码
     *
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     */ 
    public function pd_pay() {
        $pay_sn = input('param.pay_sn');
        $payment_code = input('param.payment_code');
        $sub_payment_code='';
        if(strpos($payment_code,'allinpay_h5')!==false){
            $sub_payment_code=str_replace('allinpay_h5_','',$payment_code);
            $payment_code='allinpay_h5';
        }
        $condition = array();
        $condition['payment_code'] = $payment_code;
        
        $logic_payment = model('payment', 'logic');
        $result = $logic_payment->getPaymentInfo($payment_code);
        if (!$result['code']) {
            ds_json_encode(10001, $result['msg']);
        }
        $payment_info = $result['data'];
        
        $result = $logic_payment->getPdOrderInfo($pay_sn, $this->member_info['member_id']);
        if (!$result['code']) {
            ds_json_encode(10001, $result['msg']);
            exit();
        }
        if ($result['data']['pdr_payment_state'] || empty($result['data']['api_pay_amount'])) {
            ds_json_encode(12001, lang('no_payment_required'));
            exit();
        }
        $payment_info['payment_config']['sub_payment_code']=$sub_payment_code;
        $this->_api_pay($result['data'], $payment_info);
    }

    /**
     * 第三方在线支付接口
     *
     */
    private function _api_pay($order_info, $payment_info)
    {
        try{
        $payment_api = new $payment_info['payment_code']($payment_info);
        $payment_api->get_payform($order_info);
        }catch(\Exception $e){
            ds_json_encode(10001, $e->getMessage());
        }
    }




    /**
     * @api {POST} api/Memberpayment/payment_list 可用支付参数列表
     * @apiVersion 1.0.0
     * @apiGroup MemberPayment
     *
     * @apiHeader {String} X-DS-KEY 用户授权token
     *
     * @apiParam {String} payment_platform 支付平台
     * @apiParam {String} payment_code 支付方式代码
     *
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     * @apiSuccess {Object[]} result.payment_list  支付方式数组
     * @apiSuccess {String} result.payment_list.payment_code  支付方式代码
     * @apiSuccess {String} result.payment_list.payment_name  支付方式名称
     */ 
    public function payment_list()
    {
        $payment_model = model('payment');
        $condition=array();
        $payment_code=input('param.payment_code');
        if($payment_code){
            $condition['payment_code'] = $payment_code;
        }
        $payment_platform=input('param.payment_platform');
        if($payment_platform){
            $condition['payment_platform'] = $payment_platform;
        }else{
            $condition['payment_platform'] = 'h5';
        }
        
        $payment_list = $payment_model->getPaymentOpenList($condition);

        $payment_array = array();
        if (!empty($payment_list)) {
            foreach ($payment_list as $value) {
                $payment_array[] = array(
                    'payment_code'=>$value['payment_code'],
                    'payment_name'=>$value['payment_name'],
                );
            }
        }
        ds_json_encode(10000, '',array('payment_list' => $payment_array));
    }
}

?>
