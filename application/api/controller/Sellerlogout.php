<?php

namespace app\api\controller;

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
 * 卖家登出控制器
 */
class Sellerlogout extends MobileSeller {

    public function _initialize() {
        parent::_initialize();
    }


    /**
     * @api {POST} api/Sellerlogout/group_edit 注销
     * @apiVersion 1.0.0
     * @apiGroup Sellerlogout
     *
     * @apiHeader {String} X-DS-KEY 卖家授权token
     * 
     * @apiParam {String} seller_name 卖家账号
     * @apiParam {String} client 客户端类型 android wap wechat ios windows jswechat
     *
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     */
    public function index() {
        if (empty(input('post.seller_name')) || !in_array(input('post.client'), $this->client_type_array)) {
            ds_json_encode(10001, lang('param_error'));
        }
        $mbsellertoken_model = model('mbsellertoken');
        if ($this->seller_info['seller_name'] == input('post.seller_name')) {
            $condition = array();
            $condition['seller_id'] = $this->seller_info['seller_id'];
            $mbsellertoken_model->delMbsellertoken($condition);
            ds_json_encode(10000, '', 1);
        } else {
            ds_json_encode(10001, lang('param_error'));
        }
    }

}
