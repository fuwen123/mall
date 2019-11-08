<?php

/*
 * 交易投诉
 */

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
 * 投诉控制器
 */
class Membercomplain extends MobileMember {

    //定义投诉状态常量

    const STATE_NEW = 10;
    const STATE_APPEAL = 20;
    const STATE_TALK = 30;
    const STATE_HANDLE = 40;
    const STATE_FINISH = 99;
    const STATE_UNACTIVE = 1;
    const STATE_ACTIVE = 2;

    public function _initialize() {
        parent::_initialize();
        Lang::load(APP_PATH . 'home/lang/' . config('default_lang') . '/membercomplain.lang.php');
    }


    /**
     * @api {POST} api/Membercomplain/index 投诉列表
     * @apiVersion 1.0.0
     * @apiGroup Membercomplain
     * 
     * @apiHeader {String} X-DS-KEY 用户授权token
     * 
     * @apiParam {Int} per_page 每页显示数量
     * @apiParam {Int} page 当前页数
     * 
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     * @apiSuccess {Object[]} result.complaint_list  投诉列表
     * @apiSuccess {Int} result.complaint_list.accused_id  被告ID
     * @apiSuccess {String} result.complaint_list.accused_name  被告名称
     * @apiSuccess {Int} result.complaint_list.accuser_id  原告ID
     * @apiSuccess {String} result.complaint_list.accuser_name  原告用户名
     * @apiSuccess {Int} result.complaint_list.appeal_datetime  申诉时间，Unix时间戳
     * @apiSuccess {String} result.complaint_list.appeal_message  申诉内容
     * @apiSuccess {String} result.complaint_list.appeal_pic1  申诉图片1
     * @apiSuccess {String} result.complaint_list.appeal_pic2  申诉图片2
     * @apiSuccess {String} result.complaint_list.appeal_pic3  申诉图片3
     * @apiSuccess {Int} result.complaint_list.complain_active  投诉是否通过平台审批 1:未通过 2:通过
     * @apiSuccess {String} result.complaint_list.complain_content  投诉内容
     * @apiSuccess {Int} result.complaint_list.complain_datetime  投诉时间，Unix时间戳
     * @apiSuccess {Int} result.complaint_list.complain_handle_datetime  投诉处理时间，Unix时间戳
     * @apiSuccess {Int} result.complaint_list.complain_handle_member_id  投诉处理人ID
     * @apiSuccess {Int} result.complaint_list.complain_id  投诉ID
     * @apiSuccess {String} result.complaint_list.complain_pic1  投诉图片1
     * @apiSuccess {String} result.complaint_list.complain_pic2  投诉图片2
     * @apiSuccess {String} result.complaint_list.complain_pic3  投诉图片3
     * @apiSuccess {Int} result.complaint_list.complain_state  投诉状态 10:新投诉 20:投诉通过转给被投诉人 30:被投诉人已申诉 40:提交仲裁 99:已关闭
     * @apiSuccess {String} result.complaint_list.complain_subject_content  投诉主题
     * @apiSuccess {Int} result.complaint_list.complain_subject_id  投诉主题id
     * @apiSuccess {Int} result.complaint_list.final_handle_datetime  最终处理时间，Unix时间戳
     * @apiSuccess {Int} result.complaint_list.final_handle_member_id  最终处理人ID
     * @apiSuccess {String} result.complaint_list.final_handle_message  最终处理意见
     * @apiSuccess {Int} result.complaint_list.order_goods_id  订单商品ID
     * @apiSuccess {Int} result.complaint_list.order_id  订单ID
     * @apiSuccess {Object} result.goods_list  投诉商品列表，键为订单商品ID
     * @apiSuccess {Int} result.goods_list.buyer_id  买家ID
     * @apiSuccess {Float} result.goods_list.commis_rate  商品分类佣金比例
     * @apiSuccess {Int} result.goods_list.gc_id  分类ID
     * @apiSuccess {Int} result.goods_list.goods_id  商品ID
     * @apiSuccess {String} result.goods_list.goods_image  商品图片名称
     * @apiSuccess {String} result.goods_list.goods_image_url  商品图片完整地址
     * @apiSuccess {String} result.goods_list.goods_name  商品名称
     * @apiSuccess {Int} result.goods_list.goods_num  购买数量
     * @apiSuccess {Float} result.goods_list.goods_pay_price  商品实际支付金额
     * @apiSuccess {Float} result.goods_list.goods_price  商品价格
     * @apiSuccess {Int} result.goods_list.goods_type  商品类型 1默认2抢购商品3限时折扣商品4组合套装5赠品6拼团7会员等级折扣
     * @apiSuccess {Int} result.goods_list.order_id  订单ID
     * @apiSuccess {Int} result.goods_list.promotions_id  促销ID
     * @apiSuccess {Int} result.goods_list.rec_id  订单商品表自增ID
     * @apiSuccess {Int} result.goods_list.store_id  店铺ID
     * @apiSuccess {Int} result.page_total  总页数
     * @apiSuccess {Boolean} result.hasmore  是否有更多 true是false否
     */
    public function index() {
        /*
         * 得到当前用户的投诉列表
         */
        $complain_model = model('complain');
        $condition = array();
        $condition['accuser_id'] = $this->member_info['member_id'];
        switch (intval(input('param.select_complain_state'))) {
            case 1:
                $condition['complain_state'] = array('lt', 90);
                break;
            case 2:
                $condition['complain_state'] = 99;
                break;
        }
        $complain_list = $complain_model->getComplainList($condition,$this->pagesize);
     
        $goods_list = $complain_model->getComplainGoodsList($complain_list);
        foreach($goods_list as $key => $val){
            $goods_list[$key]['goods_image_url']=goods_thumb($val, 240);
        }
        $result= array_merge(array('complaint_list' => $complain_list,'goods_list'=>$goods_list), mobile_page($complain_model->page_info));
        ds_json_encode(10000, '',$result);
    }

    public function common_data($order_id, $goods_id) {
        //获取投诉类型
        $complainsubject_model = model('complainsubject');
        $param = array();
        $complain_subject_list = $complainsubject_model->getActiveComplainsubject($param);
        if (empty($complain_subject_list)) {
            ds_json_encode(10001, lang('complain_subject_error'));
        }
        $condition = array();
        $condition['buyer_id'] = $this->member_info['member_id'];
        $condition['order_id'] = $order_id;
        $refundreturn_model = model('refundreturn');
        $order_info = $refundreturn_model->getRightOrderList($condition, $goods_id);

        $order_info['extend_order_goods'] = $order_info['goods_list'];
        $order_list[$order_id] = $order_info;
        $order_list = $refundreturn_model->getGoodsRefundList($order_list);

        if (isset($order_list[$order_id]['extend_complain'][$goods_id]) && intval($order_list[$order_id]['extend_complain'][$goods_id]) == 1) {//退款投诉
            $complainsubject_model = model('complainsubject');
            $complain_subject = $complainsubject_model->getComplainsubject(array('complainsubject_id' => 1)); //投诉主题
            $complain_subject_list = array_merge($complain_subject, $complain_subject_list);
        }
        return array('subject_list' => $complain_subject_list);
    }
    /**
     * @api {POST} api/Membercomplain/get_common_data 新增/编辑投诉公共数据
     * @apiVersion 1.0.0
     * @apiGroup Membercomplain
     *
     * @apiHeader {String} X-DS-KEY 用户授权token
     *
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     * @apiSuccess {Object[]} result.subject_list  投诉主题列表
     * @apiSuccess {String} result.subject_list.complainsubject_content  投诉主题内容
     * @apiSuccess {String} result.subject_list.complainsubject_desc  投诉主题描述
     * @apiSuccess {Int} result.subject_list.complainsubject_id  投诉主题ID
     * @apiSuccess {Int} result.subject_list.complainsubject_state  投诉主题状态 1:有效 2:失效
     */
    public function get_common_data() {
        $order_id = intval(input('param.order_id'));
        $goods_id = intval(input('param.goods_id'));
        $common_data = $this->common_data($order_id, $goods_id);
        ds_json_encode(10000, '', $common_data);
    }

    /**
     * @api {POST} api/Membercomplain/complain_show 获取投诉信息
     * @apiVersion 1.0.0
     * @apiGroup Membercomplain
     * 
     * @apiHeader {String} X-DS-KEY 用户授权token
     * 
     * @apiParam {Int} complain_id 投诉ID
     * 
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     * @apiSuccess {String[]} result.appeal_pic  申诉凭证图片列表
     * @apiSuccess {String[]} result.complain_pic  投诉凭证图片列表
     * @apiSuccess {Object} result.complain_info  投诉信息
     * @apiSuccess {Int} result.complain_info.accused_id  被告ID
     * @apiSuccess {String} result.complain_info.accused_name  被告名称
     * @apiSuccess {Int} result.complain_info.accuser_id  原告ID
     * @apiSuccess {String} result.complain_info.accuser_name  原告用户名
     * @apiSuccess {Int} result.complain_info.appeal_datetime  申诉时间，Unix时间戳
     * @apiSuccess {String} result.complain_info.appeal_message  申诉内容
     * @apiSuccess {String} result.complain_info.appeal_pic1  申诉图片1
     * @apiSuccess {String} result.complain_info.appeal_pic2  申诉图片2
     * @apiSuccess {String} result.complain_info.appeal_pic3  申诉图片3
     * @apiSuccess {Int} result.complain_info.complain_active  投诉是否通过平台审批 1:未通过 2:通过
     * @apiSuccess {String} result.complain_info.complain_content  投诉内容
     * @apiSuccess {Int} result.complain_info.complain_datetime  投诉时间，Unix时间戳
     * @apiSuccess {Int} result.complain_info.complain_handle_datetime  投诉处理时间，Unix时间戳
     * @apiSuccess {Int} result.complain_info.complain_handle_member_id  投诉处理人ID
     * @apiSuccess {Int} result.complain_info.complain_id  投诉ID
     * @apiSuccess {String} result.complain_info.complain_pic1  投诉图片1
     * @apiSuccess {String} result.complain_info.complain_pic2  投诉图片2
     * @apiSuccess {String} result.complain_info.complain_pic3  投诉图片3
     * @apiSuccess {Int} result.complain_info.complain_state  投诉状态 10:新投诉 20:投诉通过转给被投诉人 30:被投诉人已申诉 40:提交仲裁 99:已关闭
     * @apiSuccess {String} result.complain_info.complain_subject_content  投诉主题
     * @apiSuccess {Int} result.complain_info.complain_subject_id  投诉主题id
     * @apiSuccess {Int} result.complain_info.final_handle_datetime  最终处理时间，Unix时间戳
     * @apiSuccess {Int} result.complain_info.final_handle_member_id  最终处理人ID
     * @apiSuccess {String} result.complain_info.final_handle_message  最终处理意见
     * @apiSuccess {Int} result.complain_info.order_goods_id  订单商品ID
     * @apiSuccess {Int} result.complain_info.order_id  订单ID
     * @apiSuccess {Object} result.return_info  退款信息
     * @apiSuccess {Int} result.return_info.add_time  退款添加时间
     * @apiSuccess {String} result.return_info.buyer_email  买家邮箱
     * @apiSuccess {Int} result.return_info.buyer_id  买家ID
     * @apiSuccess {String} result.return_info.buyer_name  买家用户名
     * @apiSuccess {Int} result.return_info.delay_time  自动收货时间
     * @apiSuccess {Int} result.return_info.delete_state  订单删除状态 0:未删除 1:放入回收站 2:彻底删除
     * @apiSuccess {Int} result.return_info.evaluation_state  评论状态
     * @apiSuccess {Object} result.return_info.extend_order_common  订单公共信息
     * @apiSuccess {Int} result.return_info.extend_order_common.daddress_id  发货地址ID
     * @apiSuccess {String} result.return_info.extend_order_common.deliver_explain  订单发货备注
     * @apiSuccess {String} result.return_info.extend_order_common.dlyo_pickup_code  订单提货码
     * @apiSuccess {Int} result.return_info.extend_order_common.evalseller_state  卖家是否已评价买家
     * @apiSuccess {Int} result.return_info.extend_order_common.evalseller_time  卖家评价买家的时间
     * @apiSuccess {Int} result.return_info.extend_order_common.evaluation_time  评价时间
     * @apiSuccess {String} result.return_info.extend_order_common.invoice_info  订单发票信息
     * @apiSuccess {Int} result.return_info.extend_order_common.order_id  订单ID
     * @apiSuccess {String} result.return_info.extend_order_common.order_message  订单留言
     * @apiSuccess {Int} result.return_info.extend_order_common.order_pointscount  订单赠送积分
     * @apiSuccess {String} result.return_info.extend_order_common.promotion_info  订单促销信息备注
     * @apiSuccess {Int} result.return_info.extend_order_common.reciver_city_id  收货人市级ID
     * @apiSuccess {String} result.return_info.extend_order_common.reciver_info  收货人其它信息
     * @apiSuccess {String} result.return_info.extend_order_common.reciver_info.address  收货地址
     * @apiSuccess {String} result.return_info.extend_order_common.reciver_info.area  收货地区
     * @apiSuccess {String} result.return_info.extend_order_common.reciver_info.mob_phone  收货人手机号
     * @apiSuccess {String} result.return_info.extend_order_common.reciver_info.phone  收货人联系号码
     * @apiSuccess {String} result.return_info.extend_order_common.reciver_info.street  街道地址
     * @apiSuccess {String} result.return_info.extend_order_common.reciver_info.tel_phone  座机号
     * @apiSuccess {String} result.return_info.extend_order_common.reciver_name  收货人姓名
     * @apiSuccess {Int} result.return_info.extend_order_common.reciver_province_id  收货地区省ID
     * @apiSuccess {Int} result.return_info.extend_order_common.shipping_express_id  配送公司ID
     * @apiSuccess {Int} result.return_info.extend_order_common.shipping_time  发货时间
     * @apiSuccess {Int} result.return_info.extend_order_common.store_id  店铺ID
     * @apiSuccess {String} result.return_info.extend_order_common.voucher_code  代金券编码
     * @apiSuccess {Int} result.return_info.extend_order_common.voucher_price  代金券面额
     * @apiSuccess {Object} result.return_info.extend_store  店铺信息
     * @apiSuccess {String} result.return_info.extend_store.area_info  店铺地区
     * @apiSuccess {Int} result.return_info.extend_store.bind_all_gc  是否绑定所有分类 0否1是
     * @apiSuccess {String} result.return_info.extend_store.deliver_region  店铺默认配送区域
     * @apiSuccess {Int} result.return_info.extend_store.goods_count  商品数量
     * @apiSuccess {Int} result.return_info.extend_store.grade_id  等级ID
     * @apiSuccess {Int} result.return_info.extend_store.is_platform_store  是否自营店 0否1是
     * @apiSuccess {String} result.return_info.extend_store.live_store_address  商家地址
     * @apiSuccess {String} result.return_info.extend_store.live_store_bus  公交线路
     * @apiSuccess {String} result.return_info.extend_store.live_store_name  商铺名称
     * @apiSuccess {String} result.return_info.extend_store.live_store_tel  商铺电话
     * @apiSuccess {String} result.return_info.extend_store.mb_sliders  手机店铺轮播图序列化字符串
     * @apiSuccess {String} result.return_info.extend_store.mb_title_img  手机店铺背景图
     * @apiSuccess {Int} result.return_info.extend_store.member_id  店铺用户ID
     * @apiSuccess {String} result.return_info.extend_store.member_name  店铺用户名
     * @apiSuccess {Int} result.return_info.extend_store.region_id  店铺地区ID
     * @apiSuccess {String} result.return_info.extend_store.seller_name  卖家用户名
     * @apiSuccess {String} result.return_info.extend_store.store_address  店铺地址
     * @apiSuccess {Int} result.return_info.extend_store.store_addtime  店铺添加时间
     * @apiSuccess {Object[]} result.return_info.extend_store.store_aftersales  售后列表
     * @apiSuccess {String} result.return_info.extend_store.store_aftersales.name  售后名称
     * @apiSuccess {String} result.return_info.extend_store.store_aftersales.num  售后账号
     * @apiSuccess {String} result.return_info.extend_store.store_aftersales.type  售后类型 1QQ2旺旺3站内IM
     * @apiSuccess {Float} result.return_info.extend_store.store_avaliable_deposit  可用保证金
     * @apiSuccess {Float} result.return_info.extend_store.store_avaliable_money  可用预存款
     * @apiSuccess {String} result.return_info.extend_store.store_avatar  店铺头像
     * @apiSuccess {String} result.return_info.extend_store.store_banner  店铺背景图
     * @apiSuccess {Int} result.return_info.extend_store.store_baozh  是否已缴保证金 0否1是
     * @apiSuccess {Int} result.return_info.extend_store.store_bill_time  上次结算时间
     * @apiSuccess {String} result.return_info.extend_store.store_close_info  店铺关闭原因
     * @apiSuccess {Int} result.return_info.extend_store.store_collect  店铺收藏数量
     * @apiSuccess {String} result.return_info.extend_store.store_company_name  店铺公司名称
     * @apiSuccess {Object} result.return_info.extend_store.store_credit  店铺信用信息
     * @apiSuccess {String} result.return_info.extend_store.store_credit.store_deliverycredit  发货速度信息
     * @apiSuccess {Int} result.return_info.extend_store.store_credit.store_deliverycredit.credit  发货速度评分
     * @apiSuccess {String} result.return_info.extend_store.store_credit.store_deliverycredit.text  发货速度描述
     * @apiSuccess {Object} result.return_info.extend_store.store_credit.store_desccredit  描述相符信息
     * @apiSuccess {Int} result.return_info.extend_store.store_credit.store_desccredit.credit  描述相符评分
     * @apiSuccess {String} result.return_info.extend_store.store_credit.store_desccredit.text  描述相符描述
     * @apiSuccess {Object} result.return_info.extend_store.store_credit.store_servicecredit  服务态度信息
     * @apiSuccess {Int} result.return_info.extend_store.store_credit.store_servicecredit.credit  服务态度评分
     * @apiSuccess {String} result.return_info.extend_store.store_credit.store_servicecredit.text  服务态度描述
     * @apiSuccess {Int} result.return_info.extend_store.store_credit_average  平均评分
     * @apiSuccess {Int} result.return_info.extend_store.store_credit_percent  好评率
     * @apiSuccess {Int} result.return_info.extend_store.store_deliverycredit  发货速度评分
     * @apiSuccess {Int} result.return_info.extend_store.store_desccredit  描述相符评分
     * @apiSuccess {String} result.return_info.extend_store.store_description  店铺SEO描述
     * @apiSuccess {Int} result.return_info.extend_store.store_endtime  店铺到期时间
     * @apiSuccess {Int} result.return_info.extend_store.store_erxiaoshi  是否两小时发货 0否1是
     * @apiSuccess {Int} result.return_info.extend_store.store_free_price  超出该金额免运费 0未设置
     * @apiSuccess {Int} result.return_info.extend_store.store_free_time  商家配送时间
     * @apiSuccess {Float} result.return_info.extend_store.store_freeze_deposit  冻结保证金
     * @apiSuccess {Float} result.return_info.extend_store.store_freeze_money  冻结预存款
     * @apiSuccess {Int} result.return_info.extend_store.store_huodaofk  是否支持货到付款 0否1是
     * @apiSuccess {Int} result.return_info.extend_store.store_id  店铺ID
     * @apiSuccess {String} result.return_info.extend_store.store_keywords  店铺SEO关键字
     * @apiSuccess {String} result.return_info.extend_store.store_latitude  纬度
     * @apiSuccess {String} result.return_info.extend_store.store_logo  店铺logo
     * @apiSuccess {String} result.return_info.extend_store.store_longitude  经度
     * @apiSuccess {String} result.return_info.extend_store.store_mainbusiness  主营商品
     * @apiSuccess {String} result.return_info.extend_store.store_mgdiscount  序列化会员等级折扣
     * @apiSuccess {Int} result.return_info.extend_store.store_mgdiscount_state  店铺是否开启序列化会员等级折扣 0否1是
     * @apiSuccess {String} result.return_info.extend_store.store_name  店铺名称
     * @apiSuccess {Float} result.return_info.extend_store.store_payable_deposit  应缴保证金
     * @apiSuccess {String} result.return_info.extend_store.store_phone  店铺电话
     * @apiSuccess {Object[]} result.return_info.extend_store.store_presales  售前列表
     * @apiSuccess {String} result.return_info.extend_store.store_presales.name  售前名称
     * @apiSuccess {String} result.return_info.extend_store.store_presales.num  售前账号
     * @apiSuccess {String} result.return_info.extend_store.store_presales.type  售前类型 1QQ2旺旺3站内IM
     * @apiSuccess {String} result.return_info.extend_store.store_printexplain  打印订单页面下方说明文字
     * @apiSuccess {String} result.return_info.extend_store.store_qq  店铺QQ
     * @apiSuccess {Int} result.return_info.extend_store.store_qtian  是否支持7天退换 0否1是
     * @apiSuccess {Int} result.return_info.extend_store.store_recommend  推荐店铺 0否1是
     * @apiSuccess {Int} result.return_info.extend_store.store_sales  销量
     * @apiSuccess {Int} result.return_info.extend_store.store_servicecredit  服务态度评分
     * @apiSuccess {Int} result.return_info.extend_store.store_shiti  实体店认证 0否1是
     * @apiSuccess {Int} result.return_info.extend_store.store_shiyong  是否支持试用 0否1是
     * @apiSuccess {String} result.return_info.extend_store.store_slide  店铺幻灯片
     * @apiSuccess {String} result.return_info.extend_store.store_slide_url  店铺幻灯片链接
     * @apiSuccess {Int} result.return_info.extend_store.store_sort  店铺排序
     * @apiSuccess {Int} result.return_info.extend_store.store_state  店铺状态 0关闭，1开启，2审核中
     * @apiSuccess {Int} result.return_info.extend_store.store_tuihuo  是否支持退货承诺 0否1是
     * @apiSuccess {String} result.return_info.extend_store.store_vrcode_prefix  商家兑换码前缀
     * @apiSuccess {String} result.return_info.extend_store.store_workingtime  工作时间
     * @apiSuccess {String} result.return_info.extend_store.store_ww  店铺旺旺
     * @apiSuccess {Int} result.return_info.extend_store.store_xiaoxie  是否消协保证 0否1是
     * @apiSuccess {Int} result.return_info.extend_store.store_zhping  是否正品保障 0否1是
     * @apiSuccess {String} result.return_info.extend_store.store_zip  邮政编码
     * @apiSuccess {Int} result.return_info.extend_store.storeclass_id  店铺分类ID
     * @apiSuccess {Int} result.return_info.finnshed_time  订单完成时间
     * @apiSuccess {Float} result.return_info.goods_amount  商品总额
     * @apiSuccess {Object[]} result.return_info.goods_list  商品列表
     * @apiSuccess {Int} result.return_info.goods_list.buyer_id  买家ID
     * @apiSuccess {Float} result.return_info.goods_list.commis_rate  佣金比例
     * @apiSuccess {Int} result.return_info.goods_list.gc_id  分类ID
     * @apiSuccess {Int} result.return_info.goods_list.goods_id  商品ID
     * @apiSuccess {String} result.return_info.goods_list.goods_image  商品图片
     * @apiSuccess {String} result.return_info.goods_list.goods_name  商品名称
     * @apiSuccess {Int} result.return_info.goods_list.goods_num  购买数量
     * @apiSuccess {Float} result.return_info.goods_list.goods_pay_price  实际支付金额
     * @apiSuccess {Float} result.return_info.goods_list.goods_price  商品金额
     * @apiSuccess {Int} result.return_info.goods_list.goods_type  商品类型 1默认2抢购商品3限时折扣商品4组合套装5赠品6拼团7会员等级折扣
     * @apiSuccess {Int} result.return_info.goods_list.order_id  订单ID
     * @apiSuccess {Int} result.return_info.goods_list.promotions_id  促销ID
     * @apiSuccess {Int} result.return_info.goods_list.rec_id  订单商品ID
     * @apiSuccess {Int} result.return_info.goods_list.store_id  店铺ID
     * @apiSuccess {Int} result.return_info.lock_state  锁定状态:0:正常,大于0:锁定
     * @apiSuccess {Int} result.return_info.ob_no  结算单号
     * @apiSuccess {Float} result.return_info.order_amount  订单总金额
     * @apiSuccess {Int} result.return_info.order_from  订单来源，1:PC 2:手机
     * @apiSuccess {Int} result.return_info.order_id  订单ID
     * @apiSuccess {String} result.return_info.order_sn  订单编号
     * @apiSuccess {Int} result.return_info.order_state  订单状态
     * @apiSuccess {Int} result.return_info.order_type  订单类型
     * @apiSuccess {String} result.return_info.pay_sn  支付单号
     * @apiSuccess {String} result.return_info.payment_code  支付方式代码
     * @apiSuccess {String} result.return_info.payment_name  支付方式名称
     * @apiSuccess {Int} result.return_info.payment_time  支付时间
     * @apiSuccess {Float} result.return_info.pd_amount  使用预存款金额
     * @apiSuccess {Float} result.return_info.rcb_amount  使用充值卡金额
     * @apiSuccess {Float} result.return_info.refund_amount  退款金额
     * @apiSuccess {Int} result.return_info.refund_state  退款状态 0:无退款 1:部分退款 2:全部退款
     * @apiSuccess {String} result.return_info.shipping_code  发货运单号
     * @apiSuccess {Float} result.return_info.shipping_fee  运费
     * @apiSuccess {String} result.return_info.state_desc  状态描述
     * @apiSuccess {Int} result.return_info.store_id  店铺ID
     * @apiSuccess {String} result.return_info.store_name  店铺名称
     */
    public function complain_show() {
        $complain_id = intval(input('complain_id'));
        //获取投诉详细信息
        $complain_info = $this->get_complain_info($complain_id);
        $complain_pic = array();
        $appeal_pic = array();
        for ($i = 1; $i <= 3; $i++) {
            if (!empty($complain_info['complain_pic' . $i])) {
                $complain_pic[] = UPLOAD_SITE_URL . DS . ATTACH_PATH . DS . 'complain' . DS . $complain_info['complain_pic' . $i];
            }
            if (!empty($complain_info['appeal_pic' . $i])) {
                $appeal_pic[] = UPLOAD_SITE_URL . DS . ATTACH_PATH . DS . 'complain' . DS . $complain_info['appeal_pic' . $i];
            }
        }
        $condition = array();
        $condition['buyer_id'] = $this->member_info['member_id'];
        $condition['order_id'] = $complain_info['order_id'];
        $refundreturn_model = model('refundreturn');
        $return_info = $refundreturn_model->getRightOrderList($condition, $complain_info['order_goods_id']);
        ds_json_encode(10000, '',array('appeal_pic'=>$appeal_pic,'complain_pic'=>$complain_pic,'return_info'=>$return_info,'complain_info'=>$complain_info));
    }


    /**
     * @api {POST} api/Membercomplain/complain_save 保存用户提交的投诉
     * @apiVersion 1.0.0
     * @apiGroup Membercomplain
     * 
     * @apiHeader {String} X-DS-KEY 用户授权token
     * 
     * @apiParam {Int} input_order_id 订单ID
     * @apiParam {Int} input_goods_id 订单商品ID
     * @apiParam {String} pic_name 投诉凭证 用,分隔多个投诉凭证
     * @apiParam {String} input_complain_subject 投诉主题
     * @apiParam {String} input_complain_content 投诉内容
     * 
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     */
    public function complain_save() {
        //获取输入的投诉信息
        $input = array();
        $input['order_id'] = intval(input('post.input_order_id'));
        $input['order_goods_id'] = intval(input('post.input_goods_id'));
        $pic_name = input('post.pic_name/a');
        $condition = array();
        $condition['buyer_id'] = $this->member_info['member_id'];
        $condition['order_id'] = $input['order_id'];
        $order_model = model('order');
        $order_info = $order_model->getOrderInfo($condition);
        $if_complain = $order_model->getOrderOperateState('complain', $order_info); //检查订单是否可以投诉
        if ($if_complain < 1) {
            ds_json_encode(10001, lang('param_error'));
        }
        //检查是不是正在进行投诉
        if ($this->check_complain_exist($input['order_goods_id'])) {
            ds_json_encode(10001, lang('complain_repeat'));
        }
        list($input['complain_subject_id'], $input['complain_subject_content']) = explode(',', trim(input('post.input_complain_subject')));
        $input['complain_content'] = trim(input('post.input_complain_content'));
        $input['accuser_id'] = $order_info['buyer_id'];
        $input['accuser_name'] = $order_info['buyer_name'];
        $input['accused_id'] = $order_info['store_id'];
        $input['accused_name'] = $order_info['store_name'];
        $input['complain_datetime'] = time();
        $input['complain_state'] = self::STATE_NEW;
        $input['complain_active'] = self::STATE_UNACTIVE;
        $input['complain_pic1'] = isset($pic_name[0]) ? $pic_name[0] : '';
        $input['complain_pic2'] = isset($pic_name[1]) ? $pic_name[1] : '';
        $input['complain_pic3'] = isset($pic_name[2]) ? $pic_name[2] : '';
        $complain_model = model('complain');
        $state = $complain_model->addComplain($input); //保存投诉信息
        if ($state) {
            ds_json_encode(10000, lang('complain_submit_success'));
        } else {
            ds_json_encode(10001, lang('ds_common_save_fail'));
        }
    }


    /**
     * @api {POST} api/Membercomplain/complain_cancel 取消用户提交的投诉
     * @apiVersion 1.0.0
     * @apiGroup Membercomplain
     * 
     * @apiHeader {String} X-DS-KEY 用户授权token
     * 
     * @apiParam {Int} complain_id 投诉ID
     * 
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     */
    public function complain_cancel() {
        $complain_id = intval(input('param.complain_id'));
        $complain_info = $this->get_complain_info($complain_id);
        if (intval($complain_info['complain_state']) === 10) {
            $pics = array();
            if (!empty($complain_info['complain_pic1']))
                $pics[] = $complain_info['complain_pic1'];
            if (!empty($complain_info['complain_pic2']))
                $pics[] = $complain_info['complain_pic2'];
            if (!empty($complain_info['complain_pic3']))
                $pics[] = $complain_info['complain_pic3'];
            if (!empty($pics)) {//删除图片
                foreach ($pics as $pic) {
                    $pic = BASE_UPLOAD_PATH . DS . ATTACH_PATH . DS . 'complain' . DS . $pic;
                    if (file_exists($pic)) {
                        @unlink($pic);
                    }
                }
            }
            $complain_model = model('complain');
            $complain_model->delComplain(array('complain_id' => $complain_id));
            ds_json_encode(10000, lang('complain_cancel_success'));
        } else {
            ds_json_encode(10001, lang('complain_cancel_fail'));
        }
    }


    /**
     * @api {POST} api/Membercomplain/apply_handle 处理用户申请仲裁
     * @apiVersion 1.0.0
     * @apiGroup Membercomplain
     * 
     * @apiHeader {String} X-DS-KEY 用户授权token
     * 
     * @apiParam {Int} input_complain_id 投诉ID
     * 
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     */
    public function apply_handle() {
        $complain_id = intval(input('post.input_complain_id'));
        //获取投诉详细信息
        $complain_info = $this->get_complain_info($complain_id);
        $complain_state = intval($complain_info['complain_state']);
        //检查当前是不是投诉状态
        if ($complain_state < self::STATE_TALK || $complain_state === 99) {
            ds_json_encode(10001, lang('param_error'));
        }
        $update_array = array();
        $update_array['complain_state'] = self::STATE_HANDLE;
        $where_array = array();
        $where_array['complain_id'] = $complain_id;
        //保存投诉信息
        $complain_model = model('complain');
        $complain_model->editComplain($update_array, $where_array);
        ds_json_encode(10000, lang('handle_submit_success'));
    }


    /**
     * @api {POST} api/Membercomplain/get_complain_talk 根据投诉id获取投诉对话
     * @apiVersion 1.0.0
     * @apiGroup Membercomplain
     * 
     * @apiHeader {String} X-DS-KEY 用户授权token
     * 
     * @apiParam {Int} complain_id 投诉ID
     * 
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     * @apiSuccess {Object[]} result.talk_list  对话列表
     * @apiSuccess {String} result.talk_list.css  对话类型 admin管理员，accused被告人，accuser原告人
     * @apiSuccess {String} result.talk_list.talk  对话内容
     */
    public function get_complain_talk() {
        $complain_id = intval(input('post.complain_id'));
        $complain_info = $this->get_complain_info($complain_id);
        $complaintalk_model = model('complaintalk');
        $param = array();
        $param['complain_id'] = $complain_id;
        $complain_talk_list = $complaintalk_model->getComplaintalkList($param);
        $talk_list = array();
        $i = 0;
        foreach ($complain_talk_list as $talk) {
            $talk_list[$i]['css'] = $talk['talk_member_type'];
            $talk_list[$i]['talk'] = date("Y-m-d H:i:s", $talk['talk_datetime']);
            switch ($talk['talk_member_type']) {
                case 'accuser':
                    $talk_list[$i]['talk'] .= lang('complain_accuser');
                    break;
                case 'accused':
                    $talk_list[$i]['talk'] .= lang('complain_accused');
                    break;
                case 'admin':
                    $talk_list[$i]['talk'] .= lang('complain_admin');
                    break;
                default:
                    $talk_list[$i]['talk'] .= lang('complain_unknow');
            }
            if (intval($talk['talk_state']) === 2) {
                $talk['talk_content'] = lang('talk_forbit_message');
            }
            $talk_list[$i]['talk'] .= '(' . $talk['talk_member_name'] . ')' . lang('complain_text_say') . ':' . $talk['talk_content'];
            $i++;
        }

        ds_json_encode(10000, '', array('talk_list' => $talk_list));
    }

    /**
     * @api {POST} api/Membercomplain/publish_complain_talk 根据发布投诉对话
     * @apiVersion 1.0.0
     * @apiGroup Membercomplain
     * 
     * @apiHeader {String} X-DS-KEY 用户授权token
     * 
     * @apiParam {Int} complain_id 投诉ID
     * @apiParam {String} complain_talk 对话内容
     * 
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     */
    public function publish_complain_talk() {
        $complain_id = intval(input('post.complain_id'));
        $complain_talk = trim(input('post.complain_talk'));
        $talk_len = strlen($complain_talk);
        if ($talk_len > 0 && $talk_len < 255) {
            $complain_info = $this->get_complain_info($complain_id);
            $complain_state = intval($complain_info['complain_state']);
            //检查投诉是否是可发布对话状态
            if ($complain_state > self::STATE_APPEAL && $complain_state < self::STATE_FINISH) {
                $complaintalk_model = model('complaintalk');
                $param = array();
                $param['complain_id'] = $complain_id;
                $param['talk_member_id'] = $complain_info['accuser_id'];
                $param['talk_member_name'] = $complain_info['accuser_name'];
                $param['talk_member_type'] = $complain_info['member_status'];

                $param['talk_content'] = $complain_talk;
                $param['talk_state'] = 1;
                $param['talk_admin'] = 0;
                $param['talk_datetime'] = time();
                if ($complaintalk_model->addComplaintalk($param)) {
                    ds_json_encode(10000,('talk_send_success'));
                } else {
                   ds_json_encode(10001,('talk_send_fail'));
                }
            } else {
                ds_json_encode(10001, lang('talk_state_error'));
            }
        } else {
            ds_json_encode(10001, lang('talk_null'));
        }
    }

    /*
     * 获取投诉信息
     */

    private function get_complain_info($complain_id) {
        $complain_model = model('complain');
        $complain_info = $complain_model->getOneComplain($complain_id);
        if ($complain_info['accuser_id'] != $this->member_info['member_id']) {
            ds_json_encode(10001, lang('param_error'));
        }
        $complain_info['member_status'] = 'accuser';
        $complain_info['complain_state_text'] = $this->get_complain_state_text($complain_info['complain_state']);
        return $complain_info;
    }

    /*
     * 检查投诉是否已经存在
     */

    private function check_complain_exist($goods_id) {
        $complain_model = model('complain');
        $param = array();
        $param['order_goods_id'] = $goods_id;
        $param['accuser_id'] = $this->member_info['member_id'];
        $param['complain_state'] = array('lt', 90);
        return $complain_model->isComplainExist($param);
    }

    /*
     * 获得投诉状态文本
     */

    private function get_complain_state_text($complain_state) {
        switch (intval($complain_state)) {
            case self::STATE_NEW:
                return lang('complain_state_new');
                break;
            case self::STATE_APPEAL:
                return lang('complain_state_appeal');
                break;
            case self::STATE_TALK:
                return lang('complain_state_talk');
                break;
            case self::STATE_HANDLE:
                return lang('complain_state_handle');
                break;
            case self::STATE_FINISH:
                return lang('complain_state_finish');
                break;
            default:
                ds_json_encode(10001, lang('param_error'));
        }
    }

    /**
     * @api {POST} api/Membercomplain/upload_pic 上传凭证
     * @apiVersion 1.0.0
     * @apiGroup Membercomplain
     *
     * @apiHeader {String} X-DS-KEY 用户授权token
     *
     * @apiParam {File} complain_pic 投诉凭证
     *
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     * @apiSuccess {String} result.file_name  文件名称
     * @apiSuccess {String} result.pic  文件完整路径
     */
    public function upload_pic() {
        if (!empty($_FILES['complain_pic']['name'])) {
            $upload = request()->file('complain_pic');
            $uploaddir = BASE_UPLOAD_PATH . DS . ATTACH_PATH . DS . 'complain' . DS;
            $file_name = $this->member_info['member_id'] . '_' . date('YmdHis') . rand(10000, 99999);
            $result = $upload->rule('uniqid')->validate(['ext' => ALLOW_IMG_EXT])->move($uploaddir,$file_name);
            if ($result) {
                $file_name = $result->getFilename();
            } else {
                ds_json_encode(10001, lang('file_upload_error'));
            }
            $pic = UPLOAD_SITE_URL . DS . ATTACH_PATH . DS . 'complain' . DS . $file_name;
            ds_json_encode(10000, '', array('file_name' => $file_name, 'pic' => $pic));
        }else{
            ds_json_encode(10001, lang('file_empty'));
        }
        
    }

}

?>
