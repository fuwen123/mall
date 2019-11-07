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
 * 用户控制器
 */
class Member extends MobileMember {

    public function _initialize() {
        parent::_initialize();
    }

    /**
     * @api {POST} api/Member/index 用户首页基本信息显示
     * @apiVersion 1.0.0
     * @apiGroup Member
     *
     * @apiHeader {String} X-DS-KEY 用户授权token
     *
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     * @apiSuccess {Object} result.member_info  会员信息
     * @apiSuccess {String} result.member_info.available_predeposit  可用预存款余额
     * @apiSuccess {String} result.member_info.available_rc_balance  可用充值卡余额
     * @apiSuccess {String} result.member_info.exppoints  当前等级所需经验值
     * @apiSuccess {String} result.member_info.freeze_predeposit  冻结预存款余额
     * @apiSuccess {String} result.member_info.freeze_rc_balance  冻结充值卡余额
     * @apiSuccess {String} result.member_info.inform_allow  是否允许举报(1可以/2不可以)
     * @apiSuccess {String} result.member_info.inviter_id  推荐人ID
     * @apiSuccess {String} result.member_info.inviter_state  推广员状态（0审核中1已审核2已清退）
     * @apiSuccess {String} result.member_info.is_allowtalk  会员是否有咨询和发送站内信的权限 1为开启 0为关闭
     * @apiSuccess {String} result.member_info.is_buylimit  会员是否有购买权限 1为开启 0为关闭
     * @apiSuccess {String} result.member_info.level  会员等级
     * @apiSuccess {String} result.member_info.level_name  等级名称
     * @apiSuccess {String} result.member_info.member_addtime  添加时间，Unix时间戳
     * @apiSuccess {String} result.member_info.member_areaid  地区ID
     * @apiSuccess {String} result.member_info.member_areainfo  地区信息
     * @apiSuccess {String} result.member_info.member_avatar  用户头像
     * @apiSuccess {String} result.member_info.member_birthday  生日
     * @apiSuccess {String} result.member_info.member_cityid  城市ID
     * @apiSuccess {String} result.member_info.member_email  邮箱
     * @apiSuccess {String} result.member_info.member_emailbind  已绑定邮箱 0否1是
     * @apiSuccess {String} result.member_info.member_exppoints  会员经验值
     * @apiSuccess {String} result.member_info.member_id  用户ID
     * @apiSuccess {String} result.member_info.member_login_ip  本次登录IP
     * @apiSuccess {String} result.member_info.member_loginnum  本次登录次数
     * @apiSuccess {String} result.member_info.member_logintime  本次登录时间，Unix时间戳
     * @apiSuccess {String} result.member_info.member_mobile  手机号
     * @apiSuccess {String} result.member_info.member_mobilebind  已绑定手机 0否1是
     * @apiSuccess {String} result.member_info.member_name  用户名称
     * @apiSuccess {String} result.member_info.member_old_login_ip  上次登录IP
     * @apiSuccess {String} result.member_info.member_old_logintime  上次登录时间，Unix时间戳
     * @apiSuccess {String} result.member_info.member_points  用户积分
     * @apiSuccess {String} result.member_info.member_privacy  隐私设定
     * @apiSuccess {String} result.member_info.member_provinceid  省份ID
     * @apiSuccess {String} result.member_info.member_qq  用户QQ
     * @apiSuccess {String} result.member_info.member_qqinfo  qq快捷登录信息
     * @apiSuccess {String} result.member_info.member_qqopenid  qq openid
     * @apiSuccess {String} result.member_info.member_sex  会员性别 0保密1男2女3保密
     * @apiSuccess {String} result.member_info.member_sinainfo  新浪快捷登录信息
     * @apiSuccess {String} result.member_info.member_sinaopenid  新浪openid
     * @apiSuccess {String} result.member_info.member_state  会员的开启状态 1为开启 0为关闭
     * @apiSuccess {String} result.member_info.member_truename  会员真实姓名
     * @apiSuccess {String} result.member_info.member_ww  用户旺旺
     * @apiSuccess {String} result.member_info.member_wxinfo  微信快捷登录信息
     * @apiSuccess {String} result.member_info.member_wxopenid  微信openid
     * @apiSuccess {String} result.member_info.member_wxunionid  微信unionid
     * @apiSuccess {String} result.member_info.order_noeval_count  待评论订单数
     * @apiSuccess {String} result.member_info.order_nopay_count  待付款订单数
     * @apiSuccess {String} result.member_info.order_noreceipt_count  待收货订单数
     * @apiSuccess {String} result.member_info.order_noship_count  待发货订单数
     * @apiSuccess {String} result.member_info.order_refund_count  退款中订单数
     * @apiSuccess {String} result.member_info.store_id  店铺ID
     * @apiSuccess {String} result.member_info.voucher_count  可用优惠券数
     * @apiSuccess {String} result.member_info.member_signin_time  最后一次签到时间
     * @apiSuccess {String} result.member_info.member_signin_days_cycle  持续签到天数，每周期后清零
     * @apiSuccess {String} result.member_info.member_signin_days_total  签到总天数
     * @apiSuccess {String} result.member_info.member_signin_days_series  持续签到天数总数，非连续周期清零
     */
    public function index() {
        $member_model = model('member');
        $member_info = $member_model->getMemberInfoByID($this->member_info['member_id']);
        
        unset($member_info['member_password']);
        unset($member_info['member_paypwd']);
        
        if ($member_info) {
            $member_gradeinfo = $member_model->getOneMemberGrade(intval($member_info['member_exppoints']));
            $member_info = array_merge($member_info, $member_gradeinfo);
            //代金券数量
            $member_info['voucher_count'] = model('voucher')->getCurrentAvailableVoucherCount($this->member_info['member_id']);
            $member_info['member_avatar'] = get_member_avatar_for_id($this->member_info['member_id']);
            $member_info['member_idcard_image1_url'] = get_member_idcard_image($member_info['member_idcard_image1']);
            $member_info['member_idcard_image2_url'] = get_member_idcard_image($member_info['member_idcard_image2']);
            $member_info['member_idcard_image3_url'] = get_member_idcard_image($member_info['member_idcard_image3']);
        }
        
        //获取用户是否有推广权限
        if (config('inviter_open')) {
            //查看是否已是分销会员
            $inviter_model = model('inviter');
            $inviter_info = $inviter_model->getInviterInfo('i.inviter_id=' . $this->member_info['member_id']);
            if(!empty($inviter_info)){
                $member_info['inviter_state'] = $inviter_info['inviter_state']; // 是否是分销员
            }
        }
        
        //判断此用户是否有店铺
        $seller_info = model('seller')->getSellerInfo(array('member_id' => $this->member_info['member_id']));
        if ($seller_info) {
            $member_info['store_id'] = $seller_info['store_id'];
        } else {
            $member_info['store_id'] = 0;
        }

        // 交易提醒
        $order_model = model('order');
        $refundreturn_model = model('refundreturn');
        $member_info['order_nopay_count'] = $order_model->getOrderCountByID('buyer', $this->member_info['member_id'], 'NewCount');
        $member_info['order_noreceipt_count'] = $order_model->getOrderCountByID('buyer', $this->member_info['member_id'], 'SendCount');
        $member_info['order_noeval_count'] = $order_model->getOrderCountByID('buyer', $this->member_info['member_id'], 'EvalCount');
        $member_info['order_noship_count'] = $order_model->getOrderCountByID('buyer', $this->member_info['member_id'], 'PayCount');
        $member_info['order_refund_count'] = $refundreturn_model->getRefundreturnCount(array('buyer_id' => $this->member_info['member_id'], 'refund_state' => array('<>', 3)));
        
        ds_json_encode(10000, '', array('member_info' => $member_info));
    }

    public function my_asset() {
        $fields_arr = array('point', 'predepoit', 'available_rc_balance', 'voucher');
        $fields_str = trim(input('fields'));
        if ($fields_str) {
            $fields_arr = explode(',', $fields_str);
        }
        $member_info = array();
        if (in_array('point', $fields_arr)) {
            $member_info['point'] = $this->member_info['member_points'];
        }
        if (in_array('predepoit', $fields_arr)) {
            $member_info['predepoit'] = $this->member_info['available_predeposit'];
        }
        if (in_array('available_rc_balance', $fields_arr)) {
            $member_info['available_rc_balance'] = $this->member_info['available_rc_balance'];
        }
        if (in_array('voucher', $fields_arr)) {
            $member_info['voucher'] = model('voucher')->getCurrentAvailableVoucherCount($this->member_info['member_id']);
        }
        ds_json_encode(10000, '', $member_info);
    }


    /**
     * @api {POST} api/Member/information 用户基本信息显示
     * @apiVersion 1.0.0
     * @apiGroup Member
     *
     * @apiHeader {String} X-DS-KEY 用户授权token
     *
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     * @apiSuccess {Object} result.member_info  会员信息
     * @apiSuccess {String} result.member_info.available_predeposit  可用预存款余额
     * @apiSuccess {String} result.member_info.available_rc_balance  可用充值卡余额
     * @apiSuccess {String} result.member_info.freeze_predeposit  冻结预存款余额
     * @apiSuccess {String} result.member_info.freeze_rc_balance  冻结充值卡余额
     * @apiSuccess {String} result.member_info.inform_allow  是否允许举报(1可以/2不可以)
     * @apiSuccess {String} result.member_info.inviter_id  推荐人ID
     * @apiSuccess {String} result.member_info.is_allowtalk  会员是否有咨询和发送站内信的权限 1为开启 0为关闭
     * @apiSuccess {String} result.member_info.is_buylimit  会员是否有购买权限 1为开启 0为关闭
     * @apiSuccess {String} result.member_info.member_addtime  添加时间，Unix时间戳
     * @apiSuccess {String} result.member_info.member_areaid  地区ID
     * @apiSuccess {String} result.member_info.member_areainfo  地区信息
     * @apiSuccess {String} result.member_info.member_avatar  用户头像
     * @apiSuccess {String} result.member_info.member_birthday  生日
     * @apiSuccess {String} result.member_info.member_cityid  城市ID
     * @apiSuccess {String} result.member_info.member_email  邮箱
     * @apiSuccess {String} result.member_info.member_emailbind  已绑定邮箱 0否1是
     * @apiSuccess {String} result.member_info.member_exppoints  会员经验值
     * @apiSuccess {String} result.member_info.member_id  用户ID
     * @apiSuccess {String} result.member_info.member_login_ip  本次登录IP
     * @apiSuccess {String} result.member_info.member_loginnum  登录次数
     * @apiSuccess {String} result.member_info.member_logintime  本次登录时间，Unix时间戳
     * @apiSuccess {String} result.member_info.member_mobile  手机号
     * @apiSuccess {String} result.member_info.member_mobilebind  已绑定手机 0否1是
     * @apiSuccess {String} result.member_info.member_name  用户名称
     * @apiSuccess {String} result.member_info.member_old_login_ip  上次登录IP
     * @apiSuccess {String} result.member_info.member_old_logintime  上次登录时间，Unix时间戳
     * @apiSuccess {String} result.member_info.member_points  用户积分
     * @apiSuccess {String} result.member_info.member_privacy  隐私设定
     * @apiSuccess {String} result.member_info.member_provinceid  省份ID
     * @apiSuccess {String} result.member_info.member_qq  用户QQ
     * @apiSuccess {String} result.member_info.member_qqinfo  qq快捷登录信息
     * @apiSuccess {String} result.member_info.member_qqopenid  qq openid
     * @apiSuccess {String} result.member_info.member_sex  会员性别 0保密1男2女3保密
     * @apiSuccess {String} result.member_info.member_sinainfo  新浪快捷登录信息
     * @apiSuccess {String} result.member_info.member_sinaopenid  新浪openid
     * @apiSuccess {String} result.member_info.member_state  会员的开启状态 1为开启 0为关闭
     * @apiSuccess {String} result.member_info.member_truename  会员真实姓名
     * @apiSuccess {String} result.member_info.member_ww  用户旺旺
     * @apiSuccess {String} result.member_info.member_wxinfo  微信快捷登录信息
     * @apiSuccess {String} result.member_info.member_wxopenid  微信openid
     * @apiSuccess {String} result.member_info.member_wxunionid  微信unionid
     */
    public function information() {
        $member_model = model('member');
        $condition['member_id'] = $this->member_info['member_id'];
        $member_info = $member_model->getMemberInfo($condition);
        $member_info['member_avatar'] = get_member_avatar_for_id($member_info['member_id']);
        $member_info['member_idcard_image1_url'] = get_member_idcard_image($member_info['member_idcard_image1']);
            $member_info['member_idcard_image2_url'] = get_member_idcard_image($member_info['member_idcard_image2']);
            $member_info['member_idcard_image3_url'] = get_member_idcard_image($member_info['member_idcard_image3']);
        unset($member_info['member_password']);
        unset($member_info['member_paypwd']);
        ds_json_encode(10000, '', array('member_info'=>$member_info));
    }

    /**
     * @api {POST} api/Member/edit_information 用户基本信息修改
     * @apiVersion 1.0.0
     * @apiGroup Member
     *
     * @apiHeader {String} X-DS-KEY 用户授权token
     *
     * @apiParam {String} member_qq 会员QQ
     * @apiParam {String} member_ww 会员旺旺
     *
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     */
    public function edit_information() {
        $data = array(
            'member_nickname' => input('param.member_nickname'),
            'member_sex' => input('param.member_sex'),
            'member_qq' => input('param.member_qq'),
            'member_ww' => input('param.member_ww'),
            'member_birthday' => strtotime(input('param.member_birthday')),
        );

        $member_validate = validate('member');
        if (!$member_validate->scene('edit_information')->check($data)) {
            ds_json_encode(10001, $member_validate->getError());
        }

        $member_model = model('member');
        $condition['member_id'] = $this->member_info['member_id'];
        $result = $member_model->editMember($condition, $data);
        if ($result) {
            ds_json_encode(10000, lang('ds_common_op_succ'));
        } else {
            ds_json_encode(10001, lang('ds_common_op_fail'));
        }
    }

    /**
     * @api {POST} api/Member/edit_memberavatar 更新用户头像
     * @apiVersion 1.0.0
     * @apiGroup Member
     *
     * @apiHeader {String} X-DS-KEY 用户授权token
     *
     * @apiParam {File} file 用户头像
     *
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {String} result  用户头像
     */
    public function edit_memberavatar() {
        $file = request()->file('memberavatar');
        $upload_file = BASE_UPLOAD_PATH . DS . ATTACH_AVATAR . DS;
        $avatar_name = 'avatar_' . $this->member_info['member_id'] . '.jpg';
        $info = $file->validate(['ext' => ALLOW_IMG_EXT])->move($upload_file, $avatar_name);
        //生成缩略图
        $image = \think\Image::open($upload_file . '/' . $avatar_name);
        $image->thumb(100, 100, \think\Image::THUMB_CENTER)->save($upload_file . '/' . $avatar_name);
        if ($info) {
            ds_json_encode(10000, '', get_member_avatar_for_id($this->member_info['member_id']));
        } else {
            // 上传失败获取错误信息
            ds_json_encode(10001, $file->getError());
        }
    }
    public function auth()
    {
        $member_model = model('member');

            $member_array = array();
            $member_array['member_auth_state'] = 1;
            $member_array['member_idcard'] = input('post.member_idcard');
            $member_array['member_truename'] = input('post.member_truename');
            $member_validate = validate('member');
                if (!$member_validate->scene('auth')->check($member_array)) {
                    ds_json_encode(10001,$member_validate->getError());
                }
            if(!$this->member_info['member_idcard_image1']){
              ds_json_encode(10001,lang('member_idcard_image1_require'));
            }    
            if(!$this->member_info['member_idcard_image2']){
              ds_json_encode(10001,lang('member_idcard_image2_require'));
            }  
            if(!$this->member_info['member_idcard_image3']){
              ds_json_encode(10001,lang('member_idcard_image3_require'));
            }  
            if(!input('post.if_confirm')){
                ds_json_encode(10000);
            }
            $update = $member_model->editMember(array('member_id' => $this->member_info['member_id'],'member_auth_state'=>array('in',array(0,2))), $member_array);

            $message = $update ? lang('ds_common_op_succ') : lang('ds_common_op_fail');
            
            if($update){
                ds_json_encode(10000,$message);
            }else{
                ds_json_encode(10001,$message);
            }
        

    }
    public function edit_auth() {
        $file_name = input('param.id');
            if (!empty($_FILES[$file_name]['name'])) {

                $res=ds_upload_pic(ATTACH_IDCARD_IMAGE,$file_name);
                if(!$res['code']){
                    ds_json_encode(10001,$res['msg']);
                }

                $member_array=array();
                $member_array[substr($file_name,0,20)] = $res['data']['file_name'];
                $member_model = model('member');
                if(!$member_model->editMember(array('member_id' => $this->member_info['member_id'],'member_auth_state'=>array('in',array(0,2))), $member_array)){
                    ds_json_encode(10001,lang('ds_common_op_fail'));
                }
                ds_json_encode(10000,'',array('file_name'=>$res['data']['file_name'],'file_path'=>get_member_idcard_image($res['data']['file_name'])));
            }
            ds_json_encode(10001,lang('param_error'));
    }
    public function drop_auth(){
        $file_name=input('param.file_name');

        @unlink(BASE_UPLOAD_PATH . DS . ATTACH_IDCARD_IMAGE . DS . $this->member_info[$file_name]);
        $member_array=array();
        $member_array[$file_name] = '';
        $member_model = model('member');
        if(!$member_model->editMember(array('member_id' => $this->member_info['member_id'],'member_auth_state'=>array('in',array(0,2))), $member_array)){
                    ds_json_encode(10001,lang('ds_common_op_fail'));
                }
        ds_json_encode(10000);        
    }
}

?>
