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
class MobileSeller extends MobileHome {

    protected $seller_info = array();
    protected $seller_group_info = array();
    protected $member_info = array();
    protected $store_info = array();
    protected $store_grade = array();

    public function _initialize() {
        parent::_initialize();

        $mbsellertoken_model = model('mbsellertoken');
        $key = request()->header('X-DS-KEY');
        if (empty($key)) {
            ds_json_encode(13001,lang('please_login'), array('login' => '0'));
        }

        $mb_seller_token_info = $mbsellertoken_model->getMbsellertokenInfoByToken($key);
        if (empty($mb_seller_token_info)) {
            ds_json_encode(13001,lang('please_login'), array('login' => '0'));
        }

        $seller_model = model('seller');
        $member_model = model('member');
        $store_model = model('store');
        $sellergroup_model = model('sellergroup');

        $this->seller_info = $seller_model->getSellerInfo(array('seller_id' => $mb_seller_token_info['seller_id']));
        $this->member_info = $member_model->getMemberInfoByID($this->seller_info['member_id']);
        $this->store_info = $store_model->getStoreInfoByID($this->seller_info['store_id']);
        $this->seller_group_info = $sellergroup_model->getSellergroupInfo(array('sellergroup_id' => $this->seller_info['sellergroup_id']));
        if(!is_array($this->seller_group_info)){
            $this->seller_group_info=array();
        }
        // 店铺等级
        if (intval($this->store_info['is_platform_store']) === 1) {
            $this->store_grade = array(
                'storegrade_id' => '0',
                'storegrade_name' => lang('store_grade_ownshop'),
                'storegrade_goods_limit' => '0',
                'storegrade_album_limit' => '0',
                'storegrade_space_limit' => '999999999',
                'storegrade_template_number' => '6',
                'storegrade_price' => '0.00',
                'storegrade_description' => '',
                'storegrade_function' => 'editor_multimedia',
                'storegrade_sort' => '255',
            );
        } else {
            $store_grade = rkcache('storegrade', true);
            $this->store_grade = $store_grade[$this->store_info['grade_id']];
        }

        if (empty($this->member_info)) {
            ds_json_encode(13001,lang('please_login'), array('login' => '0'));
        } else {
            $this->seller_info['seller_clienttype'] = $mb_seller_token_info['seller_clienttype'];
        }
        $seller_model->createSellerSession($this->member_info,$this->store_info,$this->seller_info, $this->seller_group_info);
    }
    /**
     * 记录卖家日志
     *
     * @param $content 日志内容
     * @param $state 1成功 0失败
     */
    protected function recordSellerlog($content = '', $state = 1) {
        $seller_info = array();
        $seller_info['sellerlog_content'] = $content;
        $seller_info['sellerlog_time'] = TIMESTAMP;
        $seller_info['sellerlog_seller_id'] =$this->seller_info['seller_id'];
        $seller_info['sellerlog_seller_name'] = $this->seller_info['seller_name'];
        $seller_info['sellerlog_store_id'] = $this->store_info['store_id'];
        $seller_info['sellerlog_seller_ip'] = request()->ip();
        $seller_info['sellerlog_url'] = request()->module() . '/' . request()->controller() . '/' . request()->action();
        $seller_info['sellerlog_state'] = $state;
        $sellerlog_model = model('sellerlog');
        $sellerlog_model->addSellerlog($seller_info);
    }
    
    /**
     * 记录店铺费用
     *
     * @param $storecost_price 费用金额
     * @param $storecost_remark 费用备注
     */
    protected function recordStorecost($storecost_price, $storecost_remark) {
        // 平台店铺不记录店铺费用
        if ($this->store_info['is_platform_store']) {
            return false;
        }
        $storecost_model = model('storecost');
        $param = array();
        $param['storecost_store_id'] = $this->store_info['store_id'];
        $param['storecost_seller_id'] = $this->seller_info['seller_id'];
        $param['storecost_price'] = $storecost_price;
        $param['storecost_remark'] = $storecost_remark;
        $param['storecost_state'] = 0;
        $param['storecost_time'] = TIMESTAMP;
        $storecost_model->addStorecost($param);

        // 发送店铺消息
        $param = array();
        $param['code'] = 'store_cost';
        $param['store_id'] = $this->store_info['store_id'];
        $param['param'] = array(
            'price' => $storecost_price,
            'seller_name' => $this->seller_info['seller_name'],
            'remark' => $storecost_remark
        );

        \mall\queue\QueueClient::push('sendStoremsg', $param);
    }
}

?>
