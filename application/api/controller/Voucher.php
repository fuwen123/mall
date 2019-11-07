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
 * 代金券控制器
 */
class Voucher extends MobileMall {

    public function _initialize() {
        parent::_initialize();
    }


    /**
     * @api {POST} api/Voucher/voucher_tpl_list 代金券列表
     * @apiVersion 1.0.0
     * @apiGroup Voucher
     * 
     * @apiParam {String} store_id 店铺ID
     * @apiParam {String} gettype 代金券类型
     * 
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     * @apiSuccess {Object[]} result.voucher_list  代金券模板列表 （返回字段参考vouchertemplate）
     * @apiSuccess {String} result.voucher_list.vouchertemplate_enddate_text  代金券过期时间描述
     */
    public function voucher_tpl_list() {
        $param = array();
				$param['store_id']=input('param.store_id');
				$param['gettype']=input('param.gettype');
        $voucher_model = model('voucher');
        $templatestate_arr = $voucher_model->getTemplateState();
        $voucher_gettype_array = $voucher_model->getVoucherGettypeArray();

        $where = array();
        $where['vouchertemplate_state'] = $templatestate_arr['usable'][0];
        $store_id = intval($param['store_id']);
        if ($store_id > 0) {
            $where['vouchertemplate_store_id'] = $store_id;
        }
        $where['vouchertemplate_gettype'] = array('in', array($voucher_gettype_array['points']['sign'], $voucher_gettype_array['free']['sign']));
        if ($param['gettype'] && in_array($param['gettype'], array('points', 'free'))) {
            $where['vouchertemplate_gettype'] = $voucher_gettype_array[$param['gettype']]['sign'];
        }
        $order = 'vouchertemplate_id asc';

        $voucher_list = $voucher_model->getVouchertemplateList($where, '*', 20, 0, $order);
        if ($voucher_list) {
            foreach ($voucher_list as $k => $v) {
                $v['vouchertemplate_limit'] = floatval($v['vouchertemplate_limit']);
                $v['vouchertemplate_enddate_text'] = $v['vouchertemplate_enddate'] ? @date('Y.m.d', $v['vouchertemplate_enddate']) : '';
                $voucher_list[$k] = $v;
            }
        }
        ds_json_encode(10000, '',array('voucher_list' => $voucher_list));
    }

}

?>
