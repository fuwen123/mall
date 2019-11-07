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
 * 卖家退款控制器
 */
class Sellerrefund extends MobileSeller {

    public function _initialize() {
        parent::_initialize();
        Lang::load(APP_PATH . 'home/lang/'.config('default_lang').'/sellerrefund.lang.php');
    }


    /**
     * @api {POST} api/Sellerrefund/refund_list 退款记录列表页
     * @apiVersion 1.0.0
     * @apiGroup Sellerrefund
     *
     * @apiHeader {String} X-DS-KEY 卖家授权token
     *
     * @apiParam {Int} refund_type 类型 1退款 2退货
     * @apiParam {String} key 搜索关键词
     * @apiParam {String} type 搜索字段名 order_sn订单号 refund_sn退款单号 buyer_name买家用户名
     * @apiParam {String} add_time_from 开始日期 YYYY-MM-DD
     * @apiParam {String} add_time_to 结束日期 YYYY-MM-DD
     * @apiParam {Int} state 卖家处理状态:1:待审核,2:同意,3:不同意
     * @apiParam {Int} lock 订单锁定类型:1:不用锁定,2:需要锁定
     * @apiParam {String} page 页码
     * @apiParam {String} pagesize 每页显示数量
     *
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     * @apiSuccess {Object[]} result.refund_list  退款记录列表 （返回字段参考refundreturn表）
     * @apiSuccess {Object[]} result.refund_list.goods_list 退款商品列表 （返回字段参考ordergoods表）
     * @apiSuccess {String} result.refund_list.goods_list.goods_img_480 商品图片完整路径
     * @apiSuccess {String} result.refund_list.seller_state_text 卖家处理状态描述
     * @apiSuccess {String} result.refund_list.refund_state_text 管理员处理状态描述
     * @apiSuccess {String} result.refund_list.delay_time 延期时间
     * @apiSuccess {Int} result.page_total  总页数
     * @apiSuccess {Boolean} result.hasmore  是否有更多 true是false否
     */
    public function refund_list() {
        $refund_type=intval(input('refund_type'));
        if(!in_array($refund_type,array(1,2))){
            $refund_type=1;
        }
        $refundreturn_model = model('refundreturn');
        $condition = array();
        $condition['store_id'] = $this->store_info['store_id'];
        $condition['refund_type'] = $refund_type; //类型:1为退款,2为退货
        $keyword_type = array('order_sn', 'refund_sn', 'buyer_name');

        $key = input('key');
        $type = input('type');
        if (trim($key) != '' && in_array($type, $keyword_type)) {
            $condition[$type] = array('like', '%' . $key . '%');
        }
        $add_time_from = input('add_time_from');
        $add_time_to = input('add_time_to');
        if (trim($add_time_from) != '' || trim($add_time_to) != '') {

            if ($add_time_from !== false || $add_time_to !== false) {
                $condition['add_time'] = array('between time', array($add_time_from, $add_time_to));
            }
        }
        $seller_state = intval(input('state'));
        if ($seller_state > 0) {
            $condition['seller_state'] = $seller_state;
        }
        $order_lock = intval(input('lock'));
        if ($order_lock) {
            $condition['order_lock'] = $order_lock;
        }
        
        $refund_list = $refundreturn_model->getRefundList($condition, 10);
        $mobile_page=$refundreturn_model->page_info;
        $order_model=model('order');
        $trade_model=model('trade');
        $return_delay = $trade_model->getMaxDay('return_delay'); //发货默认5天后才能选择没收到
        foreach($refund_list as $k => $v){
                            $goods_list = array();
                if ($v['goods_id'] > 0) {
                    $goods = array();
                    $goods['goods_id'] = $v['goods_id'];
                    $goods['goods_name'] = $v['goods_name'];


                    $goods['goods_img_480'] = goods_thumb($v, 480);
                    $goods_list[] = $goods;
                }
                else {
                    $condition = array();
                    $condition['order_id'] = $v['order_id'];
                    $order_goods_list = $order_model->getOrdergoodsList($condition);
                    foreach ($order_goods_list as $key => $value) {
                        $goods = array();
                        $goods['goods_id'] = $value['goods_id'];
                        $goods['goods_name'] = $value['goods_name'];
                        //$goods['goods_spec'] = $value['goods_spec'];
                        $goods['goods_img_480'] = goods_thumb($value, 480);
                        $goods_list[] = $goods;
                    }
                }
                $refund_list[$k]['goods_list'] = $goods_list;
                $refund_state_array=$this->getRefundStateArray();
                $refund_list[$k]['seller_state_text'] = $refund_state_array['state_array'][$v['seller_state']];
                $refund_list[$k]['refund_state_text'] = $refund_state_array['admin_array'][$v['refund_state']];
                
                $refund_list[$k]['delay_time'] = time() - $v['delay_time'] - 60 * 60 * 24 * $return_delay;
        }
        $result = array_merge(array('refund_list' => $refund_list), mobile_page($mobile_page));
        ds_json_encode(10000, '',$result);
    }


    /**
     * @api {POST} api/Sellerrefund/edit 退款审核页
     * @apiVersion 1.0.0
     * @apiGroup Sellerrefund
     *
     * @apiHeader {String} X-DS-KEY 卖家授权token
     *
     * @apiParam {Int} refund_id 退款ID
     * @apiParam {Int} refund_type 类型 1退款 2退货
     * @apiParam {String} seller_message 卖家备注
     * @apiParam {Int} seller_state 卖家处理状态:1:待审核,2:同意,3:不同意
     *
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     */
    public function edit() {
        $refundreturn_model = model('refundreturn');
        $condition = array();
        $condition['store_id'] = $this->store_info['store_id'];
        $condition['refund_id'] = intval(input('param.refund_id'));
        $refund = $refundreturn_model->getRefundreturnInfo($condition);



            if ($refund['seller_state'] != '1') {//检查状态,防止页面刷新不及时造成数据错误
                ds_json_encode(10001,lang('param_error'));
            }
            $order_id = $refund['order_id'];
            $refund_array = array();
            $refund_array['seller_time'] = time();
            $refund_array['seller_state'] = input('post.seller_state'); //卖家处理状态:1为待审核,2为同意,3为不同意
            $refund_array['seller_message'] = input('post.seller_message');
            if ($refund_array['seller_state'] == '3') {
                $refund_array['refund_state'] = '3'; //状态:1为处理中,2为待管理员处理,3为已完成
            } else {
                $refund_array['seller_state'] = '2';
                $refund_array['refund_state'] = '2';
            }
            
            if($refund['refund_type']==2 && $refund_array['seller_state'] == '2'){
                $return_type = intval(input('post.return_type'));
                $refund_array['return_type']=$return_type;
                if(!in_array($return_type,array(1,2))){
                    $refund_array['return_type']=2;
                }
            }
            $state = $refundreturn_model->editRefundreturn($condition, $refund_array);

            if ($state) {
                if ($refund_array['seller_state'] == '3' && $refund['order_lock'] == '2') {
                    $refundreturn_model->editOrderUnlock($order_id); //订单解锁
                }
                $this->recordSellerlog(lang('refund_processing') . $refund['refund_sn']);

                // 发送买家消息
                $param = array();
                $param['code'] = 'refund_return_notice';
                $param['member_id'] = $refund['buyer_id'];
                $param['param'] = array(
                    'refund_url' => url('Memberrefund/view/', ['refund_id' => $refund['refund_id']]),
                    'refund_sn' => $refund['refund_sn']
                );
                \mall\queue\QueueClient::push('sendMemberMsg', $param);
                ds_json_encode(10000,lang('ds_common_op_succ'));
            } else {
                ds_json_encode(10001,lang('ds_common_op_fail'));
            }
    }


    /**
     * @api {POST} api/Sellerrefund/get_refund_info 退款记录查看页
     * @apiVersion 1.0.0
     * @apiGroup Sellerrefund
     *
     * @apiHeader {String} X-DS-KEY 卖家授权token
     *
     * @apiParam {Int} refund_id 退款ID
     *
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     * @apiSuccess {String[]} result.pic_list  退货凭证列表
     * @apiSuccess {Object} result.refund  退货信息 （返回字段参考refundreturn）
     */
    public function get_refund_info() {
        $refundreturn_model = model('refundreturn');
        $condition = array();
        $condition['store_id'] = $this->store_info['store_id'];
        $condition['refund_id'] = intval(input('param.refund_id'));
        $refund = $refundreturn_model->getRefundreturnInfo($condition);

        $info['buyer'] = array();
        if (!empty($refund['pic_info'])) {
            $info = unserialize($refund['pic_info']);
        }
            $pic_list = array();
            if (is_array($info['buyer'])) {
                foreach ($info['buyer'] as $k => $v) {
                    if (!empty($v)) {
                        $pic_list[] = UPLOAD_SITE_URL . '/' . ATTACH_PATH . '/refund/' . $v;
                    }
                }
            }
            ds_json_encode(10000, '', array('refund' => $refund, 'pic_list' => $pic_list));
       

    }

 
    /**
     * @api {POST} api/Sellerrefund/receive 收货
     * @apiVersion 1.0.0
     * @apiGroup Sellerrefund
     *
     * @apiHeader {String} X-DS-KEY 卖家授权token
     *
     * @apiParam {Int} refund_id 退款ID
     * @apiParam {Int} return_type 物流状态 3:延迟收货（发货需要超过5天） 4:已收货
     *
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     */
     public function receive() {
        $refundreturn_model = model('refundreturn');
        $trade_model = model('trade');
        $condition = array();
        $condition['store_id'] = $this->store_info['store_id'];
        $condition['refund_id'] = intval(input('param.return_id'));
        $return = $refundreturn_model->getRefundreturnInfo($condition);
        $return_delay = $trade_model->getMaxDay('return_delay'); //发货默认5天后才能选择没收到
        $delay_time = time() - $return['delay_time'] - 60 * 60 * 24 * $return_delay;

            if ($return['seller_state'] != '2' || $return['goods_state'] != '2') {//检查状态,防止页面刷新不及时造成数据错误
                ds_json_encode(10001,lang('param_error'));
            }
            $refund_array = array();
            if (input('post.return_type') == '3' && $delay_time > 0) {
                $refund_array['goods_state'] = '3';
            } else {
                $refund_array['receive_time'] = time();
                $refund_array['receive_message'] = lang('confirm_receipt_goods_completed');
                $refund_array['refund_state'] = '2'; //状态:1为处理中,2为待管理员处理,3为已完成
                $refund_array['goods_state'] = '4';
            }
            $state = $refundreturn_model->editRefundreturn($condition, $refund_array);
            if ($state) {
                $this->recordSellerlog(lang('confirm_receipt_goods_returned') . $return['refund_sn']);
                
                // 发送买家消息
                $param = array();
                $param['code'] = 'refund_return_notice';
                $param['member_id'] = $return['buyer_id'];
                $param['param'] = array(
                    'refund_url' => url('Memberreturn/view',['return_id'=>$return['refund_id']]),
                    'refund_sn' => $return['refund_sn']
                );
                \mall\queue\QueueClient::push('sendMemberMsg', $param);
                ds_json_encode(10000,lang('ds_common_save_succ'));
            } else {
                ds_json_encode(10001,lang('ds_common_save_fail'));
            }
    }
    function getRefundStateArray() {
        $state_array = array(
            '1' => lang('refund_state_confirm'),
            '2' => lang('refund_state_yes'),
            '3' => lang('refund_state_no')
        ); //卖家处理状态:1为待审核,2为同意,3为不同意

        $admin_array = array(
            '1' => lang('in_processing'),
            '2' => lang('to_processed'),
            '3' => lang('has_been_completed')
        ); //确认状态:1为买家或卖家处理中,2为待平台管理员处理,3为退款退货已完成
     
        return array('state_array'=>$state_array,'admin_array'=>$admin_array,);
    }

    /**
     * @api {POST} api/Sellerrefund/search_deliver 物流跟踪
     * @apiVersion 1.0.0
     * @apiGroup Sellerrefund
     *
     * @apiHeader {String} X-DS-KEY 卖家授权token
     *
     * @apiParam {String} refund_id 退款id
     *
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     * @apiSuccess {String} result.express_name  物流公司名称
     * @apiSuccess {String} result.shipping_code  物流单号
     * @apiSuccess {Object[]} result.deliver_info  物流数据
     * @apiSuccess {String} result.deliver_info.context  内容
     * @apiSuccess {String} result.deliver_info.time  时间
     */
    public function search_deliver() {
        $refund_id = intval(input('post.refund_id'));
        if ($refund_id <= 0) {
            ds_json_encode(10001,lang('param_error'));
        }
        $refundreturn_model = model('refundreturn');
        $condition = array();
        $condition['store_id'] = $this->store_info['store_id'];
        $condition['refund_id'] = $refund_id;
        $return = $refundreturn_model->getRefundreturnInfo($condition);
        if (empty($return)) {
            ds_json_encode(10001,lang('refund_not_exist'));
        }
        if(!$return['express_id'] || !$return['invoice_no']){
            ds_json_encode(10001,lang('deliver_not_exist'));
        }

        $express = rkcache('express', true);
        $express_code = $express[$return['express_id']]['express_code'];
        $express_name = $express[$return['express_id']]['express_name'];

        $deliver_info = $this->_get_express($express_code, $return['invoice_no']);
        ds_json_encode(10000, '',array('express_name' => $express_name, 'shipping_code' => $return['invoice_no'], 'deliver_info' => $deliver_info));
    }
    /**
     * 从第三方取快递信息
     *
     */
    public function _get_express($express_code, $shipping_code) {

        $result = model('express')->queryExpress($express_code,$shipping_code);

        if ($result['Success'] != true) {
            ds_json_encode(10001,lang('deliver_search_fail'));
        }
        $content['Traces'] = array_reverse($result['Traces']);
        $output = array();
        if (is_array($content['Traces'])) {
            foreach ($content['Traces'] as $k => $v) {
                if ($v['AcceptTime'] == '')
                    continue;
                //$output[] = $v['time'] . '&nbsp;&nbsp;' . $v['context'];
                $output[$k]['AcceptTime'] = $v['AcceptTime'];
                $output[$k]['AcceptStation'] = $v['AcceptStation'];
            }
        }
        if (empty($output))
            ds_json_encode(10001,lang('deliver_not_exist'));
        return $output;
    }

}
