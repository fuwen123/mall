<?php

namespace app\common\model;


use think\Model;

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
 * 数据层模型
 */
class Storewaybill extends Model
{
    const STOREWAYBILL_DEFAULT = 1;
    const STOREWAYBILL_UNDEFAULT = 0;

    protected static function init()
    {
        parent::init(); // TODO: Change the autogenerated stub
    }
  
    /**
     * 读取列表
     * @access public
     * @author csdeshang
     * @param array $condition 条件
     * @param string $order 排序
     * @return array
     */
    public function getStorewaybillList($condition, $order = '') {
        $waybill_model = model('waybill');

        $storewaybill_list = db('storewaybill')->where($condition)->order($order)->select();
        foreach ($storewaybill_list as $key => $value) {
            $storewaybill_list[$key]['is_default_text'] = $value['storewaybill_isdefault'] ? '是' : '否';
            $storewaybill_list[$key]['waybill_pixel_top'] = $value['storewaybill_top'] * $waybill_model::WAYBILL_PIXEL_CONSTANT;
            $storewaybill_list[$key]['waybill_pixel_left'] = $value['storewaybill_left'] * $waybill_model::WAYBILL_PIXEL_CONSTANT;
        }
        return $storewaybill_list;
    }

    /**
     * 读取列表包含模板信息
     * @access public
     * @author csdeshang
     * @param type $store_id 店铺ID
     * @param type $store_express 店铺运单
     * @return type
     */
    public function getStorewaybillListWithWaybillInfo($store_id, $store_express) {
        $condition = array();
        $condition['s.store_id'] = $store_id;
        $condition['s.express_id'] = array('in', $store_express);
        $field = 's.*,w.waybill_image,w.waybill_width,w.waybill_height';
        return db('storewaybill')->alias('s')->join('__WAYBILL__ w','w.waybill_id=s.waybill_id')->where($condition)->field($field)->select();
    }

    /**
     * 读取单条记录
     * @access public
     * @author csdeshang
     * @param array $condition 条件
     * @return array
     */
    public function getStorewaybillInfo($condition) {
        $storewaybill_info = db('storewaybill')->where($condition)->find();
        if(!empty($storewaybill_info)) {
            $storewaybill_info['storewaybill_data'] = unserialize($storewaybill_info['storewaybill_data']);
        }
        return $storewaybill_info;
    }


    /**
     * 增加
     * @access public
     * @author csdeshang
     * @param array $storewaybill_info 运单信息
     * @return bool
     */
    public function addStorewaybill($storewaybill_info){
        $waybill_model = model('waybill');
        $item_list = $waybill_model->getWaybillItemList();
        foreach ($item_list as $key => $value) {
            $item_list[$key]['show'] = true;
        }
        $storewaybill_info['storewaybill_data'] = serialize($item_list);
        return db('storewaybill')->insertGetId($storewaybill_info);
    }

    /**
     * 更新
     * @access public
     * @author csdeshang
     * @param type $update 更新数据
     * @param type $condition 条件
     * @param type $data 参数数组
     * @return type
     */
    public function editStorewaybill($update, $condition, $data = array()) {
        if(!empty($data)) {
            $update['storewaybill_data'] = $this->_getStorewaybillData($data);
        }
        return db('storewaybill')->where($condition)->update($update);
    }

    /**
     * 获取处理后的自定义数据内容
     * @access public
     * @author csdeshang
     * @param array $data 数据
     * @return array
     */
    private function _getStorewaybillData($data) {
        $waybill_model = model('waybill');

        $item_list = $waybill_model->getWaybillItemList();
        foreach ($item_list as $key => $value) {
            if($data[$key]) {
                $item_list[$key]['show'] = true;
            } else {
                $item_list[$key]['show'] = false;
            }
        }
        return serialize($item_list);
    }

    /**
     * 设置默认打印模板
     * @access public
     * @author csdeshang
     * @param int $storewaybill_id 运单ID 
     * @param int $store_id 店铺ID
     * @return bool|爱人ray
     */
    public function editStorewaybillDefault($storewaybill_id, $store_id) {
        $storewaybill_id = intval($storewaybill_id);
        if($storewaybill_id <= 0) {
            return false;
        }

        //解除原默认设置
        $this->editStorewaybill(array('storewaybill_isdefault' => self::STOREWAYBILL_UNDEFAULT), array('store_id' => $store_id));

        $condition = array();
        $condition['storewaybill_id'] = $storewaybill_id;
        $condition['store_id'] = $store_id;
        return $this->editStorewaybill(array('storewaybill_isdefault' => self::STOREWAYBILL_DEFAULT), $condition);
    }

    /**
     * 删除
     * @access public
     * @author csdeshang
     * @param array $condition 条件
     * @return bool
     */
    public function delStorewaybill($condition) {
        return db('storewaybill')->where($condition)->delete();
    }
}