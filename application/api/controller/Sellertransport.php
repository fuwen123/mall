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
 * 卖家售卖地区控制器
 */
class Sellertransport extends MobileSeller {

    public function _initialize() {
        parent::_initialize();
    }

    public function index() {
        $this->transport_list();
    }

    /**
     * 返回商家店铺商品分类列表
     */
    public function transport_list() {
        $transport_model = model('transport');
        $transport_list = $transport_model->getTransportList(array('store_id' => $this->store_info['store_id']));
        ds_json_encode(10000, '',array('transport_list' => $transport_list));
    }

}

?>
