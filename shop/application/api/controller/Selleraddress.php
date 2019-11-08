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
 * 卖家发货地址控制器
 */
class Selleraddress extends MobileSeller {

    public function _initialize() {
        parent::_initialize();
        Lang::load(APP_PATH . 'home/lang/'.config('default_lang').'/sellerdeliver.lang.php');
        $this->model_address = model('daddress');
    }

    /**
     * @api {POST} api/Selleraddress/address_list 地址列表
     * @apiVersion 1.0.0
     * @apiGroup Selleraddress
     *
     * @apiHeader {String} X-DS-KEY 卖家授权token
     *
     * @apiParam {Int} page 页码
     * @apiParam {Int} pagesize 每页显示数量
     *
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     * @apiSuccess {Object[]} result.address_list  发货地址列表 （返回字段参考daddress表）
     */
    public function address_list() {
        $daddress_model = model('daddress');
        $condition = array();
        $condition['store_id'] = $this->store_info['store_id'];
        $address_list = $daddress_model->getAddressList($condition, '*', '', 20);
        ds_json_encode(10000, '',array('address_list' => $address_list));
    }


    /**
     * @api {POST} api/Selleraddress/address_info 地址详细信息
     * @apiVersion 1.0.0
     * @apiGroup Selleraddress
     *
     * @apiHeader {String} X-DS-KEY 卖家授权token
     *
     * @apiParam {Int} address_id 地址ID
     *
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     * @apiSuccess {Object} result.address_info  发货地址信息 （返回字段参考daddress表）
     */
    public function address_info() {
        $address_id = intval(input('param.address_id'));
        if ($address_id <= 0) {
            ds_json_encode(10001,lang('param_error'));
        }
        $condition = array();
        $condition['daddress_id'] = $address_id;
        $address_info = $this->model_address->getAddressInfo($condition);
        if (!empty($address_id) && $address_info['store_id'] == $this->store_info['store_id']) {
            ds_json_encode(10000, '',array('address_info' => $address_info));
        } else {
            ds_json_encode(10001,lang('daddress_not_exist'));
        }
    }


    /**
     * @api {POST} api/Selleraddress/address_del 删除地址
     * @apiVersion 1.0.0
     * @apiGroup Selleraddress
     *
     * @apiHeader {String} X-DS-KEY 卖家授权token
     *
     * @apiParam {Int} address_id 地址ID
     *
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     */
    public function address_del() {

        $address_id = intval(input('param.address_id'));
        if ($address_id <= 0) {
            ds_json_encode(10001,lang('param_error'));
        }
        $condition = array();
        $condition['daddress_id'] = $address_id;
        $condition['store_id'] = $this->store_info['store_id'];
        $delete = model('daddress')->delDaddress($condition);
        if ($delete) {
            ds_json_encode(10000, lang('ds_common_op_succ'));
        } else {
            ds_json_encode(10001,lang('ds_common_op_fail'));
        }
    }


    /**
     * @api {POST} api/Selleraddress/address_add 新增/编辑地址
     * @apiVersion 1.0.0
     * @apiGroup Selleraddress
     *
     * @apiHeader {String} X-DS-KEY 卖家授权token
     *
     * @apiParam {Int} address_id 地址ID 0新增
     * @apiParam {String} seller_name 发货人姓名
     * @apiParam {Int} area_id 地区ID
     * @apiParam {Int} city_id 城市ID
     * @apiParam {String} area_info 地区
     * @apiParam {String} address 地址
     * @apiParam {String} telphone 电话
     * @apiParam {Int} is_default 默认地址 1是 0否
     *
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     */
    public function address_add() {
        $daddress_model = model('daddress');

        //保存 新增/编辑 表单
        $data = array(
            'store_id' => $this->store_info['store_id'],
            'seller_name' => input('post.seller_name'),
            'area_id' => input('post.area_id'),
            'city_id' => input('post.city_id'),
            'area_info' => input('post.area_info'),
            'daddress_detail' => input('post.address'),
            'daddress_telphone' => input('post.telphone'),
            'daddress_company' => '',
            'daddress_isdefault' => intval(input('post.is_default'))
        );

        $selleraddress_validate = validate('selleraddress');
        if (!$selleraddress_validate->scene('address_add')->check($data)){
            ds_json_encode(10001,$selleraddress_validate->getError());
        }

        $address_id = intval(input('post.address_id'));
        if ($address_id > 0) {
            $condition = array();
            $condition['daddress_id'] = $address_id;
            $condition['store_id'] = $this->store_info['store_id'];
            $update = $daddress_model->editDaddress($data, $condition);
            if (!$update) {
                ds_json_encode(10001,lang('store_daddress_modify_fail'));
            }
            $is_default = intval(input('post.is_default'));
            if ($is_default == 1) {
                $condition = array();
                $condition['daddress_id'] = array('neq', $address_id);
                $condition['store_id'] = $this->store_info['store_id'];
                $update = $daddress_model->editDaddress(array('daddress_isdefault' => 0), $condition);
            }
        } else {
            $insert = $daddress_model->addDaddress($data);
            if (!$insert) {
                ds_json_encode(10001,lang('store_daddress_add_fail'));
            }
            $is_default = intval(input('post.is_default'));
            if ($is_default == 1) {
                $condition = array();
                $condition['daddress_id'] = array('neq', $insert);
                $condition['store_id'] = $this->store_info['store_id'];
                $update = $daddress_model->editDaddress(array('daddress_isdefault' => 0), $condition);
            }
        }
        ds_json_encode(10000, lang('ds_common_op_succ'));
    }
}

?>
