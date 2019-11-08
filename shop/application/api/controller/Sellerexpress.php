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
 * 卖家发货控制器
 */
class Sellerexpress extends MobileSeller {
    
    public function _initialize() {
        parent::_initialize();
        Lang::load(APP_PATH . 'home/lang/'.config('default_lang').'/sellerdeliver.lang.php');
    }

    /**
     * 物流列表
     */
    public function get_list() {
        $express_list = rkcache('express', true);
        //快递公司
        $express_select = ds_getvalue_byname('storeextend', 'store_id', $this->store_info['store_id'], 'express');
        if (!is_null($express_select)) {
            $express_select = explode(',', $express_select);
        } else {
            $express_select = array();
        }
        ds_json_encode(10000, '',array('express_array' => $express_list, 'express_select' => $express_select));
    }

    /**
     * @api {POST} api/Sellerexpress/get_defaultexpress 获取默认发货信息
     * @apiVersion 1.0.0
     * @apiGroup Sellerexpress
     *
     * @apiHeader {String} X-DS-KEY 卖家授权token
     *
     * @apiParam {Int} order_id 订单ID
     * 
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     * @apiSuccess {Object} result.daddress_info  默认发货地址 （返回字段参考daddress表）
     * @apiSuccess {Object} result.orderinfo  订单信息 （返回字段参考order表）
     * @apiSuccess {Object} result.orderinfo.extend_order_common  订单其他信息 （返回字段参考ordercommon表）
     * @apiSuccess {Object} result.orderinfo.extend_order_goods  订单商品信息 （返回字段参考ordergoods表）
     */
    public function get_defaultexpress() {
        $order_id = intval(input('post.order_id'));
        if ($order_id <= 0) {
            ds_json_encode(10001,lang('param_error'));
        }
        $order_model = model('order');
        $condition = array();
        $condition['order_id'] = $order_id;
        $condition['store_id'] = $this->store_info['store_id'];

        $order_info = $order_model->getOrderInfo($condition, array('order_common', 'order_goods'));
        $if_allow_send = intval($order_info['lock_state']) || !in_array($order_info['order_state'], array(ORDER_STATE_PAY, ORDER_STATE_SEND));

        if ($if_allow_send) {
            ds_json_encode(10001,lang('param_error'));
        }

        //取发货地址
        $daddress_model = model('daddress');
        if ($order_info['extend_order_common']['daddress_id'] > 0) {
            $daddress_info = $daddress_model->getAddressInfo(array('daddress_id' => $order_info['extend_order_common']['daddress_id']));
        } else {
            //取默认地址
            $daddress_info = $daddress_model->getAddressList(array('store_id' => $this->store_info['store_id']), '*', 'daddress_isdefault desc', 1);
            if(!$daddress_info){
                ds_json_encode(12002,lang('default_daddress_not_exist'));
            }
            $daddress_info = $daddress_info[0];
        }
        ds_json_encode(10000, '',array('daddress_info' => $daddress_info, 'orderinfo' => $order_info));
    }

    /**
     * @api {POST} api/Sellerexpress/get_mylist 获取物流服务列表
     * @apiVersion 1.0.0
     * @apiGroup Sellerexpress
     *
     * @apiHeader {String} X-DS-KEY 卖家授权token
     *
     * @apiParam {Int} order_id 订单ID
     * 
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     * @apiSuccess {Object} result.orderinfo  订单信息 （返回字段参考order表）
     * @apiSuccess {Object} result.orderinfo.extend_order_common  订单其他信息 （返回字段参考ordercommon表）
     * @apiSuccess {Object} result.orderinfo.extend_order_goods  订单商品信息 （返回字段参考ordergoods表）
     * @apiSuccess {Object} result.orderinfo.extend_member  用户信息 （返回字段参考member表）
     * @apiSuccess {Object} result.express_array  物流公司列表，键为物流公司ID
     * @apiSuccess {String} result.express_array.express_code  物流公司代码
     * @apiSuccess {String} result.express_array.express_id  物流公司ID
     * @apiSuccess {String} result.express_array.express_letter  物流公司首字母
     * @apiSuccess {String} result.express_array.express_name  物流公司名称
     * @apiSuccess {String} result.express_array.express_order  排序 1:常用2:不常用
     * @apiSuccess {String} result.express_array.express_state  状态 0:不可用1:可用
     * @apiSuccess {String} result.express_array.express_url  官网地址
     * @apiSuccess {String} result.express_array.express_zt_state  是否支持服务站配送 0否1是
     */
    public function get_mylist() {
        $order_id = intval(input('post.order_id'));
        if ($order_id <= 0) {
            ds_json_encode(10001,lang('param_error'));
        }
        
        //订单信息
        $order_model = model('order');
        $condition = array();
        $condition['order_id'] = $order_id;
        $condition['store_id'] = $this->store_info['store_id'];

        $order_info = $order_model->getOrderInfo($condition, array('order_common', 'order_goods', 'member'));
        if (empty($order_info)) {
            ds_json_encode(10001,lang('store_order_none_exist'));
        }

        foreach ($order_info['extend_order_goods'] as $value) {
            $value['image_240_url'] = goods_cthumb($value['goods_image'], 240, $value['store_id']);
            $value['goods_type_cn'] = get_order_goodstype($value['goods_type']);

            $order_info['zengpin_list'] = array();
            if ($value['goods_type'] == 5) {
                $order_info['zengpin_list'][] = $value;
            } else {
                $order_info['goods_list'][] = $value;
            }
        }
        
        
        
        $express_list = rkcache('express', true);
        //如果是自提订单，只保留自提快递公司
        if (!empty($order_info['extend_order_common']['reciver_info']['dlyp'])) {
            foreach ($express_list as $k => $v) {
                if ($v['express_zt_state'] == '0')
                    unset($express_list[$k]);
            }
            $my_express_list = array_keys($express_list);
        } else {
            //快递公司
            $express_array=array();
            $my_express_list = ds_getvalue_byname('storeextend', 'store_id', $this->store_info['store_id'], 'express');
            if (!empty($my_express_list)) {
                $my_express_list = explode(',', $my_express_list);
                foreach ($my_express_list as $val) {
                    $express_array[$val] = $express_list[$val];
                }
            }
        }
        ds_json_encode(10000, '',array('orderinfo' => $order_info, 'express_array' => $express_array));
    }

    /**
     * 自提物流列表
     */
    public function get_zt_list() {
        $express_list = rkcache('express', true);
        foreach ($express_list as $k => $v) {
            if ($v['express_zt_state'] == '0')
                unset($express_list[$k]);
        }
        ds_json_encode(10000, '',array('express_array' => $express_list));
    }

    /**
     * 物流保存
     */
    public function savedefault() {
        $storeextend_model = model('storeextend');
        $data['store_id'] = $this->store_info['store_id'];
        $data['express'] = input('post.expresslists');

        if (!$storeextend_model->getby_store_id($this->store_info['store_id'])) {
            $result = $storeextend_model->insert($data);
        } else {
            $result = $storeextend_model->where(array('store_id' => $this->store_info['store_id']))->update($data);
        }
        if ($result) {
            ds_json_encode(10000, '','succ');
        } else {
            ds_json_encode(10001,'error');
        }
    }

}