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
 * 浏览历史控制器
 */
class Membergoodsbrowse extends MobileMember {

    public function _initialize() {
        parent::_initialize(); // TODO: Change the autogenerated stub
        Lang::load(APP_PATH . 'home/lang/'.config('default_lang').'/membergoodsbrowse.lang.php');
    }

    /**
     * 我的足迹
     */
    public function browse_list() {
        $goodsbrowse_model = model('goodsbrowse');
        //查询浏览记录
        $where = array();
        $where['member_id'] = $this->member_info['member_id'];
        $browselist = $goodsbrowse_model->getViewedGoodsList($this->member_info['member_id'], $this->pagesize);
        $goodsid_arr = array();
        foreach ((array) $browselist as $k => $v) {
            $goodsid_arr[] = $v['goods_id'];
        }
        //查询商品信息
        $browselist_new = array();
        if ($goodsid_arr) {
            $goods_list_tmp = model('goods')->getGoodsList(array(
                'goods_id' => array(
                    'in', $goodsid_arr
                )), 'goods_id, goods_name, goods_promotion_price,goods_promotion_type, goods_marketprice, goods_image, store_id, gc_id, gc_id_1, gc_id_2, gc_id_3');
            $goods_list = array();
            foreach ((array) $goods_list_tmp as $v) {
                $goods_list[$v['goods_id']] = $v;
            }
            foreach ($browselist as $k => $v) {
                if ($goods_list[$v['goods_id']]) {
                    $tmp = array();
                    $tmp = $goods_list[$v['goods_id']];
                    $tmp["goodsbrowse_time"] = $v['goodsbrowse_time'];
                    $tmp["goods_image_url"] = goods_cthumb($goods_list[$v['goods_id']]['goods_image'], 480, $goods_list[$v['goods_id']]['store_id']);
                    if (date('Y-m-d', $v['goodsbrowse_time']) == date('Y-m-d', time())) {
                        $tmp['browsetime_day'] = lang('today');
                    } elseif (date('Y-m-d', $v['goodsbrowse_time']) == date('Y-m-d', (time() - 86400))) {
                        $tmp['browsetime_day'] = lang('yesterday');
                    } else {
                        $tmp['browsetime_day'] = date('Y年m月d日', $v['goodsbrowse_time']);
                    }
                    $tmp['browsetime_text'] = $tmp['browsetime_day'] . date('H:i', $v['goodsbrowse_time']);
                    $browselist_new[] = $tmp;
                }
            }
        }
        $result = array_merge(array('goodsbrowse_list' => $browselist_new), mobile_page($goodsbrowse_model->page_info));
        ds_json_encode(10000, '',$result);
    }

    /**
     * 清空足迹
     */
    public function browse_clearall() {
        //清除缓存中浏览记录
        dcache($this->member_info['member_id'], 'goodsbrowse');
        model('goodsbrowse')->delGoodsbrowse(array('member_id' => $this->member_info['member_id']));
        ds_json_encode(10000, '',1);
    }

}