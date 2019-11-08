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
 * 营销活动控制器
 */
class Marketmanage extends MobileMall {

    public function _initialize() {
        parent::_initialize();
        Lang::load(APP_PATH . 'home/lang/' . config('default_lang') . '/marketmanage.lang.php');
    }

    /**
     * @api {POST} api/Marketmanage/get_info 营销活动详情
     * @apiVersion 1.0.0
     * @apiGroup Marketmanage
     *
     * @apiParam {Int} marketmanage_id 活动ID
     * 
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     */
    public function get_info() {
        $marketmanage_id = intval(input('param.marketmanage_id'));
        if ($marketmanage_id < 0) {
            ds_json_encode(10001, lang('param_error'));
        }
        $marketmanage_model = model('marketmanage');
        $condition['marketmanage_id'] = $marketmanage_id;
        $marketmanage = $marketmanage_model->getOneMarketmanage($condition);
        if (!$marketmanage) {
            ds_json_encode(10001, lang('market_activity_not_exist'));
        }
        if ($marketmanage['marketmanage_begintime'] > TIMESTAMP) {
            ds_json_encode(10001, lang('market_activity_not_begin'));
        }
        if ($marketmanage['marketmanage_endtime'] < TIMESTAMP) {
            ds_json_encode(10001, lang('market_activity_not_end'));
        }
        $marketmanage['marketmanage_begintime_text'] = date('Y.m.d', $marketmanage['marketmanage_begintime']);
        $marketmanage['marketmanage_endtime_text'] = date('Y.m.d', $marketmanage['marketmanage_endtime']);
        //获取奖项设置
        $marketmanageaward_list = $marketmanage_model->getMarketmanageAwardList(array('marketmanage_id' => $marketmanage_id, 'marketmanageaward_count' => array('>', 0)));
        if (!$marketmanageaward_list) {
            ds_json_encode(10001, lang('market_award_set_error'));
        }
        foreach ($marketmanageaward_list as $k => $v) {
            switch ($v['marketmanageaward_type']) {
                case 1:
                    $marketmanageaward_list[$k]['marketmanageaward_text'] = $v['marketmanageaward_point'] . lang('point');
                    break;
                case 2:
                    $bonus_model = model('bonus');
                    $condition = array('bonus_id' => $v['bonus_id']);
                    $bonus = $bonus_model->getOneBonus($condition); //获取当前红包的领取金额
                    if (!$bonus) {
                        ds_json_encode(10001, lang('bonus_set_error'));
                    }
                    if ($bonus['bonus_type'] != 3) {
                        ds_json_encode(10001, lang('bonus_set_error'));
                    }
                    if ($bonus['bonus_pricetype']) {
                        $marketmanageaward_list[$k]['marketmanageaward_text'] = floatval($bonus['bonus_fixedprice']) . lang('bonus_fixedprice_notice');
                    } else {
                        $marketmanageaward_list[$k]['marketmanageaward_text'] = floatval($bonus['bonus_randomprice_start']) . '~' . floatval($bonus['bonus_randomprice_end']) . lang('bonus_randomprice_notice');
                    }
                    break;
                case 3:
                    $voucher_model = model('voucher');
                    //验证是否可以兑换代金券
                    $where = array();
                    $where['vouchertemplate_id'] = $v['vouchertemplate_id'];
                    $where['vouchertemplate_state'] = 1;
                    $where['vouchertemplate_enddate'] = array('gt', time());
                    $template_info = $voucher_model->getVouchertemplateInfo($where);
                    if (empty($template_info)) {
                        ds_json_encode(10001, lang('vouchertemplate_set_error'));
                    }
                    $marketmanageaward_list[$k]['marketmanageaward_text'] = $template_info['vouchertemplate_price'] . lang('vouchertemplate_notice');
                    break;
                default:
                    ds_json_encode(10001, lang('bargain_not_exist'));
            }
        }
        $can_draw=true;
        $message='';
        $count_left=0;
        $memeber_id=$this->getMemberIdIfExists();
        if ($marketmanage['marketmanage_jointype'] != 2 && $memeber_id) {//有参与次数限制
            //判断当前用户是否参与过
            $condition = array();
            $condition['marketmanage_id'] = $marketmanage_id;
            $condition['member_id'] = $memeber_id;
            $marketmanage_joincount = $marketmanage['marketmanage_joincount'];
            switch ($marketmanage['marketmanage_jointype']) {
                case 0:
                    break;
                case 1:
                    $condition['marketmanagelog_time'] = array('between', array(strtotime(date('Y-m-d 0:0:0')), TIMESTAMP));
                    break;
                default :
                    ds_json_encode(10001, lang('marketmanage_jointype_set_error'));
            }
            $marketmanagelog = $marketmanage_model->getMarketmanageLogList($condition);
            if (count($marketmanagelog) >= $marketmanage_joincount) {
                $can_draw=false;
                $message=sprintf(lang('marketmanage_joincount_error'),count($marketmanagelog));
            }else{
                $count_left=$marketmanage_joincount-count($marketmanagelog);
            }
        }
        $result = array('marketmanage_info' => $marketmanage, 'marketmanageaward_list' => $marketmanageaward_list,'can_draw'=>$can_draw,'count_left'=>$count_left);
        ds_json_encode(10000, $message, $result);
    }

}
