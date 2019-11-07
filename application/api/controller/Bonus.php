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
 * 红包控制器
 */
class Bonus extends MobileMall {

    public function _initialize() {
        parent::_initialize();
        Lang::load(APP_PATH.'home/lang/'.config('default_lang').'/bonus.lang.php');
    }

    /**
     * @api {POST} api/Bonus/detail 活动红包详情
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
    public function detail() {
        $bonus_id = intval(input('param.bonus_id'));
        if ($bonus_id <= 0) {
            ds_json_encode(10001, lang('param_error'));
        }
        $bonus_model = model('bonus');
        $condition=array();
        $condition['bonus_id'] = $bonus_id;
        $condition['bonus_type'] = 1;
        $condition['bonus_state'] = 1;
        $condition['bonus_begintime'] = array('<',TIMESTAMP);
        $condition['bonus_endtime'] = array('>',TIMESTAMP);
        $bonus = $bonus_model->getOneBonus($condition);
        if(!$bonus){
            ds_json_encode(10001, lang('bonus_not_exist'));
        }
        $bonus['bonus_begintime_text'] = date('Y-m-d H:i:s',$bonus['bonus_begintime']);
        $bonus['bonus_endtime_text'] = date('Y-m-d H:i:s',$bonus['bonus_endtime']);
        $result['bonus'] = $bonus;
        //获取最近10条领取记录
        $condition=array();
        $condition['bonus_id'] = $bonus_id;
        $condition['member_id'] = array('>',0);
        $result['bonusreceive_list'] = $bonus_model->getBonusreceiveList($condition, '', 10);
        ds_json_encode(10000, '', $result);
    }
    
    
    
    
}
