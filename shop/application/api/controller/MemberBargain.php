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
 * 用户砍价控制器
 */
class MemberBargain extends MobileMember {

    public function _initialize() {
        parent::_initialize();
        Lang::load(APP_PATH . 'home/lang/' . config('default_lang') . '/memberbargain.lang.php');
    }

    /**
     * @api {POST} api/MemberBargain/add 发起砍价
     * @apiVersion 1.0.0
     * @apiGroup MemberBargain
     *
     * @apiHeader {String} X-DS-KEY 用户授权token
     *
     * @apiParam {Int} bargain_id 砍价id
     * 
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     * @apiSuccess {Int} result.bargainorder_id  用户砍价id
     */
    public function add() {
        $bargain_id = input('param.bargain_id');
        if (!$bargain_id) {
            ds_json_encode(10001, lang('param_error'));
        }
        $pbargain_model = model('pbargain');

        $bargain_info = $pbargain_model->getOnlineBargainInfoByID($bargain_id);
        if (!$bargain_info) {
            ds_json_encode(10001, lang('bargain_not_exist'));
        }

        //是否已添加过正在进行的砍价活动
        $pbargainorder_model = model('pbargainorder');
        if ($pbargainorder_model->getOnePbargainorder(array('bargainorder_initiator_id' => $this->member_info['member_id'], 'bargain_id' => $bargain_info['bargain_id'], 'bargainorder_state' => array('in',array(1,2))))) {
            ds_json_encode(10001, lang('bargain_already_add'));
        }
        $bargainorder_id = $pbargainorder_model->addPbargainorder(array(
            'bargainorder_initiator_id' => $this->member_info['member_id'],
            'bargainorder_initiator_name' => $this->member_info['member_name'],
            'store_id' => $bargain_info['store_id'],
            'bargain_id' => $bargain_info['bargain_id'],
            'bargain_name' => $bargain_info['bargain_name'],
            'bargain_total' => $bargain_info['bargain_total'],
            'bargain_goods_id' => $bargain_info['bargain_goods_id'],
            'bargain_goods_name' => $bargain_info['bargain_goods_name'],
            'bargain_goods_price' => $bargain_info['bargain_goods_price'],
            'bargain_goods_image' => $bargain_info['bargain_goods_image'],
            'bargainorder_current_price' => $bargain_info['bargain_goods_price'],
            'bargainorder_begintime' => TIMESTAMP,
            'bargainorder_endtime' => TIMESTAMP + $bargain_info['bargain_time'] * 3600,
            'bargainorder_state' => 1,
        ));
        if (!$bargainorder_id) {
            ds_json_encode(10001, lang('ds_common_op_fail'));
        }
        ds_json_encode(10000, '', array('bargainorder_id' => $bargainorder_id));
    }

    /**
     * @api {POST} api/MemberBargain/add_log 用户砍价
     * @apiVersion 1.0.0
     * @apiGroup MemberBargain
     *
     * @apiHeader {String} X-DS-KEY 用户授权token
     *
     * @apiParam {Int} bargainorder_id 用户砍价id
     * 
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     * @apiSuccess {Boolean} result.success  砍价是否成功
     * @apiSuccess {Float} result.price  砍价金额
     * @apiSuccess {Float} result.now_price  当前价格
     */
    public function add_log() {
        $bargainorder_id = input('param.bargainorder_id');
        if (!$bargainorder_id) {
            ds_json_encode(10001, lang('param_error'));
        }
        $pbargain_model = model('pbargain');
        $pbargainorder_model = model('pbargainorder');
        $pbargainlog_model = model('pbargainlog');
        $pbargain_model->startTrans();
        $success=false;//砍价是否成功
        try {
            //是否正在进行的砍价活动

            $pbargainorder_info = $pbargainorder_model->getOnePbargainorder(array('bargainorder_id' => $bargainorder_id, 'bargainorder_state' => 1, 'bargainorder_endtime' => array('>', TIMESTAMP)), true);
            if (!$pbargainorder_info) {
                exception(lang('bargain_not_exist'));
            }
            //不能帮自己砍
            if($pbargainorder_info['bargainorder_initiator_id']==$this->member_info['member_id']){
                exception(lang('bargain_self'));
            }
            //不能重复砍
            if($pbargainlog_model->getOnePbargainlog(array('pbargainlog_member_id'=>$this->member_info['member_id'],'bargainorder_id'=>$pbargainorder_info['bargainorder_id']))){
                exception(lang('bargain_repeat'));
            }
            $bargain_info = $pbargain_model->getOnlineBargainInfoByID($pbargainorder_info['bargain_id']);
            if (!$bargain_info) {
                exception(lang('bargain_not_exist'));
            }
            
            //计算砍价金额
            $left_price = $pbargainorder_info['bargainorder_current_price'] - $bargain_info['bargain_floorprice'];
            $left_times = $pbargainorder_info['bargain_total'] - $pbargainorder_info['bargainorder_times'];
            if (($left_times - 1) > 0) {
                $max_price = ($left_price - ($left_times - 1) * 0.01) / ($left_times - 1);
                $price = round(mt_rand(1, intval($max_price * 100)) / 100, 2);
                $price = min(array($price, $bargain_info['bargain_max']));
                $now_price=$pbargainorder_info['bargainorder_current_price'] - $price;
                if (!$pbargainorder_model->editPbargainorder(array('bargainorder_id' => $pbargainorder_info['bargainorder_id']), array('bargainorder_times' => $pbargainorder_info['bargainorder_times'] + 1, 'bargainorder_current_price' => $now_price))) {
                    exception(lang('bargain_fail'));
                }
            } else {
                $price = $left_price;
                $now_price=$bargain_info['bargain_floorprice'];
                if (!$pbargainorder_model->editPbargainorder(array('bargainorder_id' => $pbargainorder_info['bargainorder_id']), array('bargainorder_state' => 2, 'bargainorder_times' => $pbargainorder_info['bargainorder_times'] + 1, 'bargainorder_current_price' => $now_price))) {
                    exception(lang('bargain_fail'));
                }
            }
            //新增砍价记录
            if(!$pbargainlog_model->addPbargainlog(array(
                'pbargainlog_member_id'=>$this->member_info['member_id'],
                'pbargainlog_member_name'=>$this->member_info['member_name'],
                'pbargainlog_price'=>$price,
                'pbargainlog_time'=>TIMESTAMP,
                'bargain_id'=>$pbargainorder_info['bargain_id'],
                'bargainorder_id'=>$pbargainorder_info['bargainorder_id'],
            ))){
                exception(lang('bargain_log_add_fail'));
            }
            
            $pbargain_model->commit();
        } catch (\Exception $e) {
            $pbargain_model->rollback();
            ds_json_encode(10001, $e->getMessage());
        }
        ds_json_encode(10000, '', array('success'=>$success,'price' => $price,'now_price'=>$now_price));
    }

    /**
     * @api {POST} api/MemberBargain/get_list 砍价活动列表
     * @apiVersion 1.0.0
     * @apiGroup MemberBargain
     *
     * @apiHeader {String} X-DS-KEY 用户授权token
     *
     * @apiParam {Int} page 页码
     * @apiParam {Int} per_page 每页数量
     * 
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     * @apiSuccess {Object[]} result.bargainorder_list  砍价活动列表
     */
    public function get_list() {
        $pbargainorder_model=model('pbargainorder');
        $bargainorder_list=$pbargainorder_model->getPbargainorderList(array('bargainorder_initiator_id'=>$this->member_info['member_id']),$this->pagesize);
        foreach($bargainorder_list as $key => $val){
            $bargainorder_list[$key]['bargain_goods_image_url'] = goods_cthumb($val['bargain_goods_image'], 480, $val['store_id']);
        }
        $result = array_merge(array('bargainorder_list' => $bargainorder_list), mobile_page($pbargainorder_model->page_info));
        ds_json_encode(10000, '',$result);
    }

    
}
