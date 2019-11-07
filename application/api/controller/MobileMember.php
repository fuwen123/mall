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
 * 控制器
 */
class MobileMember extends MobileHome {

    public function _initialize() {
        parent::_initialize();
        $key = request()->header('X-DS-KEY');
        if(!$key){
            $key=input('param.key');//微信支付需要
        }
        if (!empty($key)) {
            $mbusertoken_model = model('mbusertoken');
            $mb_user_token_info = $mbusertoken_model->getMbusertokenInfoByToken($key);
            if (empty($mb_user_token_info)) {
                ds_json_encode(11001, lang('please_login'));
            }
            $member_model = model('member');
            $this->member_info = $member_model->getMemberInfoByID($mb_user_token_info['member_id']);

            if (empty($this->member_info)) {
                ds_json_encode(11001, lang('please_login'));
            } else {
              if (!$this->member_info['member_state']) {
                    ds_json_encode(11001, lang('please_login'));
                }
                $this->member_info['member_clienttype'] = $mb_user_token_info['member_clienttype'];
                $this->member_info['member_openid'] = $mb_user_token_info['member_openid'];
                $this->member_info['member_token'] = $mb_user_token_info['member_token'];
                $level_name = $member_model->getOneMemberGrade($mb_user_token_info['member_id']);
                $this->member_info['level_name'] = $level_name['level_name'];
                //读取卖家信息
                $seller_info = model('seller')->getSellerInfo(array('member_id' => $this->member_info['member_id']));
                $this->member_info['store_id'] = $seller_info['store_id'];
                //考虑到模型中session
                if (session('member_id') != $this->member_info['member_id']) {
                    //避免重复查询数据库
                    $member_model->createSession(array_merge($this->member_info, $level_name));
                }
            }
        }else{
            ds_json_encode(11001, lang('please_login'));
        }
    }

    public function getOpenId() {
        return $this->member_info['member_openid'];
    }

    public function setOpenId($openId) {
        $this->member_info['member_openid'] = $openId;
        model('mbusertoken')->editMemberOpenId($this->member_info['member_token'], $openId);
    }

}

?>
