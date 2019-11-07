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
 * 领取红包控制器
 */
class Memberbonus extends MobileMember {

    public function _initialize() {
        parent::_initialize();
        Lang::load(APP_PATH . 'home/lang/'.config('default_lang').'/memberbonus.lang.php');
    }
    /**
     * @api {POST} api/Memberbonus/get_receive_list 红包记录
     * @apiVersion 1.0.0
     * @apiGroup Memberbonus
     *
     * @apiHeader {String} X-DS-KEY 用户授权token
     *
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     */
    public function get_receive_list() {
        $bonus_model=model('bonus');
        $bonuslog_list=$bonus_model->getBonusreceiveList(array('member_id'=>$this->member_info['member_id']),$this->pagesize);
        $result = array_merge(array('log_list' => $bonuslog_list), mobile_page($bonus_model->page_info));
        ds_json_encode(10000, '',$result);
    }
    /**
     * @api {POST} api/Memberbonus/receive 活动红包领取
     * @apiVersion 1.0.0
     * @apiGroup Memberbonus
     *
     * @apiHeader {String} X-DS-KEY 用户授权token
     *
     * @apiParam {Int} bonus_id 活动ID
     * 
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     */
    public function receive() {
        $bonus_id = intval(input('param.bonus_id'));
        if ($bonus_id < 0) {
            ds_json_encode(10001, lang('param_error'));
        }
        $bonus_model = model('bonus');
        $condition = array();
        $condition['bonus_id'] = $bonus_id;
        $bonus = $bonus_model->getOneBonus($condition); //获取当前红包的领取金额
        if ($bonus['bonus_begintime'] > TIMESTAMP) {
            ds_json_encode(10001, lang('bonus_not_begin'));
        }
        if ($bonus['bonus_type'] != 1) {
            ds_json_encode(10001, lang('bonus_type_error'));
        }
        if ($bonus['bonus_state'] == 2 || TIMESTAMP > $bonus['bonus_endtime']) {
            ds_json_encode(10001, lang('bonus_expire'));
        }
        if ($bonus['bonus_state'] == 3) {
            ds_json_encode(10001, lang('bonus_invalid'));
        }

        //判断当前用户是否领取过
        $condition = array();
        $condition['bonus_id'] = $bonus_id;
        $condition['member_id'] = $this->member_info['member_id'];
        $bonusreceive = $bonus_model->getOneBonusreceive($condition);
        if ($bonusreceive) {
            ds_json_encode(10001, sprintf(lang('bonusreceive_info'),date('Y-m-d H:i:s', $bonusreceive['bonusreceive_time']),$bonusreceive['bonusreceive_price']));
        }
        //获取未领取单个红包
        $condition = array();
        $condition['bonus_id'] = $bonus_id;
        $condition['member_id'] = 0;
        $bonusreceive = $bonus_model->getOneBonusreceive($condition);
        if (empty($bonusreceive)) {
            ds_json_encode(10001, lang('bonus_send_over'));
        }

        try {
            $bonus_model->startTrans();

            $res=$bonus_model->receiveBonus($this->member_info,$bonus,$bonusreceive,lang('receive_bonus'));
            if(!$res['code']){
                exception($res['msg']);
            }
            $bonus_model->commit();
            ds_json_encode(10000, sprintf(lang('get_bonus_info'),$bonusreceive['bonusreceive_price']));
        } catch (Exception $e) {
            $bonus_model->rollback();
            ds_json_encode(10001, $e->getMessage());
        }
    }

}
