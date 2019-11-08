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
 * 充值控制器
 */
class Recharge extends MobileMember {

    public function _initialize() {
        parent::_initialize();
        Lang::load(APP_PATH . 'home/lang/'.config('default_lang').'/predeposit.lang.php');
    }


    /**
     * @api {POST} api/Recharge/index 新增充值信息
     * @apiVersion 1.0.0
     * @apiGroup Recharge
     *
     * @apiHeader {String} X-DS-KEY 用户授权token
     *
     * @apiParam {Float} pdr_amount 充值金额
     *
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     * @apiSuccess {String} result.pay_sn  支付单号
     */
    public function index() {
        $pdr_amount = abs(floatval(input('post.pdr_amount')));
        if ($pdr_amount <= 0) {
            ds_json_encode(10001,lang('param_error'));
        } else {
            $predeposit_model = model('predeposit');
            $data = array();
            $data['pdr_sn'] = $pay_sn = makePaySn($this->member_info['member_id']);
            $data['pdr_member_id'] = $this->member_info['member_id'];
            $data['pdr_member_name'] = $this->member_info['member_name'];
            $data['pdr_amount'] = $pdr_amount;
            $data['pdr_addtime'] = TIMESTAMP;
            $insert = $predeposit_model->addPdRecharge($data);
            if ($insert) {
                ds_json_encode(10000, '',array('pay_sn' => $pay_sn));
            } else {
                ds_json_encode(10001,lang('ds_common_op_fail'));
            }
        }
    }


    /**
     * @api {POST} api/Recharge/pd_cash_add 申请提现
     * @apiVersion 1.0.0
     * @apiGroup Recharge
     *
     * @apiHeader {String} X-DS-KEY 用户授权token
     *
     * @apiParam {String} pdc_bank_name 银行名称
     * @apiParam {String} pdc_bank_no 银行卡号
     * @apiParam {String} pdc_bank_user 银行用户名
     * @apiParam {String} password 支付密码
     * @apiParam {Float} pdc_amount 提现金额
     *
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     */
    public function pd_cash_add() {
        $pdc_amount = abs(floatval(input('post.pdc_amount')));
        
        $memberbank_id = intval(input('param.memberbank_id'));
        if ($memberbank_id <= 0) {
            ds_json_encode(10001, lang('param_error'));
        }
        $memberbank = model('memberbank')->getMemberbankInfo(array('member_id' => session('member_id'), 'memberbank_id' => $memberbank_id));
        if (empty($memberbank)) {
            ds_json_encode(10001, lang('param_error'));
        }
        
        $pdc_bank_name = $memberbank['memberbank_name'];
        $pdc_bank_no = $memberbank['memberbank_no'];
        $pdc_bank_user = $memberbank['memberbank_truename'];

        $data = array(
            'pdc_amount'=>$pdc_amount,
            'pdc_bank_name'=>$pdc_bank_name,
            'pdc_bank_no'=>$pdc_bank_no,
            'pdc_bank_user'=>$pdc_bank_user,
            'password'=>input('post.password'),
        );

        $recharge_validate = validate('predeposit');
        if (!$recharge_validate->scene('pd_cash_add')->check($data)){
            ds_json_encode(10001,$recharge_validate->getError());
        }

        $predeposit_model = model('predeposit');
        $member_model = model('member');
        $memberinfo = $member_model->getMemberInfoByID($this->member_info['member_id']);
        //验证支付密码
        if (md5(input('post.password')) != $memberinfo['member_paypwd']) {
            ds_json_encode(10001,lang('payment_password_error'));
        }
        //验证金额是否足够
        if (floatval($memberinfo['available_predeposit']) < $pdc_amount) {
            ds_json_encode(10001,lang('predeposit_cash_shortprice_error'));
        }
        try {
            $predeposit_model->startTrans();
            $pdc_sn = makePaySn($memberinfo['member_id']);
            $data = array();
            $data['pdc_sn'] = $pdc_sn;
            $data['pdc_member_id'] = $memberinfo['member_id'];
            $data['pdc_member_name'] = $memberinfo['member_name'];
            $data['pdc_amount'] = $pdc_amount;
            $data['pdc_bank_name'] = $pdc_bank_name;
            $data['pdc_bank_no'] = $pdc_bank_no;
            $data['pdc_bank_user'] = $pdc_bank_user;
            $data['pdc_addtime'] = TIMESTAMP;
            $data['pdc_payment_state'] = 0;
            
            $insert = $predeposit_model->addPdcash($data);
            if (!$insert) {
                exception(lang('predeposit_cash_add_fail'));
            }
            //冻结可用预存款
            $data = array();
            $data['member_id'] = $memberinfo['member_id'];
            $data['member_name'] = $memberinfo['member_name'];
            $data['amount'] = $pdc_amount;
            $data['order_sn'] = $pdc_sn;
            $predeposit_model->changePd('cash_apply', $data);
            $predeposit_model->commit();
            
        } catch (\Exception $e) {
            $predeposit_model->rollback();
            ds_json_encode(10001,$e->getMessage());
        }
        ds_json_encode(10000, lang('ds_common_op_succ'),array('status' => 'ok'));
    }

    /**
     * @api {POST} api/Recharge/recharge_order 获取充值信息
     * @apiVersion 1.0.0
     * @apiGroup Recharge
     *
     * @apiHeader {String} X-DS-KEY 用户授权token
     *
     * @apiParam {String} paysn 充值单号
     *
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     * @apiSuccess {Object} result.payment_list  返回数据
     * @apiSuccess {String} result.payment_list.payment_code  支付方式代码
     * @apiSuccess {String} result.payment_list.payment_name  支付方式名称
     * @apiSuccess {Object} result.pdinfo  充值信息 （返回字段参考pdrecharge表）
     * @apiSuccess {Object} result.base_site_url  域名
     */
    public function recharge_order() {
        $pay_sn = input('param.paysn');
        if (!preg_match('/^\d{20}$/', $pay_sn)) {
            ds_json_encode(10001,lang('param_error'));
            exit();
        }

        //查询支付单信息
        $predeposit_model = model('predeposit');
        $pd_info = $predeposit_model->getPdRechargeInfo(array('pdr_sn' => $pay_sn, 'pdr_member_id' => $this->member_info['member_id']));
        if (empty($pd_info)) {
            ds_json_encode(10001,lang('recharge_info_not_exist'));
            exit();
        }
        if (intval($pd_info['pdr_payment_state'])) {
            ds_json_encode(10001,lang('payment_repeat'));
            exit();
        }


        $payment_model = model('payment');

        $condition = array();
        $condition['payment_platform'] = 'h5';
        $payment_list = $payment_model->getPaymentOpenList($condition);
        $payment_array = array();
        if (!empty($payment_list)) {
            foreach ($payment_list as $value) {
                $payment_array[] = array('payment_code' => $value['payment_code'], 'payment_name' => $value['payment_name']);
            }
        } else {
            ds_json_encode(10001,lang('predeposit_payment_error'));
            exit();
        }
        unset($pd_info['pdr_payment_code']);
        unset($pd_info['pdr_trade_sn']);
        unset($pd_info['pdr_payment_state']);
        unset($pd_info['pdr_paymenttime']);
        unset($pd_info['pdr_admin']);
        
        ds_json_encode(10000, '',array('payment_list' => $payment_array, 'pdinfo' => $pd_info,'base_site_url'=>BASE_SITE_URL));
    }

    public function member_v() {
        $member_info = array();
        $member_info['user_name'] = $this->member_info['member_name'];
        $member_info['avator'] = get_member_avatar_for_id($this->member_info['member_id']);
        $member_info['point'] = $this->member_info['member_points'];
        $member_gradeinfo = model('member')->getOneMemberGrade(intval($this->member_info['member_exppoints']));
        $member_info['level_name'] = $member_gradeinfo['level_name'];
        $member_info['favorites_store'] = model('favorites')->getStoreFavoritesCountByMemberId($this->member_info['member_id']);
        $member_info['favorites_goods'] = model('favorites')->getGoodsFavoritesCountByMemberId($this->member_info['member_id']);
        $member_info['member_id'] = $this->member_info['member_id']; //

        $member_info['member_id_64'] = base64_encode(intval($this->member_info['member_id']) * 1); //
        $list_setting = rkcache('config', true);
        $member_info['vip_1fee'] = $list_setting['vip_1fee'];
        $member_info['vip_2fee'] = $list_setting['vip_2fee'];
        ds_json_encode(10000, '',array('member_info' => $member_info));
    }

    /**
     * 在线升级到会员级别
     */
    public function recharge_vip1() {
        $pdr_amount = abs(floatval(input('post.pdr_amount')));
        $list_setting = rkcache('config', true);
        if ($pdr_amount <= 0 || $pdr_amount != abs(floatval($list_setting['vip_1fee']))) {

            ds_json_encode(10001,lang('param_error'));
            exit();
        }

        $predeposit_model = model('predeposit');

        $data = array();

        $data['pdr_sn'] = $pay_sn = makePaySn($this->member_info['member_id']);

        $data['pdr_member_id'] = $this->member_info['member_id'];

        $data['pdr_member_name'] = $this->member_info['member_name'];

        $data['pdr_amount'] = $pdr_amount;

        $data['pdr_addtime'] = TIMESTAMP;

        $data['pdr_vipid'] = '1';

        $insert = $predeposit_model->addVipRecharge($data);

        if ($insert) {
            ds_json_encode(10000, '',array('pay_sn' => $pay_sn));
        } else {
            ds_json_encode(10001,lang('ds_common_op_fail'));
        }
    }

    public function recharge_vip2() {
        $pdr_amount = abs(floatval(input('post.pdr_amount')));
        $list_setting = rkcache('config', true);
        if ($pdr_amount <= 0 || $pdr_amount != abs(floatval($list_setting['vip_2fee']))) {
            ds_json_encode(10001,lang('param_error'));
            exit();
        }

        $predeposit_model = model('predeposit');
        $data = array();
        $data['pdr_sn'] = $pay_sn = makePaySn($this->member_info['member_id']);
        $data['pdr_member_id'] = $this->member_info['member_id'];
        $data['pdr_member_name'] = $this->member_info['member_name'];
        $data['pdr_amount'] = $pdr_amount;
        $data['pdr_addtime'] = TIMESTAMP;
        $data['pdr_vipid'] = '2';
        $insert = $predeposit_model->addVipRecharge($data);

        if ($insert) {
            ds_json_encode(10000, '',array('pay_sn' => $pay_sn));
        } else {
            ds_json_encode(10001,lang('ds_common_op_fail'));
        }
    }

    public function viprecharge_order() {
        $pay_sn = input('post.paysn');
        if (!preg_match('/^\d{20}$/', $pay_sn)) {
            ds_json_encode(10001,lang('param_error'));
            exit();
        }

        //查询支付单信息
        $predeposit_model = model('predeposit');
        $pd_info = $predeposit_model->getVipRechargeInfo(array('pdr_sn' => $pay_sn, 'pdr_member_id' => $this->member_info['member_id']));
        if (empty($pd_info)) {
            ds_json_encode(10001,lang('recharge_info_not_exist'));
            exit();
        }
        if (intval($pd_info['pdr_payment_state'])) {
            ds_json_encode(10001,lang('payment_repeat'));
            exit();
        }


        $payment_model = model('payment');
        $condition = array();
        $condition['payment_platform'] = 'h5';
        $payment_list = $payment_model->getPaymentOpenList($condition);
        $payment_array = array();
        if (!empty($payment_list)) {
            foreach ($payment_list as $value) {
                $payment_array[] = array('payment_code' => $value['payment_code'], 'payment_name' => $value['payment_name']);
            }
        } else {
            ds_json_encode(10001,lang('predeposit_payment_error'));
            exit();
        }
        unset($pd_info['pdr_payment_code']);
        unset($pd_info['pdr_trade_sn']);
        unset($pd_info['pdr_payment_state']);
        unset($pd_info['pdr_paymenttime']);
        unset($pd_info['pdr_admin']);
        ds_json_encode(10000, '',array('payment_list' => $payment_array, 'pdinfo' => $pd_info));
    }

}

?>
