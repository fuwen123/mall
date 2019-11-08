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
 * 分销控制器
 */
class Memberinviter extends MobileMember {

    public function _initialize() {
        parent::_initialize();
        Lang::load(APP_PATH . 'home/lang/' . config('default_lang') . '/memberinviter.lang.php');
        if (!config('inviter_open')) {
            ds_json_encode(10001,lang('inviter_not_open'));
        }
    }

    

    /**
     * @api {POST} api/Memberinviter/check 检测是否有推广权限，符合条件自动新增推广员
     * @apiVersion 1.0.0
     * @apiGroup Memberinviter
     *
     * @apiHeader {String} X-DS-KEY 用户授权token
     *
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     */
    public function check() {
        $inviter_model = model('inviter');
        $inviter_info = $inviter_model->getInviterInfo('i.inviter_id=' . $this->member_info['member_id']);
        if (!$inviter_info) {
            //是否有分销门槛
            if (config('inviter_condition')) {
                //检查消费金额
                $order_amount = db('order')->where('buyer_id=' . $this->member_info['member_id'] . ' AND order_state=' . ORDER_STATE_SUCCESS . ' AND lock_state=0')->sum('order_amount-refund_amount');
                if ($order_amount < config('inviter_condition_amount')) {
                    ds_json_encode(10001,sprintf(lang('inviter_condition_amount'), $order_amount, config('inviter_condition_amount')));
                }
            }
            $inviter_model->addInviter(array(
                'inviter_id' => $this->member_info['member_id'],
                'inviter_state' => config('inviter_view') ? 0 : 1,
                'inviter_applytime' => TIMESTAMP,
            ));
            if (config('inviter_view')) {
                ds_json_encode(10001,lang('inviter_view'));
            } else {
                ds_json_encode(10000, '');
            }
        } else {
            if ($inviter_info['inviter_state'] == 0) {
                ds_json_encode(10001,lang('inviter_view'));
            } elseif ($inviter_info['inviter_state'] == 2) {
                ds_json_encode(10001,lang('inviter_close'));
            }else{
                ds_json_encode(10000, '');
            }
        }
    }

    /**
     * @api {POST} api/Memberinviter/index 首页显示
     * @apiVersion 1.0.0
     * @apiGroup Memberinviter
     *
     * @apiHeader {String} X-DS-KEY 用户授权token
     *
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     * @apiSuccess {String} result.refer_qrcode_logo  分销海报
     * @apiSuccess {String} result.inviter_url  分销url
     * @apiSuccess {String} result.refer_qrcode_weixin  分销微信二维码
     * @apiSuccess {String} result.wx_error_msg  微信错误信息
     */
    public function index() {
        $member_info = $this->member_info;
        //生成微信推广二维码
        $inviter_model=model('inviter');
        $qrcode_weixin = $inviter_model->qrcode_weixin($member_info);
        //生成URL推广二维码
        $inviter_model->qrcode_logo($member_info);
        
        $result = array(
            'refer_qrcode_logo' => UPLOAD_SITE_URL . '/' . ATTACH_INVITER . '/' . $member_info['member_id'] . '_poster.png',
            'inviter_url' => H5_SITE_URL . '/home/memberregister?inviter_id=' . $member_info['member_id'],
            'refer_qrcode_weixin' => $qrcode_weixin['refer_qrcode_weixin'],
            'wx_error_msg' => $qrcode_weixin['wx_error_msg']
        );
        ds_json_encode(10000, '',$result);
    }

    /**
     * @api {POST} api/Memberinviter/user 获取推广会员
     * @apiVersion 1.0.0
     * @apiGroup Memberinviter
     *
     * @apiHeader {String} X-DS-KEY 用户授权token
     * 
     * @apiParam {Int} page 页码
     * @apiParam {Int} per_page 每页数量
     *
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     * @apiSuccess {Object[]} result.list  用户列表
     * @apiSuccess {Int} result.list.member_id  用户ID
     * @apiSuccess {String} result.list.member_name  用户名称
     * @apiSuccess {String} result.list.member_avatar  用户头像
     * @apiSuccess {String} result.list.member_addtime  注册时间
     * @apiSuccess {String} result.list.member_logintime  登录时间
     * @apiSuccess {Object[]} result.list.inviters  上级分销员列表
     * @apiSuccess {Int} result.page_total  总页数
     * @apiSuccess {Boolean} result.hasmore  是否有更多 true是false否
     */
    public function user() {
        $member_model = model('member');
        $conditions = array('inviter_id' => $this->member_info['member_id']);
        if (input('param.member_name')) {
            $conditions['member_name'] = array('like', '%' . input('param.member_name') . '%');
        }
        $list = $member_model->getMemberList($conditions, 'member_id,member_name,member_avatar,member_addtime,member_logintime', 10, 'member_id desc');
        if (is_array($list)) {
            foreach ($list as $key => $val) {
                $list[$key]['member_avatar'] = get_member_avatar($val['member_avatar']) . '?' . microtime();
                $list[$key]['member_addtime'] = $val['member_addtime'] ? date('Y-m-d H:i:s', $val['member_addtime']) : '';
                $list[$key]['member_logintime'] = $val['member_logintime'] ? date('Y-m-d H:i:s', $val['member_logintime']) : '';
                //该会员的2级内推荐会员
                $list[$key]['inviters'] = array();
                $inviter_1 = db('member')->where('inviter_id', $val['member_id'])->field('member_id,member_name')->find();
                if ($inviter_1) {
                    $list[$key]['inviters'][] = $inviter_1['member_name'];
                    $inviter_2 = db('member')->where('inviter_id', $inviter_1['member_id'])->field('member_id,member_name')->find();
                    if ($inviter_2) {
                        $list[$key]['inviters'][] = $inviter_2['member_name'];
                    }
                }
            }
        }
        $result = array_merge(array('list' => $list), mobile_page($member_model->page_info));
        ds_json_encode(10000, '',$result);
    }
    /**
     * @api {POST} api/Memberinviter/order 获取推广业绩
     * @apiVersion 1.0.0
     * @apiGroup Memberinviter
     *
     * @apiHeader {String} X-DS-KEY 用户授权token
     * 
     * @apiParam {Int} page 页码
     * @apiParam {Int} per_page 每页数量
     *
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     * @apiSuccess {Object[]} result.list  分销业绩列表
     * @apiSuccess {Int} result.list.orderinviter_id  分销业绩ID
     * @apiSuccess {Int} result.list.orderinviter_order_id  订单ID
     * @apiSuccess {Int} result.list.orderinviter_member_id  用户ID
     * @apiSuccess {Float} result.list.orderinviter_money  分销金额
     * @apiSuccess {String} result.list.orderinviter_remark  分销业绩描述
     * @apiSuccess {String} result.list.orderinviter_member_name  用户名称
     * @apiSuccess {String} result.list.orderinviter_order_sn  订单编号
     * @apiSuccess {Int} result.list.orderinviter_goods_commonid  商品公共ID
     * @apiSuccess {Int} result.list.orderinviter_goods_id  商品ID
     * @apiSuccess {Int} result.list.orderinviter_level  分销级别
     * @apiSuccess {String} result.list.orderinviter_goods_name  分销商品名称
     * @apiSuccess {Int} result.list.orderinviter_valid  是否有效 0否1是
     * @apiSuccess {Int} result.list.orderinviter_store_id  店铺ID
     * @apiSuccess {Int} result.list.orderinviter_order_type  订单类型（0实物订单1虚拟订单）
     * @apiSuccess {Int} result.list.orderinviter_goods_quantity  购买数量
     * @apiSuccess {Float} result.list.orderinviter_goods_amount  商品总价
     * @apiSuccess {String} result.list.orderinviter_store_name  店铺名称
     * @apiSuccess {Int} result.list.orderinviter_addtime  添加时间
     * @apiSuccess {Int} result.page_total  总页数
     * @apiSuccess {Boolean} result.hasmore  是否有更多 true是false否
     */
    public function order() {
        $conditions = array('orderinviter_member_id' => $this->member_info['member_id']);
        if (input('param.orderinviter_order_sn')) {
            $conditions['orderinviter_order_sn'] = array('like', '%' . input('param.orderinviter_order_sn') . '%');
        }
        $list = db('orderinviter')->where($conditions)->order('orderinviter_id desc')->paginate(10, false, ['query' => request()->param()]);
        $result = array_merge(array('list' =>  $list->items()), mobile_page($list));
        ds_json_encode(10000, '',$result);
    }

}
