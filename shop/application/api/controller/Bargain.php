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
 * 砍价活动控制器
 */
class Bargain extends MobileMall {

    public function _initialize() {
        parent::_initialize();
        Lang::load(APP_PATH.'home/lang/'.config('default_lang').'/bargain.lang.php');
    }

    /**
     * @api {POST} api/Bargain/get_list 砍价活动列表
     * @apiVersion 1.0.0
     * @apiGroup Bargain
     *
     * @apiHeader {String} X-DS-KEY 用户授权token
     * 
     * @apiParam {Int} page 页码
     * @apiParam {Int} per_page 每页数量
     *
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     * @apiSuccess {Object[]} result.bargain_list  砍价活动列表
     * @apiSuccess {String} result.bargain_list.bargain_goods_image_url  商品图片
     */
    public function get_list() {
        $pbargain_model = model('pbargain');
        $bargain_list = $pbargain_model->getOnlineBargainList(array(), $this->pagesize);
        foreach($bargain_list as $key => $val){
            $bargain_list[$key]['bargain_goods_image_url'] = goods_cthumb($val['bargain_goods_image'], 480, $val['store_id']);
        }
        $result = array_merge(array('bargain_list' => $bargain_list), mobile_page($pbargain_model->page_info));
        ds_json_encode(10000, '', $result);
    }

    /**
     * @api {POST} api/Bargain/get_info 砍价活动详情
     * @apiVersion 1.0.0
     * @apiGroup Bargain
     *
     * @apiHeader {String} X-DS-KEY 用户授权token
     *
     * @apiParam {Int} bargain_id 砍价id
     * 
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     * @apiSuccess {Object} result.bargain_info  砍价信息
     */
    public function get_info() {
        $bargain_id = input('param.bargain_id');
        if (!$bargain_id) {
            ds_json_encode(10001, lang('param_error'));
        }
        $pbargain_model = model('pbargain');

        $bargain_info = $pbargain_model->getOnlineBargainInfoByID($bargain_id);
        if (!$bargain_info) {
            ds_json_encode(10001, lang('bargain_not_exist'));
        }
        ds_json_encode(10000, '', array('bargain_info' => $bargain_info));
    }

    /**
     * @api {POST} api/Bargain/get_order_info 用户砍价活动详情
     * @apiVersion 1.0.0
     * @apiGroup Bargain
     *
     * @apiHeader {String} X-DS-KEY 用户授权token
     *
     * @apiParam {Int} bargainorder_id 用户砍价id
     * 
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     * @apiSuccess {Object} result.bargainorder_info  用户砍价信息
     * @apiSuccess {String} result.bargainorder_initiator_avatar  用户头像
     * @apiSuccess {String} result.bargain_goods_image_url  商品图片
     * @apiSuccess {Float} result.bargain_goods_diff_price  已砍金额
     * @apiSuccess {Float} result.bargain_percent  砍价进度
     */
    public function get_order_info() {
        $bargainorder_id = input('param.bargainorder_id');
        if (!$bargainorder_id) {
            ds_json_encode(10001, lang('param_error'));
        }
        $pbargainorder_model = model('pbargainorder');
        $bargainorder_info = $pbargainorder_model->getOnePbargainorder(array('bargainorder_id' => $bargainorder_id), true);
        if (!$bargainorder_info) {
            ds_json_encode(10001, lang('bargain_not_exist'));
        }
        $bargainorder_info['bargain_if_add'] = true;
        //判断是否已经砍过价
        $member_id=$this->getMemberIdIfExists();
        if($member_id){
            if(model('pbargainlog')->getOnePbargainlog(array('bargainorder_id'=>$bargainorder_id,'pbargainlog_member_id'=>$member_id))){
                $bargainorder_info['bargain_if_add'] = false;
            }
        }
        $bargainorder_info['bargainorder_initiator_avatar']=get_member_avatar_for_id($bargainorder_info['bargainorder_initiator_id']);
        $bargainorder_info['bargain_goods_image_url'] = goods_cthumb($bargainorder_info['bargain_goods_image'], 480, $bargainorder_info['store_id']);
        $bargainorder_info['bargain_goods_diff_price'] = $bargainorder_info['bargain_goods_price']-$bargainorder_info['bargainorder_current_price'];
        $bargainorder_info['bargain_percent'] = $bargainorder_info['bargainorder_times']/$bargainorder_info['bargain_total']*100;
        ds_json_encode(10000, '', array('bargainorder_info' => $bargainorder_info));
    }
    
    /**
     * @api {POST} api/Bargain/get_log_list 砍价记录列表
     * @apiVersion 1.0.0
     * @apiGroup Bargain
     *
     * @apiHeader {String} X-DS-KEY 用户授权token
     *
     * @apiParam {Int} bargainorder_id 用户砍价id
     * @apiParam {Int} page 页码
     * @apiParam {Int} per_page 每页数量
     * 
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     * @apiSuccess {Object[]} result.bargainlog_list  砍价记录
     * @apiSuccess {String} result.bargainlog_list.bargainorder_initiator_avatar  用户头像
     */
    public function get_log_list() {
        $bargainorder_id = input('param.bargainorder_id');
        if (!$bargainorder_id) {
            ds_json_encode(10001, lang('param_error'));
        }
        $pbargainlog_model = model('pbargainlog');
        $bargainlog_list = $pbargainlog_model->getPbargainlogList(array('bargainorder_id'=>$bargainorder_id), $this->pagesize);
        foreach($bargainlog_list as $key => $val){
            $bargainlog_list[$key]['pbargainlog_member_avatar']=get_member_avatar_for_id($val['pbargainlog_member_id']);
        }
        $result = array_merge(array('bargainlog_list' => $bargainlog_list), mobile_page($pbargainlog_model->page_info));
        ds_json_encode(10000, '', $result);
    }

}
