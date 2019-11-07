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
 * 聊天控制器
 */
class Memberchat extends MobileMember {

    public function _initialize() {
        parent::_initialize();
        Lang::load(APP_PATH . 'home/lang/'.config('default_lang').'/memberchat.lang.php');
    }

    /**
     * @api {POST} api/Memberchat/get_node_info node连接参数
     * @apiVersion 1.0.0
     * @apiGroup Memberchat
     *
     * @apiHeader {String} X-DS-KEY 用户授权token
     *
     * @apiParam {Int} u_id 用户ID
     * @apiParam {Int} chat_goods_id 商品ID
     *
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     * @apiSuccess {Object} result.chat_goods  返回数据
     * @apiSuccess {Int} result.chat_goods.areaid_1 一级地区id
     * @apiSuccess {Int} result.chat_goods.areaid_2 二级地区id
     * @apiSuccess {Int} result.chat_goods.brand_id 品牌ID
     * @apiSuccess {Int} result.chat_goods.color_id 颜色规格值ID
     * @apiSuccess {Int} result.chat_goods.evaluation_count 评论数
     * @apiSuccess {Int} result.chat_goods.evaluation_good_star 好评评分
     * @apiSuccess {Int} result.chat_goods.gc_id 分类ID
     * @apiSuccess {Int} result.chat_goods.gc_id_1 一级分类ID
     * @apiSuccess {Int} result.chat_goods.gc_id_2 二级分类ID
     * @apiSuccess {Int} result.chat_goods.gc_id_3 三级分类ID
     * @apiSuccess {String} result.chat_goods.goods_addtime 添加时间，Unix时间戳
     * @apiSuccess {String} result.chat_goods.goods_advword 广告词
     * @apiSuccess {Int} result.chat_goods.goods_click 商品点击次数
     * @apiSuccess {Int} result.chat_goods.goods_collect 收藏数
     * @apiSuccess {Int} result.chat_goods.goods_commend 商品推荐 1:是 0:否
     * @apiSuccess {Int} result.chat_goods.goods_commonid 商品公共ID
     * @apiSuccess {Int} result.chat_goods.goods_edittime 编辑时间，Unix时间戳
     * @apiSuccess {Float} result.chat_goods.goods_freight 运费
     * @apiSuccess {Int} result.chat_goods.goods_id 商品ID
     * @apiSuccess {String} result.chat_goods.goods_image 商品图片名称
     * @apiSuccess {Float} result.chat_goods.goods_marketprice 商品市场价
     * @apiSuccess {String} result.chat_goods.goods_mgdiscount 会员等级折扣
     * @apiSuccess {String} result.chat_goods.goods_name 商品名称
     * @apiSuccess {Float} result.chat_goods.goods_price 商品价格
     * @apiSuccess {Float} result.chat_goods.goods_promotion_price 商品促销价
     * @apiSuccess {String} result.chat_goods.goods_promotion_type 商品促销类型
     * @apiSuccess {Int} result.chat_goods.goods_salenum 销售量
     * @apiSuccess {String} result.chat_goods.goods_serial 货号
     * @apiSuccess {String} result.chat_goods.goods_spec 商品规格序列化
     * @apiSuccess {String} result.chat_goods.goods_state 商品状态 0:下架 1:正常 10:违规（禁售）
     * @apiSuccess {String} result.chat_goods.goods_stcids 店铺分类id 首尾用,隔开
     * @apiSuccess {Int} result.chat_goods.goods_storage 库存
     * @apiSuccess {Int} result.chat_goods.goods_storage_alarm 预警库存
     * @apiSuccess {Int} result.chat_goods.goods_vat 是否开具增值税发票 1:是 0:否
     * @apiSuccess {Object} result.chat_goods.goods_verify 已审核 1是0否
     * @apiSuccess {Int} result.chat_goods.is_appoint 是否是预约商品 1:是 0:否
     * @apiSuccess {Int} result.chat_goods.is_goodsfcode 是否是F码商品 1:是 0:否
     * @apiSuccess {Int} result.chat_goods.is_have_gift 是否含赠品 1:是 0:否
     * @apiSuccess {Int} result.chat_goods.is_platform_store 是否自营商品 1:是 0:否
     * @apiSuccess {Int} result.chat_goods.is_presell 是否是预售商品 1:是 0:否
     * @apiSuccess {Int} result.chat_goods.is_virtual 是否是虚拟商品 1:是 0:否
     * @apiSuccess {String} result.chat_goods.pic 商品图片完整路径
     * @apiSuccess {String} result.chat_goods.pic24 商品小图完整路径
     * @apiSuccess {Int} result.chat_goods.region_id 地区ID
     * @apiSuccess {Int} result.chat_goods.store_id 店铺ID
     * @apiSuccess {String} result.chat_goods.store_name 店铺名称
     * @apiSuccess {Int} result.chat_goods.transport_id 商品售卖区域ID
     * @apiSuccess {Int} result.chat_goods.virtual_indate 虚拟商品有效期
     * @apiSuccess {Int} result.chat_goods.virtual_invalid_refund 是否允许过期退款 1:是 0:否
     * @apiSuccess {Int} result.chat_goods.virtual_limit 虚拟商品购买上限
     * @apiSuccess {Object} result.member_info  发送聊天人用户信息
     * @apiSuccess {Int} result.member_info.grade_id  等级ID
     * @apiSuccess {String} result.member_info.member_avatar  会员头像
     * @apiSuccess {Int} result.member_info.member_id  用户ID
     * @apiSuccess {String} result.member_info.member_name  用户名称
     * @apiSuccess {String} result.member_info.store_avatar  店铺头像
     * @apiSuccess {Int} result.member_info.store_id  店铺ID
     * @apiSuccess {String} result.member_info.store_name  店铺名称
     * @apiSuccess {Int} result.node_chat  开启聊天 0否1是
     * @apiSuccess {String} result.node_site_url  聊天接口地址
     * @apiSuccess {Object} result.user_info  接收聊天人用户信息
     * @apiSuccess {Int} result.user_info.grade_id  等级ID
     * @apiSuccess {String} result.user_info.member_avatar  会员头像
     * @apiSuccess {Int} result.user_info.member_id  用户ID
     * @apiSuccess {String} result.user_info.member_name  用户名称
     * @apiSuccess {String} result.user_info.store_avatar  店铺头像
     * @apiSuccess {Int} result.user_info.store_id  店铺ID
     * @apiSuccess {String} result.user_info.store_name  店铺名称
     * @apiSuccess {Object} result.node_chat  开启聊天 0否1是
     * @apiSuccess {Object} result.node_site_url  聊天接口地址
     */
    public function get_node_info() {
        $result = array('node_chat' => config('node_site_use'), 'node_site_url' => config('node_site_url'));
        $webchat_model = model('webchat');
        $member_id = $this->member_info['member_id'];
        $member_info = $webchat_model->getMember($member_id);
        $result['member_info'] = $member_info;
        $u_id = intval(input('param.u_id'));
        if ($u_id > 0) {
            $member_info = $webchat_model->getMember($u_id);
            $result['user_info'] = $member_info;
        }
        $goods_id = intval(input('param.chat_goods_id'));
        if ($goods_id > 0) {
            $goods = $webchat_model->getGoodsInfo($goods_id);
            $result['chat_goods'] = $goods;
        }
        ds_json_encode(10000,'',$result);
    }

    /**
     * @api {POST} api/Memberchat/get_user_list 最近联系人
     * @apiVersion 1.0.0
     * @apiGroup Memberchat
     *
     * @apiHeader {String} X-DS-KEY 用户授权token
     *
     * @apiParam {Int} n 显示数量
     * @apiParam {Int} recent 只显示最近消息的用户 1是
     *
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     * @apiSuccess {Object} result.node_info  聊天接口信息
     * @apiSuccess {Int} result.node_info.node_chat  开启聊天 0否1是
     * @apiSuccess {String} result.node_info.node_site_url  聊天接口地址
     * @apiSuccess {Object} result.member_info  发送聊天人用户信息
     * @apiSuccess {Int} result.member_info.grade_id  等级ID
     * @apiSuccess {String} result.member_info.member_avatar  会员头像
     * @apiSuccess {Int} result.member_info.member_id  用户ID
     * @apiSuccess {String} result.member_info.member_name  用户名称
     * @apiSuccess {String} result.member_info.store_avatar  店铺头像
     * @apiSuccess {Int} result.member_info.store_id  店铺ID
     * @apiSuccess {String} result.member_info.store_name  店铺名称
     * @apiSuccess {Object} result.list  最近联系人列表，键为用户ID
     * @apiSuccess {String} result.list.avatar  用户头像
     * @apiSuccess {Int} result.list.r_state  是否已读 1为已读,2为未读
     * @apiSuccess {Int} result.list.recent  是否最近 0否1是
     * @apiSuccess {String} result.list.time  联系时间描述
     * @apiSuccess {Int} result.list.u_id  用户ID
     * @apiSuccess {String} result.list.u_name  用户名称
     */
    public function get_user_list() {
        $member_list = array();
        $webchat_model = model('webchat');

        $member_id = $this->member_info['member_id'];
        $member_name = $this->member_info['member_name'];
        $n = intval(input('post.key'));
        if ($n < 1)
            $n = 50;
        if (intval(input('post.recent')) != 1) {
            $member_list = $webchat_model->getFriendList(array('friend_frommid' => $member_id), $n, $member_list);
        }
        $chat_add_time = date("Y-m-d");
        $chat_add_time30 = strtotime($chat_add_time) - 60 * 60 * 24 * 30;
        $member_list = $webchat_model->getRecentList(array('f_id' => $member_id, 'chatmsg_addtime' => array('egt', $chat_add_time30)), 10, $member_list);
        $member_list = $webchat_model->getRecentFromList(array('t_id' => $member_id, 'chatmsg_addtime' => array('egt', $chat_add_time30)), 10, $member_list);
        $member_info = array();
        $member_info = $webchat_model->getMember($member_id);
        $node_info = array();
        $node_info['node_chat'] = config('node_chat');
        $node_info['node_site_url'] = config('node_site_url');
        ds_json_encode(10000,'',array('node_info' => $node_info, 'member_info' => $member_info, 'list' => $member_list));
    }

    /**
     * 会员信息
     *
     */
    public function get_info() {
        $val = '';
        $member = array();
        $webchat_model = model('webchat');
        $types = array('member_id', 'member_name', 'store_id', 'member');
        $key = input('post.t');
        $member_id = intval(input('post.u_id'));
        if ($member_id > 0 && trim($key) != '' && in_array($key, $types)) {
            $member_info = $webchat_model->getMember($member_id);
            ds_json_encode(10000,'',array('member_info' => $member_info));
        } else {
            ds_json_encode(10001,lang('param_error'));
        }
    }

    /**
     * @api {POST} api/Memberchat/send_msg 发消息
     * @apiVersion 1.0.0
     * @apiGroup Memberchat
     *
     * @apiHeader {String} X-DS-KEY 用户授权token
     *
     * @apiParam {Int} t_id 用户ID
     * @apiParam {Int} chat_goods_id 商品ID
     * @apiParam {String} t_name 用户名称
     * @apiParam {String} t_msg 消息内容
     *
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     * @apiSuccess {Object} result.msg  消息
     * @apiSuccess {String} result.msg.add_time  发送时间
     * @apiSuccess {Int} result.msg.chatlog_addtime  发送时间，Unix时间戳
     * @apiSuccess {Int} result.msg.f_id  发送消息人用户ID
     * @apiSuccess {String} result.msg.f_ip  发送IP
     * @apiSuccess {String} result.msg.f_name  发送消息人用户名称
     * @apiSuccess {Int} result.msg.goods_id  商品ID
     * @apiSuccess {String} result.msg.goods_info  商品信息
     * @apiSuccess {Int} result.msg.m_id  消息ID
     * @apiSuccess {Int} result.msg.t_id  接收消息人用户ID
     * @apiSuccess {String} result.msg.t_msg  消息内容
     * @apiSuccess {String} result.msg.t_name  接收消息人用户名称
     */
    public function send_msg() {
        $member = array();
        $webchat_model = model('webchat');
        $member_id = $this->member_info['member_id'];
        $member_name = $this->member_info['member_name'];
        $t_id = intval(input('post.t_id'));
        $t_name = trim(input('post.t_name'));
        $member = $webchat_model->getMember($t_id);
        if ($t_name != $member['member_name'])
            ds_json_encode(10001,lang('member_name_error'));

        $msg = array();
        $msg['f_id'] = $member_id;
        $msg['f_name'] = $member_name;
        $msg['t_id'] = $t_id;
        $msg['t_name'] = $t_name;
        $msg['t_msg'] = trim(input('post.t_msg'));
        if ($msg['t_msg'] != '')
            $chat_msg = $webchat_model->addChatmsg($msg);
        if ($chat_msg['m_id']) {
            $goods_id = intval(input('post.chat_goods_id'));
            if ($goods_id > 0) {
                $goods = $webchat_model->getGoodsInfo($goods_id);
                $chat_msg['chat_goods'] = $goods;
            }
            $chat_msg['t_msg']= htmlspecialchars_decode($chat_msg['t_msg']);
            ds_json_encode(10000,'',array('msg' => $chat_msg));
        } else {
            ds_json_encode(10001,lang('send_fail'));
        }
    }

    /**
     * 删除最近联系人消息
     *
     */
    public function del_msg() {
        $webchat_model = model('webchat');
        $member_id = $this->member_info['member_id'];
        $t_id = intval(input('param.t_id'));
        $condition = array();
        $condition['f_id'] = $member_id;
        $condition['t_id'] = $t_id;
        $webchat_model->delChatmsg($condition);
        $condition = array();
        $condition['t_id'] = $member_id;
        $condition['f_id'] = $t_id;
        $webchat_model->delChatmsg($condition);
        ds_json_encode(10000,'',1);
    }

    /**
     * 商品图片和名称
     *
     */
    public function get_goods_info() {
        $webchat_model = model('webchat');
        $goods_id = intval(input('post.goods_id'));
        $goods = $webchat_model->getGoodsInfo($goods_id);
        if (empty($goods)) {
            ds_json_encode(10001,lang('goods_not_exist'));
        }
        ds_json_encode(10000,'',array('goods' => $goods));
    }

    /**
     * 未读消息查询
     *
     */
    public function get_msg_count() {
        $webchat_model = model('webchat');
        $member_id = $this->member_info['member_id'];
        $condition = array();
        $condition['t_id'] = $member_id;
        $condition['r_state'] = 2;
        $n = $webchat_model->getChatmsgCount($condition);
        ds_json_encode(10000,'',$n);
    }

    /**
     * @api {POST} api/Memberchat/get_chat_log 聊天记录查询
     * @apiVersion 1.0.0
     * @apiGroup Memberchat
     *
     * @apiHeader {String} X-DS-KEY 用户授权token
     *
     * @apiParam {Int} t_id 用户ID
     * @apiParam {Int} page 页码
     * @apiParam {Int} pagesize 每页显示数量
     *
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     * @apiSuccess {Object[]} result.list  消息
     * @apiSuccess {String} result.list.add_time  发送时间
     * @apiSuccess {Int} result.list.chatlog_addtime  发送时间，Unix时间戳
     * @apiSuccess {Int} result.list.f_id  发送消息人用户ID
     * @apiSuccess {String} result.list.f_ip  发送IP
     * @apiSuccess {String} result.list.f_name  发送消息人用户名称
     * @apiSuccess {Int} result.list.goods_id  商品ID
     * @apiSuccess {Object} result.list.goods_info  商品信息
     * @apiSuccess {Int} result.list.m_id  消息ID
     * @apiSuccess {Int} result.list.t_id  接收消息人用户ID
     * @apiSuccess {String} result.list.t_msg  消息内容
     * @apiSuccess {String} result.list.t_name  接收消息人用户名称
     * @apiSuccess {Int} result.page_total  总页数
     * @apiSuccess {Boolean} result.hasmore  是否有更多 true是false否
     */
    public function get_chat_log() {
        $member_id = $this->member_info['member_id'];
        $t_id = intval(input('post.t_id'));
        $add_time_to = date("Y-m-d");
        $time_from = array();
        $time_from['7'] = strtotime($add_time_to) - 60 * 60 * 24 * 7;
        $time_from['15'] = strtotime($add_time_to) - 60 * 60 * 24 * 15;
        $time_from['30'] = strtotime($add_time_to) - 60 * 60 * 24 * 30;

        $key = input('post.t');
//        if (trim($key) != '' && array_key_exists($key, $time_from)) {
            $webchat_model = model('webchat');
            $list = array();
            $condition_sql='1=1';
//            $condition_sql = " chatlog_addtime >= '" . $time_from[$key] . "' ";
            $condition_sql .= " and ((f_id = '" . $member_id . "' and t_id = '" . $t_id . "') or (f_id = '" . $t_id . "' and t_id = '" . $member_id . "'))";
            $list = $webchat_model->getChatlogList($condition_sql, $this->pagesize);
            foreach($list as $key => $val){
                $list[$key]['t_msg']=htmlspecialchars_decode($val['t_msg']);
            }
            $result = array_merge(array('list' => $list), mobile_page($webchat_model->page_info));
            ds_json_encode(10000,'',$result);
            
//        }
    }


}

?>
