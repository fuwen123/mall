<?php
/**
 * 拼团管理
 */
namespace app\admin\controller;
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
 * 控制器
 */
class Promotionpintuan extends AdminControl
{
    public function _initialize()
    {
        parent::_initialize();
        Lang::load(APP_PATH . 'admin/lang/'.config('default_lang').'/promotionpintuan.lang.php');
    }
    /**
     * 拼团列表
     */
    public function index()
    {
        $pintuan_model = model('ppintuan');
        $condition = array();
        if (!empty(input('param.pintuan_name'))) {
            $condition['pintuan_name'] = array('like', '%' . input('param.pintuan_name') . '%');
        }
        if (!empty(input('param.store_name'))) {
            $condition['store_name'] = array('like', '%' . input('param.store_name') . '%');
        }
        if (!empty(input('param.state'))) {
            $condition['pintuan_state'] = intval(input('param.state'));
        }
        $pintuan_list = $pintuan_model->getPintuanList($condition, 10, 'pintuan_state desc, pintuan_end_time desc');
        $this->assign('pintuan_list', $pintuan_list);
        $this->assign('show_page', $pintuan_model->page_info->render());
        $this->assign('pintuan_state_array', $pintuan_model->getPintuanStateArray());
        
        $this->assign('filtered', $condition ? 1 : 0); //是否有查询条件
        
        $this->setAdminCurItem('pintuan_list');
        return $this->fetch();
    }
    /**
     * 拼团详情
     */
    public function pintuan_manage()
    {
        $ppintuangroup_model = model('ppintuangroup');
        $ppintuanorder_model = model('ppintuanorder');
        $pintuan_id = intval(input('param.pintuan_id'));
        $condition = array();
        $condition['pintuan_id'] = $pintuan_id;
        $condition['pintuangroup_state'] = intval(input('param.pintuangroup_state'));
        
        $ppintuangroup_list = $ppintuangroup_model->getPpintuangroupList($condition, 10); #获取开团信息
        foreach ($ppintuangroup_list as $key => $ppintuangroup) {
            //获取开团订单下的参团订单
            $condition = array();
            $condition['pintuangroup_id'] = $ppintuangroup['pintuangroup_id'];
            if($ppintuangroup['pintuangroup_is_virtual']){
                $ppintuangroup_list[$key]['order_list'] = $ppintuanorder_model->getPpintuanvrorderList($condition);
            }else{
                $ppintuangroup_list[$key]['order_list'] = $ppintuanorder_model->getPpintuanorderList($condition);
            }
        }
        $this->assign('show_page', $ppintuangroup_model->page_info->render());
        $this->assign('pintuangroup_list', $ppintuangroup_list);
        $this->assign('pintuangroup_state_array', $ppintuangroup_model->getPintuangroupStateArray());
        $this->assign('filtered', $condition ? 1 : 0); //是否有查询条件
        $this->setAdminCurItem('pintuan_manage');
        return $this->fetch();
    }
    
    /**
     * 拼团活动 提前结束
     */
    public function pintuan_end() {
        $pintuan_id = intval(input('param.pintuan_id'));
        $ppintuan_model = model('ppintuan');

        $pintuan_info = $ppintuan_model->getPintuanInfoByID($pintuan_id);
        if (!$pintuan_info) {
            ds_json_encode(10001, lang('param_error'));
        }

        /**
         * 指定拼团活动结束
         */
        $result = $ppintuan_model->endPintuan(array('pintuan_id' => $pintuan_id));

        if ($result) {
            $this->log('拼团活动提前结束，活动名称：' . $pintuan_info['pintuan_name'] . '活动编号：' . $pintuan_id, 1);
            ds_json_encode(10000, lang('ds_common_op_succ'));
        } else {
            ds_json_encode(10001, lang('ds_common_op_fail'));
        }
    }
    
    /**
     * 拼团套餐管理
     */
    public function pintuan_quota()
    {
        $pintuanquota_model = model('ppintuanquota');

        $condition = array();
        $condition['store_name'] = array('like', '%' . input('param.store_name') . '%');
        $pintuanquota_list = $pintuanquota_model->getPintuanquotaList($condition, 10, 'pintuanquota_endtime desc');
        $this->assign('pintuanquota_list', $pintuanquota_list);
        $this->assign('show_page', $pintuanquota_model->page_info->render());

        $this->setAdminCurItem('pintuan_quota');
        return $this->fetch();
    }
    /**
     * 拼团设置
     */
    public function pintuan_setting() {
        if (!(request()->isPost())) {
            $setting = rkcache('config', true);
            $this->assign('setting', $setting);
            return $this->fetch();
        } else {
            $promotion_pintuan_price = intval(input('post.promotion_pintuan_price'));
            if ($promotion_pintuan_price < 0) {
                $this->error(lang('param_error'));
            }

            $config_model = model('config');
            $update_array = array();
            $update_array['promotion_pintuan_price'] = $promotion_pintuan_price;

            $result = $config_model->editConfig($update_array);
            if ($result) {
                $this->log('修改拼团套餐价格为' . $promotion_pintuan_price . '元');
                dsLayerOpenSuccess(lang('setting_save_success'));
            } else {
                $this->error(lang('setting_save_fail'));
            }
        }
    }

    protected function getAdminItemList()
    {
        $menu_array = array(
            array(
                'name' => 'pintuan_list', 'text' => lang('pintuan_list'), 'url' => url('Promotionpintuan/index')
            ), array(
                'name' => 'pintuan_quota', 'text' => lang('pintuan_quota'),
                'url' => url('Promotionpintuan/pintuan_quota')
            ), array(
                'name' => 'pintuan_setting',
                'text' => lang('pintuan_setting'),
                'url' => "javascript:dsLayerOpen('".url('Promotionpintuan/pintuan_setting')."','".lang('pintuan_setting')."')"
            ),
        );
        if (request()->action() == 'pintuan_detail'){
            $menu_array[] = array(
                'name' => 'pintuan_detail', 'text' => lang('pintuan_detail'),
                'url' => url('Promotionpintuan/pintuan_detail')
            );
        }
            
        return $menu_array;
    }
}

