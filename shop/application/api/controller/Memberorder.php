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
 * 订单控制器
 */
class Memberorder extends MobileMember {

    public function _initialize() {
        parent::_initialize();
        Lang::load(APP_PATH . 'home/lang/'.config('default_lang').'/memberorder.lang.php');
    }

    /**
     * @api {POST} api/Memberorder/order_list 订单列表
     * @apiVersion 1.0.0
     * @apiGroup MemberOrder
     *
     * @apiHeader {String} X-DS-KEY 用户授权token
     *
     * @apiParam {Int} page 当前页数
     * @apiParam {Int} state_type 订单状态
     * @apiParam {String} order_key 订单编号
     * @apiParam {Int} per_page 每页数量
     *
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     * @apiSuccess {Object[]} result.order_group_list  订单组列表
     * @apiSuccess {Int} result.order_group_list.add_time  添加时间
     * @apiSuccess {Object[]} result.order_group_list.order_list  订单列表
     * @apiSuccess {Int} result.order_group_list.order_list.add_time  退款添加时间
     * @apiSuccess {String} result.order_group_list.order_list.buyer_email  买家邮箱
     * @apiSuccess {Int} result.order_group_list.order_list.buyer_id  买家ID
     * @apiSuccess {String} result.order_group_list.order_list.buyer_name  买家用户名
     * @apiSuccess {Int} result.order_group_list.order_list.delay_time  自动收货时间
     * @apiSuccess {Int} result.order_group_list.order_list.delete_state  订单删除状态 0:未删除 1:放入回收站 2:彻底删除
     * @apiSuccess {Int} result.order_group_list.order_list.evaluation_state  评论状态
     * @apiSuccess {Object} result.order_group_list.order_list.extend_order_common  订单公共信息
     * @apiSuccess {Int} result.order_group_list.order_list.extend_order_common.daddress_id  发货地址ID
     * @apiSuccess {String} result.order_group_list.order_list.extend_order_common.deliver_explain  订单发货备注
     * @apiSuccess {String} result.order_group_list.order_list.extend_order_common.dlyo_pickup_code  订单提货码
     * @apiSuccess {Int} result.order_group_list.order_list.extend_order_common.evalseller_state  卖家是否已评价买家
     * @apiSuccess {Int} result.order_group_list.order_list.extend_order_common.evalseller_time  卖家评价买家的时间
     * @apiSuccess {Int} result.order_group_list.order_list.extend_order_common.evaluation_time  评价时间
     * @apiSuccess {String} result.order_group_list.order_list.extend_order_common.invoice_info  订单发票信息
     * @apiSuccess {Int} result.order_group_list.order_list.extend_order_common.order_id  订单ID
     * @apiSuccess {String} result.order_group_list.order_list.extend_order_common.order_message  订单留言
     * @apiSuccess {Int} result.order_group_list.order_list.extend_order_common.order_pointscount  订单赠送积分
     * @apiSuccess {String} result.order_group_list.order_list.extend_order_common.promotion_info  订单促销信息备注
     * @apiSuccess {Int} result.order_group_list.order_list.extend_order_common.reciver_city_id  收货人市级ID
     * @apiSuccess {String} result.order_group_list.order_list.extend_order_common.reciver_info  收货人其它信息
     * @apiSuccess {String} result.order_group_list.order_list.extend_order_common.reciver_info.address  收货地址
     * @apiSuccess {String} result.order_group_list.order_list.extend_order_common.reciver_info.area  收货地区
     * @apiSuccess {String} result.order_group_list.order_list.extend_order_common.reciver_info.mob_phone  收货人手机号
     * @apiSuccess {String} result.order_group_list.order_list.extend_order_common.reciver_info.phone  收货人联系号码
     * @apiSuccess {String} result.order_group_list.order_list.extend_order_common.reciver_info.street  街道地址
     * @apiSuccess {String} result.order_group_list.order_list.extend_order_common.reciver_info.tel_phone  座机号
     * @apiSuccess {String} result.order_group_list.order_list.extend_order_common.reciver_name  收货人姓名
     * @apiSuccess {Int} result.order_group_list.order_list.extend_order_common.reciver_province_id  收货地区省ID
     * @apiSuccess {Int} result.order_group_list.order_list.extend_order_common.shipping_express_id  配送公司ID
     * @apiSuccess {Int} result.order_group_list.order_list.extend_order_common.shipping_time  发货时间
     * @apiSuccess {Int} result.order_group_list.order_list.extend_order_common.store_id  店铺ID
     * @apiSuccess {String} result.order_group_list.order_list.extend_order_common.voucher_code  代金券编码
     * @apiSuccess {Int} result.order_group_list.order_list.extend_order_common.voucher_price  代金券面额
     * @apiSuccess {Object} result.order_group_list.order_list.extend_store  店铺信息
     * @apiSuccess {String} result.order_group_list.order_list.extend_store.area_info  店铺地区
     * @apiSuccess {Int} result.order_group_list.order_list.extend_store.bind_all_gc  是否绑定所有分类 0否1是
     * @apiSuccess {String} result.order_group_list.order_list.extend_store.deliver_region  店铺默认配送区域
     * @apiSuccess {Int} result.order_group_list.order_list.extend_store.goods_count  商品数量
     * @apiSuccess {Int} result.order_group_list.order_list.extend_store.grade_id  等级ID
     * @apiSuccess {Int} result.order_group_list.order_list.extend_store.is_platform_store  是否自营店 0否1是
     * @apiSuccess {String} result.order_group_list.order_list.extend_store.live_store_address  商家地址
     * @apiSuccess {String} result.order_group_list.order_list.extend_store.live_store_bus  公交线路
     * @apiSuccess {String} result.order_group_list.order_list.extend_store.live_store_name  商铺名称
     * @apiSuccess {String} result.order_group_list.order_list.extend_store.live_store_tel  商铺电话
     * @apiSuccess {String} result.order_group_list.order_list.extend_store.mb_sliders  手机店铺轮播图序列化字符串
     * @apiSuccess {String} result.order_group_list.order_list.extend_store.mb_title_img  手机店铺背景图
     * @apiSuccess {Int} result.order_group_list.order_list.extend_store.member_id  店铺用户ID
     * @apiSuccess {String} result.order_group_list.order_list.extend_store.member_name  店铺用户名
     * @apiSuccess {Int} result.order_group_list.order_list.extend_store.region_id  店铺地区ID
     * @apiSuccess {String} result.order_group_list.order_list.extend_store.seller_name  卖家用户名
     * @apiSuccess {String} result.order_group_list.order_list.extend_store.store_address  店铺地址
     * @apiSuccess {Int} result.order_group_list.order_list.extend_store.store_addtime  店铺添加时间
     * @apiSuccess {Object[]} result.order_group_list.order_list.extend_store.store_aftersales  售后列表
     * @apiSuccess {String} result.order_group_list.order_list.extend_store.store_aftersales.name  售后名称
     * @apiSuccess {String} result.order_group_list.order_list.extend_store.store_aftersales.num  售后账号
     * @apiSuccess {String} result.order_group_list.order_list.extend_store.store_aftersales.type  售后类型 1QQ2旺旺3站内IM
     * @apiSuccess {Float} result.order_group_list.order_list.extend_store.store_avaliable_deposit  可用保证金
     * @apiSuccess {Float} result.order_group_list.order_list.extend_store.store_avaliable_money  可用预存款
     * @apiSuccess {String} result.order_group_list.order_list.extend_store.store_avatar  店铺头像
     * @apiSuccess {String} result.order_group_list.order_list.extend_store.store_banner  店铺背景图
     * @apiSuccess {Int} result.order_group_list.order_list.extend_store.store_baozh  是否已缴保证金 0否1是
     * @apiSuccess {Int} result.order_group_list.order_list.extend_store.store_bill_time  上次结算时间
     * @apiSuccess {String} result.order_group_list.order_list.extend_store.store_close_info  店铺关闭原因
     * @apiSuccess {Int} result.order_group_list.order_list.extend_store.store_collect  店铺收藏数量
     * @apiSuccess {String} result.order_group_list.order_list.extend_store.store_company_name  店铺公司名称
     * @apiSuccess {Object} result.order_group_list.order_list.extend_store.store_credit  店铺信用信息
     * @apiSuccess {String} result.order_group_list.order_list.extend_store.store_credit.store_deliverycredit  发货速度信息
     * @apiSuccess {Int} result.order_group_list.order_list.extend_store.store_credit.store_deliverycredit.credit  发货速度评分
     * @apiSuccess {String} result.order_group_list.order_list.extend_store.store_credit.store_deliverycredit.text  发货速度描述
     * @apiSuccess {Object} result.order_group_list.order_list.extend_store.store_credit.store_desccredit  描述相符信息
     * @apiSuccess {Int} result.order_group_list.order_list.extend_store.store_credit.store_desccredit.credit  描述相符评分
     * @apiSuccess {String} result.order_group_list.order_list.extend_store.store_credit.store_desccredit.text  描述相符描述
     * @apiSuccess {Object} result.order_group_list.order_list.extend_store.store_credit.store_servicecredit  服务态度信息
     * @apiSuccess {Int} result.order_group_list.order_list.extend_store.store_credit.store_servicecredit.credit  服务态度评分
     * @apiSuccess {String} result.order_group_list.order_list.extend_store.store_credit.store_servicecredit.text  服务态度描述
     * @apiSuccess {Int} result.order_group_list.order_list.extend_store.store_credit_average  平均评分
     * @apiSuccess {Int} result.order_group_list.order_list.extend_store.store_credit_percent  好评率
     * @apiSuccess {Int} result.order_group_list.order_list.extend_store.store_deliverycredit  发货速度评分
     * @apiSuccess {Int} result.order_group_list.order_list.extend_store.store_desccredit  描述相符评分
     * @apiSuccess {String} result.order_group_list.order_list.extend_store.store_description  店铺SEO描述
     * @apiSuccess {Int} result.order_group_list.order_list.extend_store.store_endtime  店铺到期时间
     * @apiSuccess {Int} result.order_group_list.order_list.extend_store.store_erxiaoshi  是否两小时发货 0否1是
     * @apiSuccess {Int} result.order_group_list.order_list.extend_store.store_free_price  超出该金额免运费 0未设置
     * @apiSuccess {Int} result.order_group_list.order_list.extend_store.store_free_time  商家配送时间
     * @apiSuccess {Float} result.order_group_list.order_list.extend_store.store_freeze_deposit  冻结保证金
     * @apiSuccess {Float} result.order_group_list.order_list.extend_store.store_freeze_money  冻结预存款
     * @apiSuccess {Int} result.order_group_list.order_list.extend_store.store_huodaofk  是否支持货到付款 0否1是
     * @apiSuccess {Int} result.order_group_list.order_list.extend_store.store_id  店铺ID
     * @apiSuccess {String} result.order_group_list.order_list.extend_store.store_keywords  店铺SEO关键字
     * @apiSuccess {String} result.order_group_list.order_list.extend_store.store_latitude  纬度
     * @apiSuccess {String} result.order_group_list.order_list.extend_store.store_logo  店铺logo
     * @apiSuccess {String} result.order_group_list.order_list.extend_store.store_longitude  经度
     * @apiSuccess {String} result.order_group_list.order_list.extend_store.store_mainbusiness  主营商品
     * @apiSuccess {String} result.order_group_list.order_list.extend_store.store_mgdiscount  序列化会员等级折扣
     * @apiSuccess {Int} result.order_group_list.order_list.extend_store.store_mgdiscount_state  店铺是否开启序列化会员等级折扣 0否1是
     * @apiSuccess {String} result.order_group_list.order_list.extend_store.store_name  店铺名称
     * @apiSuccess {Float} result.order_group_list.order_list.extend_store.store_payable_deposit  应缴保证金
     * @apiSuccess {String} result.order_group_list.order_list.extend_store.store_phone  店铺电话
     * @apiSuccess {Object[]} result.order_group_list.order_list.extend_store.store_presales  售前列表
     * @apiSuccess {String} result.order_group_list.order_list.extend_store.store_presales.name  售前名称
     * @apiSuccess {String} result.order_group_list.order_list.extend_store.store_presales.num  售前账号
     * @apiSuccess {String} result.order_group_list.order_list.extend_store.store_presales.type  售前类型 1QQ2旺旺3站内IM
     * @apiSuccess {String} result.order_group_list.order_list.extend_store.store_printexplain  打印订单页面下方说明文字
     * @apiSuccess {String} result.order_group_list.order_list.extend_store.store_qq  店铺QQ
     * @apiSuccess {Int} result.order_group_list.order_list.extend_store.store_qtian  是否支持7天退换 0否1是
     * @apiSuccess {Int} result.order_group_list.order_list.extend_store.store_recommend  推荐店铺 0否1是
     * @apiSuccess {Int} result.order_group_list.order_list.extend_store.store_sales  销量
     * @apiSuccess {Int} result.order_group_list.order_list.extend_store.store_servicecredit  服务态度评分
     * @apiSuccess {Int} result.order_group_list.order_list.extend_store.store_shiti  实体店认证 0否1是
     * @apiSuccess {Int} result.order_group_list.order_list.extend_store.store_shiyong  是否支持试用 0否1是
     * @apiSuccess {String} result.order_group_list.order_list.extend_store.store_slide  店铺幻灯片
     * @apiSuccess {String} result.order_group_list.order_list.extend_store.store_slide_url  店铺幻灯片链接
     * @apiSuccess {Int} result.order_group_list.order_list.extend_store.store_sort  店铺排序
     * @apiSuccess {Int} result.order_group_list.order_list.extend_store.store_state  店铺状态 0关闭，1开启，2审核中
     * @apiSuccess {Int} result.order_group_list.order_list.extend_store.store_tuihuo  是否支持退货承诺 0否1是
     * @apiSuccess {String} result.order_group_list.order_list.extend_store.store_vrcode_prefix  商家兑换码前缀
     * @apiSuccess {String} result.order_group_list.order_list.extend_store.store_workingtime  工作时间
     * @apiSuccess {String} result.order_group_list.order_list.extend_store.store_ww  店铺旺旺
     * @apiSuccess {Int} result.order_group_list.order_list.extend_store.store_xiaoxie  是否消协保证 0否1是
     * @apiSuccess {Int} result.order_group_list.order_list.extend_store.store_zhping  是否正品保障 0否1是
     * @apiSuccess {String} result.order_group_list.order_list.extend_store.store_zip  邮政编码
     * @apiSuccess {Int} result.order_group_list.order_list.extend_store.storeclass_id  店铺分类ID
     * @apiSuccess {Int} result.order_group_list.order_list.finnshed_time  订单完成时间
     * @apiSuccess {Float} result.order_group_list.order_list.goods_amount  商品总额
     * @apiSuccess {Int} result.order_group_list.order_list.if_cancel  是否可取消 true是false否
     * @apiSuccess {Int} result.order_group_list.order_list.if_delete  是否可删除 true是false否
     * @apiSuccess {Int} result.order_group_list.order_list.if_deliver  是否可发货 true是false否
     * @apiSuccess {Int} result.order_group_list.order_list.if_evaluation  是否可评价 true是false否
     * @apiSuccess {Int} result.order_group_list.order_list.if_lock  是否被锁定 true是false否
     * @apiSuccess {Int} result.order_group_list.order_list.if_receive  是否可收货 true是false否
     * @apiSuccess {Int} result.order_group_list.order_list.if_refund_cancel  是否可全部退款 true是false否
     * @apiSuccess {Int} result.order_group_list.order_list.lock_state  锁定状态:0:正常,大于0:锁定
     * @apiSuccess {Int} result.order_group_list.order_list.ob_no  结算单号
     * @apiSuccess {Float} result.order_group_list.order_list.order_amount  订单总金额
     * @apiSuccess {Int} result.order_group_list.order_list.order_from  订单来源，1:PC 2:手机
     * @apiSuccess {Int} result.order_group_list.order_list.order_id  订单ID
     * @apiSuccess {String} result.order_group_list.order_list.order_sn  订单编号
     * @apiSuccess {Int} result.order_group_list.order_list.order_state  订单状态
     * @apiSuccess {Int} result.order_group_list.order_list.order_type  订单类型
     * @apiSuccess {String} result.order_group_list.order_list.pay_sn  支付单号
     * @apiSuccess {String} result.order_group_list.order_list.payment_code  支付方式代码
     * @apiSuccess {String} result.order_group_list.order_list.payment_name  支付方式名称
     * @apiSuccess {Int} result.order_group_list.order_list.payment_time  支付时间
     * @apiSuccess {Float} result.order_group_list.order_list.pd_amount  使用预存款金额
     * @apiSuccess {Float} result.order_group_list.order_list.rcb_amount  使用充值卡金额
     * @apiSuccess {Float} result.order_group_list.order_list.refund_amount  退款金额
     * @apiSuccess {Int} result.order_group_list.order_list.refund_state  退款状态 0:无退款 1:部分退款 2:全部退款
     * @apiSuccess {String} result.order_group_list.order_list.shipping_code  发货运单号
     * @apiSuccess {Float} result.order_group_list.order_list.shipping_fee  运费
     * @apiSuccess {String} result.order_group_list.order_list.state_desc  状态描述
     * @apiSuccess {Int} result.order_group_list.order_list.store_id  店铺ID
     * @apiSuccess {String} result.order_group_list.order_list.store_name  店铺名称
     * @apiSuccess {Int} result.order_group_list.pay_amount  支付时间
     * @apiSuccess {String} result.order_group_list.pay_sn  支付单号
     * @apiSuccess {Int} result.page_total  总页数
     * @apiSuccess {Boolean} result.hasmore  是否有更多 true是false否
     */
    public function order_list() {
        $order_model = model('order');
        $condition = array();
        $condition = $this->order_type_no(input('post.state_type'));
        $condition['buyer_id'] = $this->member_info['member_id'];
        $condition['delete_state'] = 0; #订单未被删除
        $order_sn = input('post.order_key');
        if ($order_sn != '') {
            $condition['order_sn'] = array('like','%'.$order_sn.'%');
        }
        $refundreturn_model = model('refundreturn');
        $order_list_array = $order_model->getOrderList($condition, 5, '*', 'order_id desc', '', array('order_common', 'order_goods', 'store'));
        $order_list_array = $refundreturn_model->getGoodsRefundList($order_list_array, 1); //订单商品的退款退货显示

        $order_group_list = $order_pay_sn_array = array();
        foreach ($order_list_array as $value) {
            //$value['zengpin_list'] = false;
            //显示取消订单
            $value['if_cancel'] = $order_model->getOrderOperateState('buyer_cancel', $value);
            //显示退款取消订单
            $value['if_refund_cancel'] = $order_model->getOrderOperateState('refund_cancel', $value);
            //显示收货
            $value['if_receive'] = $order_model->getOrderOperateState('receive', $value);
            //显示投诉
            $order_info['if_complain'] = $order_model->getOrderOperateState('complain', $value);
            //显示锁定中
            $value['if_lock'] = $order_model->getOrderOperateState('lock', $value);
            //显示物流跟踪
            $value['if_deliver'] = $order_model->getOrderOperateState('deliver', $value);

            $value['if_evaluation'] = $order_model->getOrderOperateState('evaluation', $value);
            $value['if_delete'] = $order_model->getOrderOperateState('delete', $value);
            $value['ownshop'] = true;

            $value['zengpin_list'] = false;
            if (isset($value['extend_order_goods'])) {
                foreach ($value['extend_order_goods'] as $val) {
                    if ($val['goods_type'] == 5) {
                        $value['zengpin_list'][] = $val;
                    }
                }
            }

            //商品图
            if (isset($value['extend_order_goods'])) {
                foreach ($value['extend_order_goods'] as $k => $goods_info) {

                    if ($goods_info['goods_type'] == 5) {
                        unset($value['extend_order_goods'][$k]);
                    }
                    else {
                        $value['extend_order_goods'][$k] = $goods_info;
                        $value['extend_order_goods'][$k]['goods_image_url'] = goods_cthumb($goods_info['goods_image'], 240, $value['store_id']);
                    }
                }
            }
            $order_group_list[$value['pay_sn']]['order_list'][] = $value;
            //如果有在线支付且未付款的订单则显示合并付款链接
            if ($value['order_state'] == ORDER_STATE_NEW) {
                if(!isset($order_group_list[$value['pay_sn']]['pay_amount'])){
                    $order_group_list[$value['pay_sn']]['pay_amount'] = 0;
                }
                $order_group_list[$value['pay_sn']]['pay_amount'] += $value['order_amount'] - $value['rcb_amount'] - $value['pd_amount'];
            }
            $order_group_list[$value['pay_sn']]['add_time'] = $value['add_time'];

            //记录一下pay_sn，后面需要查询支付单表
            $order_pay_sn_array[] = $value['pay_sn'];
        }

        $new_order_group_list = array();
        foreach ($order_group_list as $key => $value) {
            $value['pay_sn'] = strval($key);
            $new_order_group_list[] = $value;
        }
        $result= array_merge(array('order_group_list' => $new_order_group_list), mobile_page($order_model->page_info));
        ds_json_encode(10000, '',$result);
    }

    private function order_type_no($stage) {
        $condition = array();
        switch ($stage) {
            case 'state_new':
                $condition['order_state'] = '10';
                break;
            case 'state_pay':
                $condition['order_state'] = '20';
                break;
            case 'state_send':
                $condition['order_state'] = '30';
                break;
            case 'state_noeval':
                $condition['order_state'] = '40';
                $condition['evaluation_state'] = '0';
                break;
        }
        return $condition;
    }

    /**
     * @api {POST} api/Memberorder/order_cancel 取消订单
     * @apiVersion 1.0.0
     * @apiGroup MemberOrder
     *
     * @apiHeader {String} X-DS-KEY 用户授权token
     *
     * @apiParam {Int} order_id 订单号
     *
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     */
    public function order_cancel() {
        $order_model = model('order');
        $logic_order = model('order','logic');
        $order_id = intval(input('post.order_id'));

        $condition = array();
        $condition['order_id'] = $order_id;
        $condition['buyer_id'] = $this->member_info['member_id'];
        //$condition['order_type'] = 1;
        $order_info = $order_model->getOrderInfo($condition);
        $if_allow = $order_model->getOrderOperateState('buyer_cancel', $order_info);
        if (!$if_allow) {
            ds_json_encode(10001,lang('have_right_operate'));
        }
 
        $result = $logic_order->changeOrderStateCancel($order_info, 'buyer', $this->member_info['member_name'], lang('other_reason'));
        if (!$result['code']) {
            ds_json_encode(10001,$result['msg']);
        } else {
            ds_json_encode(10000, '',1);
        }
    }


    /**
     * @api {POST} api/Memberorder/order_receive 订单确认收货
     * @apiVersion 1.0.0
     * @apiGroup MemberOrder
     *
     * @apiHeader {String} X-DS-KEY 用户授权token
     *
     * @apiParam {Int} order_id 订单号
     *
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     */
    public function order_receive() {
        $order_model = model('order');
        $logic_order = model('order','logic');
        $order_id = intval(input('post.order_id'));

        $condition = array();
        $condition['order_id'] = $order_id;
        $condition['buyer_id'] = $this->member_info['member_id'];
        $order_info = $order_model->getOrderInfo($condition);
        $if_allow = $order_model->getOrderOperateState('receive', $order_info);
        if (!$if_allow) {
            ds_json_encode(10001,lang('have_right_operate'));
        }

        $result = $logic_order->changeOrderStateReceive($order_info, 'buyer', $this->member_info['member_name'], lang('receive_goods'));
        if (!$result['code']) {
            ds_json_encode(10001,$result['msg']);
        } else {
            ds_json_encode(10000, '',1);
        }
    }
    
    /**
     * 回收站
     */
    public function order_delete() {
        $order_model = model('order');
        $logic_order = model('order','logic');
        $order_id = intval(input('post.order_id'));

        $condition = array();
        $condition['order_id'] = $order_id;
        $condition['buyer_id'] = $this->member_info['member_id'];
        $order_info = $order_model->getOrderInfo($condition);
        $if_allow = $order_model->getOrderOperateState('delete', $order_info);
        if (!$if_allow) {
            ds_json_encode(10001,lang('have_right_operate'));
        }
        
        $result = $logic_order->changeOrderStateRecycle($order_info, 'buyer', 'delete');
        if (!$result['code']) {
            ds_json_encode(10001,$result['msg']);
        } else {
            ds_json_encode(10000, '',1);
        }
    }

    /**
     * @api {POST} api/Memberorder/search_deliver 物流跟踪
     * @apiVersion 1.0.0
     * @apiGroup Memberorder
     *
     * @apiHeader {String} X-DS-KEY 用户授权token
     *
     * @apiParam {String} order_id 订单id
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
        $order_id = intval(input('post.order_id'));
        if ($order_id <= 0) {
            ds_json_encode(10001,lang('member_order_none_exist'));
        }

        $order_model = model('order');
        $condition['order_id'] = $order_id;
        $condition['buyer_id'] = $this->member_info['member_id'];
        $order_info = $order_model->getOrderInfo($condition, array('order_common', 'order_goods'));
        if (empty($order_info) || !in_array($order_info['order_state'], array(ORDER_STATE_SEND, ORDER_STATE_SUCCESS))) {
            ds_json_encode(10001,lang('member_order_none_exist'));
        }

        $express = rkcache('express', true);
        $express_code = $express[$order_info['extend_order_common']['shipping_express_id']]['express_code'];
        $express_name = $express[$order_info['extend_order_common']['shipping_express_id']]['express_name'];
        $deliver_info = $this->_get_express($express_code, $order_info['shipping_code']);
        ds_json_encode(10000, '',array('express_name' => $express_name, 'shipping_code' => $order_info['shipping_code'], 'deliver_info' => $deliver_info));
    }

    /**
     * @api {POST} api/Memberorder/order_info 订单详情
     * @apiVersion 1.0.0
     * @apiGroup MemberOrder
     *
     * @apiHeader {String} X-DS-KEY 用户授权token
     *
     * @apiParam {Int} order_id 订单ID
     *
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     * @apiSuccess {Object} result.order_info  订单信息
     * @apiSuccess {Int} result.order_info.add_time  退款添加时间
     * @apiSuccess {String} result.order_info.buyer_email  买家邮箱
     * @apiSuccess {Int} result.order_info.buyer_id  买家ID
     * @apiSuccess {String} result.order_info.buyer_name  买家用户名
     * @apiSuccess {Int} result.order_info.delay_time  自动收货时间
     * @apiSuccess {Int} result.order_info.delete_state  订单删除状态 0:未删除 1:放入回收站 2:彻底删除
     * @apiSuccess {Int} result.order_info.evaluation_state  评论状态
     * @apiSuccess {Object} result.order_info.extend_order_common  订单公共信息
     * @apiSuccess {Int} result.order_info.extend_order_common.daddress_id  发货地址ID
     * @apiSuccess {String} result.order_info.extend_order_common.deliver_explain  订单发货备注
     * @apiSuccess {String} result.order_info.extend_order_common.dlyo_pickup_code  订单提货码
     * @apiSuccess {Int} result.order_info.extend_order_common.evalseller_state  卖家是否已评价买家
     * @apiSuccess {Int} result.order_info.extend_order_common.evalseller_time  卖家评价买家的时间
     * @apiSuccess {Int} result.order_info.extend_order_common.evaluation_time  评价时间
     * @apiSuccess {String} result.order_info.extend_order_common.invoice_info  订单发票信息
     * @apiSuccess {Int} result.order_info.extend_order_common.order_id  订单ID
     * @apiSuccess {String} result.order_info.extend_order_common.order_message  订单留言
     * @apiSuccess {Int} result.order_info.extend_order_common.order_pointscount  订单赠送积分
     * @apiSuccess {String} result.order_info.extend_order_common.promotion_info  订单促销信息备注
     * @apiSuccess {Int} result.order_info.extend_order_common.reciver_city_id  收货人市级ID
     * @apiSuccess {String} result.order_info.extend_order_common.reciver_info  收货人其它信息
     * @apiSuccess {String} result.order_info.extend_order_common.reciver_info.address  收货地址
     * @apiSuccess {String} result.order_info.extend_order_common.reciver_info.area  收货地区
     * @apiSuccess {String} result.order_info.extend_order_common.reciver_info.mob_phone  收货人手机号
     * @apiSuccess {String} result.order_info.extend_order_common.reciver_info.phone  收货人联系号码
     * @apiSuccess {String} result.order_info.extend_order_common.reciver_info.street  街道地址
     * @apiSuccess {String} result.order_info.extend_order_common.reciver_info.tel_phone  座机号
     * @apiSuccess {String} result.order_info.extend_order_common.reciver_name  收货人姓名
     * @apiSuccess {Int} result.order_info.extend_order_common.reciver_province_id  收货地区省ID
     * @apiSuccess {Int} result.order_info.extend_order_common.shipping_express_id  配送公司ID
     * @apiSuccess {Int} result.order_info.extend_order_common.shipping_time  发货时间
     * @apiSuccess {Int} result.order_info.extend_order_common.store_id  店铺ID
     * @apiSuccess {String} result.order_info.extend_order_common.voucher_code  代金券编码
     * @apiSuccess {Int} result.order_info.extend_order_common.voucher_price  代金券面额
     * @apiSuccess {Object} result.order_info.extend_store  店铺信息
     * @apiSuccess {String} result.order_info.extend_store.area_info  店铺地区
     * @apiSuccess {Int} result.order_info.extend_store.bind_all_gc  是否绑定所有分类 0否1是
     * @apiSuccess {String} result.order_info.extend_store.deliver_region  店铺默认配送区域
     * @apiSuccess {Int} result.order_info.extend_store.goods_count  商品数量
     * @apiSuccess {Int} result.order_info.extend_store.grade_id  等级ID
     * @apiSuccess {Int} result.order_info.extend_store.is_platform_store  是否自营店 0否1是
     * @apiSuccess {String} result.order_info.extend_store.live_store_address  商家地址
     * @apiSuccess {String} result.order_info.extend_store.live_store_bus  公交线路
     * @apiSuccess {String} result.order_info.extend_store.live_store_name  商铺名称
     * @apiSuccess {String} result.order_info.extend_store.live_store_tel  商铺电话
     * @apiSuccess {String} result.order_info.extend_store.mb_sliders  手机店铺轮播图序列化字符串
     * @apiSuccess {String} result.order_info.extend_store.mb_title_img  手机店铺背景图
     * @apiSuccess {Int} result.order_info.extend_store.member_id  店铺用户ID
     * @apiSuccess {String} result.order_info.extend_store.member_name  店铺用户名
     * @apiSuccess {Int} result.order_info.extend_store.region_id  店铺地区ID
     * @apiSuccess {String} result.order_info.extend_store.seller_name  卖家用户名
     * @apiSuccess {String} result.order_info.extend_store.store_address  店铺地址
     * @apiSuccess {Int} result.order_info.extend_store.store_addtime  店铺添加时间
     * @apiSuccess {Object[]} result.order_info.extend_store.store_aftersales  售后列表
     * @apiSuccess {String} result.order_info.extend_store.store_aftersales.name  售后名称
     * @apiSuccess {String} result.order_info.extend_store.store_aftersales.num  售后账号
     * @apiSuccess {String} result.order_info.extend_store.store_aftersales.type  售后类型 1QQ2旺旺3站内IM
     * @apiSuccess {Float} result.order_info.extend_store.store_avaliable_deposit  可用保证金
     * @apiSuccess {Float} result.order_info.extend_store.store_avaliable_money  可用预存款
     * @apiSuccess {String} result.order_info.extend_store.store_avatar  店铺头像
     * @apiSuccess {String} result.order_info.extend_store.store_banner  店铺背景图
     * @apiSuccess {Int} result.order_info.extend_store.store_baozh  是否已缴保证金 0否1是
     * @apiSuccess {Int} result.order_info.extend_store.store_bill_time  上次结算时间
     * @apiSuccess {String} result.order_info.extend_store.store_close_info  店铺关闭原因
     * @apiSuccess {Int} result.order_info.extend_store.store_collect  店铺收藏数量
     * @apiSuccess {String} result.order_info.extend_store.store_company_name  店铺公司名称
     * @apiSuccess {Object} result.order_info.extend_store.store_credit  店铺信用信息
     * @apiSuccess {String} result.order_info.extend_store.store_credit.store_deliverycredit  发货速度信息
     * @apiSuccess {Int} result.order_info.extend_store.store_credit.store_deliverycredit.credit  发货速度评分
     * @apiSuccess {String} result.order_info.extend_store.store_credit.store_deliverycredit.text  发货速度描述
     * @apiSuccess {Object} result.order_info.extend_store.store_credit.store_desccredit  描述相符信息
     * @apiSuccess {Int} result.order_info.extend_store.store_credit.store_desccredit.credit  描述相符评分
     * @apiSuccess {String} result.order_info.extend_store.store_credit.store_desccredit.text  描述相符描述
     * @apiSuccess {Object} result.order_info.extend_store.store_credit.store_servicecredit  服务态度信息
     * @apiSuccess {Int} result.order_info.extend_store.store_credit.store_servicecredit.credit  服务态度评分
     * @apiSuccess {String} result.order_info.extend_store.store_credit.store_servicecredit.text  服务态度描述
     * @apiSuccess {Int} result.order_info.extend_store.store_credit_average  平均评分
     * @apiSuccess {Int} result.order_info.extend_store.store_credit_percent  好评率
     * @apiSuccess {Int} result.order_info.extend_store.store_deliverycredit  发货速度评分
     * @apiSuccess {Int} result.order_info.extend_store.store_desccredit  描述相符评分
     * @apiSuccess {String} result.order_info.extend_store.store_description  店铺SEO描述
     * @apiSuccess {Int} result.order_info.extend_store.store_endtime  店铺到期时间
     * @apiSuccess {Int} result.order_info.extend_store.store_erxiaoshi  是否两小时发货 0否1是
     * @apiSuccess {Int} result.order_info.extend_store.store_free_price  超出该金额免运费 0未设置
     * @apiSuccess {Int} result.order_info.extend_store.store_free_time  商家配送时间
     * @apiSuccess {Float} result.order_info.extend_store.store_freeze_deposit  冻结保证金
     * @apiSuccess {Float} result.order_info.extend_store.store_freeze_money  冻结预存款
     * @apiSuccess {Int} result.order_info.extend_store.store_huodaofk  是否支持货到付款 0否1是
     * @apiSuccess {Int} result.order_info.extend_store.store_id  店铺ID
     * @apiSuccess {String} result.order_info.extend_store.store_keywords  店铺SEO关键字
     * @apiSuccess {String} result.order_info.extend_store.store_latitude  纬度
     * @apiSuccess {String} result.order_info.extend_store.store_logo  店铺logo
     * @apiSuccess {String} result.order_info.extend_store.store_longitude  经度
     * @apiSuccess {String} result.order_info.extend_store.store_mainbusiness  主营商品
     * @apiSuccess {String} result.order_info.extend_store.store_mgdiscount  序列化会员等级折扣
     * @apiSuccess {Int} result.order_info.extend_store.store_mgdiscount_state  店铺是否开启序列化会员等级折扣 0否1是
     * @apiSuccess {String} result.order_info.extend_store.store_name  店铺名称
     * @apiSuccess {Float} result.order_info.extend_store.store_payable_deposit  应缴保证金
     * @apiSuccess {String} result.order_info.extend_store.store_phone  店铺电话
     * @apiSuccess {Object[]} result.order_info.extend_store.store_presales  售前列表
     * @apiSuccess {String} result.order_info.extend_store.store_presales.name  售前名称
     * @apiSuccess {String} result.order_info.extend_store.store_presales.num  售前账号
     * @apiSuccess {String} result.order_info.extend_store.store_presales.type  售前类型 1QQ2旺旺3站内IM
     * @apiSuccess {String} result.order_info.extend_store.store_printexplain  打印订单页面下方说明文字
     * @apiSuccess {String} result.order_info.extend_store.store_qq  店铺QQ
     * @apiSuccess {Int} result.order_info.extend_store.store_qtian  是否支持7天退换 0否1是
     * @apiSuccess {Int} result.order_info.extend_store.store_recommend  推荐店铺 0否1是
     * @apiSuccess {Int} result.order_info.extend_store.store_sales  销量
     * @apiSuccess {Int} result.order_info.extend_store.store_servicecredit  服务态度评分
     * @apiSuccess {Int} result.order_info.extend_store.store_shiti  实体店认证 0否1是
     * @apiSuccess {Int} result.order_info.extend_store.store_shiyong  是否支持试用 0否1是
     * @apiSuccess {String} result.order_info.extend_store.store_slide  店铺幻灯片
     * @apiSuccess {String} result.order_info.extend_store.store_slide_url  店铺幻灯片链接
     * @apiSuccess {Int} result.order_info.extend_store.store_sort  店铺排序
     * @apiSuccess {Int} result.order_info.extend_store.store_state  店铺状态 0关闭，1开启，2审核中
     * @apiSuccess {Int} result.order_info.extend_store.store_tuihuo  是否支持退货承诺 0否1是
     * @apiSuccess {String} result.order_info.extend_store.store_vrcode_prefix  商家兑换码前缀
     * @apiSuccess {String} result.order_info.extend_store.store_workingtime  工作时间
     * @apiSuccess {String} result.order_info.extend_store.store_ww  店铺旺旺
     * @apiSuccess {Int} result.order_info.extend_store.store_xiaoxie  是否消协保证 0否1是
     * @apiSuccess {Int} result.order_info.extend_store.store_zhping  是否正品保障 0否1是
     * @apiSuccess {String} result.order_info.extend_store.store_zip  邮政编码
     * @apiSuccess {Int} result.order_info.extend_store.storeclass_id  店铺分类ID
     * @apiSuccess {Int} result.order_info.finnshed_time  订单完成时间
     * @apiSuccess {Float} result.order_info.goods_amount  商品总额
     * @apiSuccess {Int} result.order_info.goods_count  商品数量
     * @apiSuccess {Object[]} result.order_info.goods_list  商品列表
     * @apiSuccess {Int} result.order_info.goods_list.buyer_id  买家ID
     * @apiSuccess {Float} result.order_info.goods_list.commis_rate  佣金比例
     * @apiSuccess {Int} result.order_info.goods_list.gc_id  分类ID
     * @apiSuccess {Int} result.order_info.goods_list.goods_id  商品ID
     * @apiSuccess {String} result.order_info.goods_list.goods_image  商品图片
     * @apiSuccess {String} result.order_info.goods_list.goods_name  商品名称
     * @apiSuccess {Int} result.order_info.goods_list.goods_num  购买数量
     * @apiSuccess {Float} result.order_info.goods_list.goods_pay_price  实际支付金额
     * @apiSuccess {Float} result.order_info.goods_list.goods_price  商品金额
     * @apiSuccess {Int} result.order_info.goods_list.goods_type  商品类型 1默认2抢购商品3限时折扣商品4组合套装5赠品6拼团7会员等级折扣
     * @apiSuccess {Int} result.order_info.goods_list.order_id  订单ID
     * @apiSuccess {Int} result.order_info.goods_list.promotions_id  促销ID
     * @apiSuccess {Int} result.order_info.goods_list.rec_id  订单商品ID
     * @apiSuccess {Int} result.order_info.goods_list.store_id  店铺ID
     * @apiSuccess {Int} result.order_info.if_cancel  是否可取消 true是false否
     * @apiSuccess {Int} result.order_info.if_delete  是否可删除 true是false否
     * @apiSuccess {Int} result.order_info.if_deliver  是否显示物流跟踪 true是false否
     * @apiSuccess {Int} result.order_info.if_evaluation  是否可评价 true是false否
     * @apiSuccess {Int} result.order_info.if_lock  是否被锁定 true是false否
     * @apiSuccess {Int} result.order_info.if_receive  是否可收货 true是false否
     * @apiSuccess {Int} result.order_info.if_refund_cancel  是否可全部退款 true是false否
     * @apiSuccess {Int} result.order_info.lock_state  锁定状态:0:正常,大于0:锁定
     * @apiSuccess {Int} result.order_info.ob_no  结算单号
     * @apiSuccess {Float} result.order_info.order_amount  订单总金额
     * @apiSuccess {Int} result.order_info.order_from  订单来源，1:PC 2:手机
     * @apiSuccess {Int} result.order_info.order_id  订单ID
     * @apiSuccess {String} result.order_info.order_sn  订单编号
     * @apiSuccess {Int} result.order_info.order_state  订单状态
     * @apiSuccess {Int} result.order_info.order_type  订单类型
     * @apiSuccess {String} result.order_info.pay_sn  支付单号
     * @apiSuccess {String} result.order_info.payment_code  支付方式代码
     * @apiSuccess {String} result.order_info.payment_name  支付方式名称
     * @apiSuccess {Int} result.order_info.payment_time  支付时间
     * @apiSuccess {Float} result.order_info.pd_amount  使用预存款金额
     * @apiSuccess {object} result.order_info.promotion  促销信息
     * @apiSuccess {Float} result.order_info.rcb_amount  使用充值卡金额
     * @apiSuccess {String} result.order_info.real_pay_amount  实际支付金额
     * @apiSuccess {String} result.order_info.reciver_addr  收货地址
     * @apiSuccess {String} result.order_info.reciver_name  收货人姓名
     * @apiSuccess {String} result.order_info.reciver_phone  收货人手机
     * @apiSuccess {Float} result.order_info.refund_amount  退款金额
     * @apiSuccess {Int} result.order_info.refund_state  退款状态 0:无退款 1:部分退款 2:全部退款
     * @apiSuccess {String} result.order_info.shipping_code  发货运单号
     * @apiSuccess {Float} result.order_info.shipping_fee  运费
     * @apiSuccess {String} result.order_info.state_desc  状态描述
     * @apiSuccess {Int} result.order_info.store_id  店铺ID
     * @apiSuccess {Int} result.order_info.store_member_id  店铺用户ID
     * @apiSuccess {String} result.order_info.store_name  店铺名称
     * @apiSuccess {String} result.order_info.store_phone  店铺电话
     * @apiSuccess {object} result.order_info.zengpin_list  赠品列表
     */
    public function order_info() {
        $order_id = intval(input('order_id'));
        if ($order_id <= 0) {
            ds_json_encode(10001,lang('member_order_none_exist'));
        }
        $order_model = model('order');
        $condition = array();
        $condition['order_id'] = $order_id;
        $condition['buyer_id'] = $this->member_info['member_id'];
        $order_info = $order_model->getOrderInfo($condition, array('order_goods', 'order_common', 'store'));

        if (empty($order_info) || $order_info['delete_state'] == ORDER_DEL_STATE_DROP) {
            ds_json_encode(10001,lang('member_order_none_exist'));
        }

        $refundreturn_model = model('refundreturn');
        $order_list = array();
        $order_list[$order_id] = $order_info;
        $order_list = $refundreturn_model->getGoodsRefundList($order_list, 1); //订单商品的退款退货显示
        $order_info = $order_list[$order_id];
        $refund_all = isset($order_info['refund_list'][0])?$order_info['refund_list'][0]:'';
        if (!empty($refund_all) && $refund_all['seller_state'] < 3) {//订单全部退款商家审核状态:1为待审核,2为同意,3为不同意
            $result['refund_all'] = $refund_all;
        }


        $order_info['store_member_id'] = $order_info['extend_store']['member_id'];
        $order_info['store_phone'] = $order_info['extend_store']['store_phone'];



		//显示系统自动取消订单日期
        if ($order_info['order_state'] == ORDER_STATE_NEW) {
            $order_info['order_cancel_day'] = $order_info['add_time'] + config('order_auto_cancel_day') * 24* 3600;
        }


        if ($order_info['extend_order_common']['order_message']) {
            $order_info['order_message'] = $order_info['extend_order_common']['order_message'];
        }
        if(!empty($order_info['extend_order_common']['invoice_info'])) {
            $order_info['invoice'] = $order_info['extend_order_common']['invoice_info']['类型'] . $order_info['extend_order_common']['invoice_info']['抬头'] . $order_info['extend_order_common']['invoice_info']['内容'];
        }

        $order_info['reciver_phone'] = $order_info['extend_order_common']['reciver_info']['phone'];
        $order_info['reciver_name'] = $order_info['extend_order_common']['reciver_name'];
        $order_info['reciver_addr'] = $order_info['extend_order_common']['reciver_info']['address'];

        $order_info['promotion'] = array();
        //显示锁定中
        $order_info['if_lock'] = $order_model->getOrderOperateState('lock', $order_info);

        //显示取消订单
        $order_info['if_buyer_cancel'] = $order_model->getOrderOperateState('buyer_cancel', $order_info);

        //显示退款取消订单
        $order_info['if_refund_cancel'] = $order_model->getOrderOperateState('refund_cancel', $order_info);

        //显示投诉
        $order_info['if_complain'] = $order_model->getOrderOperateState('complain', $order_info);

        //显示收货
        $order_info['if_receive'] = $order_model->getOrderOperateState('receive', $order_info);

        //显示物流跟踪
        $order_info['if_deliver'] = $order_model->getOrderOperateState('deliver', $order_info);
        //显示评价
        $order_info['if_evaluation'] = $order_model->getOrderOperateState('evaluation', $order_info);

        //因为格式化了时间导致按钮判断错误
        if ($order_info['payment_time']) {
            $order_info['payment_time'] = date('Y-m-d H:i:s', $order_info['payment_time']);
        } else {
            $order_info['payment_time'] = '';
        }
        if ($order_info['finnshed_time']) {
            $order_info['finnshed_time'] = date('Y-m-d H:i:s', $order_info['finnshed_time']);
        } else {
            $order_info['finnshed_time'] = '';
        }
        if ($order_info['add_time']) {
            $order_info['add_time'] = date('Y-m-d H:i:s', $order_info['add_time']);
        } else {
            $order_info['add_time'] = '';
        }
        
        $order_info['if_deliver'] = false;
        //显示快递信息
        if ($order_info['shipping_code'] != '') {
            $order_info['if_deliver'] = true;
            $express = rkcache('express', true);
            $order_info['express_info']['express_code'] = $express[$order_info['extend_order_common']['shipping_express_id']]['express_code'];
            $order_info['express_info']['express_name'] = $express[$order_info['extend_order_common']['shipping_express_id']]['express_name'];
            $order_info['express_info']['express_url'] = $express[$order_info['extend_order_common']['shipping_express_id']]['express_url'];
        }


        //显示系统自动收获时间
        if ($order_info['order_state'] == ORDER_STATE_SEND) {
            $order_info['order_confirm_day'] = $order_info['delay_time'] + config('order_auto_receive_day') * 24 * 3600;
        }

        //如果订单已取消，取得取消原因、时间，操作人
        if ($order_info['order_state'] == ORDER_STATE_CANCEL) {
            $close_info = $order_model->getOrderlogInfo(array('order_id' => $order_info['order_id']), 'log_id desc');
            $order_info['close_info'] = $close_info;
//            $order_info['state_desc'] = $close_info['log_orderstate'];
            $order_info['order_tips'] = $close_info['log_msg'];
        }
        foreach ($order_info['extend_order_goods'] as $value) {
            $value['image_240_url'] = goods_cthumb($value['goods_image'], 240, $value['store_id']);
            $value['image_url'] = goods_cthumb($value['goods_image'], 240, $value['store_id']);
            $value['goods_type_cn'] = get_order_goodstype($value['goods_type']);
            $value['goods_url'] = url('Goods/index', array('goods_id' => $value['goods_id']));
            if ($value['goods_type'] == 5) {
                $order_info['zengpin_list'][] = $value;
            } else {
                $order_info['goods_list'][] = $value;
            }
        }

        if (empty($order_info['zengpin_list'])) {
            $order_info['goods_count'] = count($order_info['goods_list']);
        } else {
            $order_info['goods_count'] = count($order_info['goods_list']) + 1;
        }

        $order_info['real_pay_amount'] = $order_info['order_amount'] + $order_info['shipping_fee'];
        //取得其它订单类型的信息000--------------------------------
        //$order_model->getOrderExtendInfo($order_info);


        $order_info['zengpin_list'] = array();
        if (is_array($order_info['extend_order_goods'])) {
            foreach ($order_info['extend_order_goods'] as $val) {
                if ($val['goods_type'] == 5) {
                    $order_info['zengpin_list'][] = $val;
                }
            }
        }
        $result['order_info'] = $order_info;
        
        ds_json_encode(10000, '',$result);
    }

    /**
     * 订单详情
     */
    public function get_current_deliver() {
        $order_id = intval(input('post.order_id'));
        if ($order_id <= 0) {
            ds_json_encode(10001,lang('member_order_none_exist'));
        }

        $order_model = model('order');
        $condition['order_id'] = $order_id;
        $condition['buyer_id'] = $this->member_info['member_id'];
        $order_info = $order_model->getOrderInfo($condition, array('order_common', 'order_goods'));
        if (empty($order_info) || !in_array($order_info['order_state'], array(ORDER_STATE_SEND, ORDER_STATE_SUCCESS))) {
            ds_json_encode(10001,lang('member_order_none_exist'));
        }

        $express = rkcache('express', true);
        if (!empty($order_info['extend_order_common']['shipping_express_id'])) {
            $express_code = $express[$order_info['extend_order_common']['shipping_express_id']]['express_code'];
            $express_name = $express[$order_info['extend_order_common']['shipping_express_id']]['express_name'];
        }else{
            $express_code = '';
            $express_name = '';
        }

        $deliver_info = $this->_get_express($express_code, $order_info['shipping_code']);


        $data = array();
        $data['deliver_info']['context'] = $express_name;
        $data['deliver_info']['time'] = $deliver_info['0'];
        ds_json_encode(10000, '',$data);
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
            ds_json_encode(10001,lang('deliver_not_support'));
        return $output;
    }

}

?>
