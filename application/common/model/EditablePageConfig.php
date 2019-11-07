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
class EditablePageConfig extends Model {

    public $page_info;

    /**
     * 新增可编辑页面配置
     * @author csdeshang
     * @param array $data 参数内容
     * @return bool 布尔类型的返回结果
     */
    public function addEditablePageConfig($data) {
        return db('editable_page_config')->insertGetId($data);
    }

    /**
     * 删除一个可编辑页面配置
     * @author csdeshang
     * @param array $condition 条件
     * @return bool 布尔类型的返回结果
     */
    public function delEditablePageConfig($condition) {
        //删除图片
        $editable_page_config_id_list=db('editable_page_config')->where($condition)->column('editable_page_config_id');
        if($editable_page_config_id_list){
            $upload_condition=array('upload_type'=>7,'item_id'=>array('in',$editable_page_config_id_list));
            $file_name_list=db('upload')->where($upload_condition)->column('file_name');
            foreach($file_name_list as $file_name){
                @unlink(BASE_UPLOAD_PATH . DS . ATTACH_EDITABLE_PAGE. DS .$file_name);
            }
            model('upload')->delUpload($upload_condition);
        }
        return db('editable_page_config')->where($condition)->delete();
    }

    /**
     * 获取可编辑页面配置列表
     * @author csdeshang
     * @param array $condition 查询条件
     * @param obj $page 分页页数
     * @param str $orderby 排序
     * @return array 二维数组
     */
    public function getEditablePageConfigList($condition = array(), $page = '', $orderby = 'editable_page_config_sort_order asc') {
        if ($page) {
            $result = db('editable_page_config')->where($condition)->order($orderby)->paginate($page, false, ['query' => request()->param()]);
            $this->page_info = $result;
            return $result->items();
        } else {
            return db('editable_page_config')->where($condition)->order($orderby)->select();
        }
    }
    public function getOneEditablePageConfig($condition = array()) {
        return db('editable_page_config')->where($condition)->find();
    }
    /**
     * 更新可编辑页面配置记录
     * @author csdeshang
     * @param array $data 更新内容
     * @return bool
     */
    public function editEditablePageConfig($condition, $data) {
        return db('editable_page_config')->where($condition)->update($data);
    }

    public function getEditablePageConfigGoods($goods_info) {
        //所需字段
        $fieldstr = "goods_id,goods_commonid,store_id,goods_name,goods_advword,goods_price,goods_promotion_price,goods_promotion_type,goods_marketprice,goods_image,goods_salenum,evaluation_good_star,evaluation_count";
        $fieldstr .= ',is_virtual,is_presell,is_goodsfcode,is_have_gift,goods_advword,store_id,store_name,is_platform_store';
        $goods_model = model('goods');
        $goods_list = array();
        $order = 'goods_id desc';
        switch ($goods_info['sort']) {
            case 'hot':
                $order = 'goods_salenum desc';
                break;
            case 'good':
                $order = 'evaluation_good_star desc';
                break;
        }
        if ($goods_info['if_fix']) {
            if (!empty($goods_info['goods_id'])) {
                $goods_list = $goods_model->getGoodsOnlineList(array('goods_id' => array('in', $goods_info['goods_id'])), $fieldstr, $goods_info['count'], $order);
            }
        } else {

            $where = array();
            if ($goods_info['gc_id']) {
                $where['gc_id'] = $goods_info['gc_id'];
            }
            $goods_list = $goods_model->getGoodsListByColorDistinct($where, $fieldstr, $order, $goods_info['count']);
        }
        return $goods_list;
    }
    
    public function getEditablePageConfigBrand($brand_info) {
        //所需字段
        $fieldstr = "*";
        $brand_model = model('brand');
        $brand_list = array();
        

        if ($brand_info['if_fix']) {
            if (!empty($brand_info['brand_id'])) {
                $brand_list = $brand_model->getBrandPassedList(array('brand_id' => array('in', $brand_info['brand_id'])), $fieldstr, $brand_info['count']);
            }
        } else {

            $where = array();
            if ($brand_info['gc_id']) {
                $where['gc_id'] = $brand_info['gc_id'];
            }
            $brand_list = $brand_model->getBrandPassedList($where, $fieldstr, $brand_info['count']);
        }
        return $brand_list;
    }
    public function getEditablePageConfigCate($cate_info,$model_id) {
      $cate_list=array();
        foreach($cate_info['list'] as $j => $cate){
          if(isset($cate['gc_id'])){
            
            $temp=model('goodsclass')->getGoodsclassInfoById($cate['gc_id']);
            if($model_id==8){
              $temp['children']=model('goodsclass')->getGoodsclassListByParentId($cate['gc_id']);
              foreach($temp['children'] as $k => $child){
                $temp['children'][$k]['children']=model('goodsclass')->getGoodsclassListByParentId($child['gc_id']);
              }
            }
            $cate_list[]=$temp;
          }
                        
        }
        return $cate_list;
    }

}

?>
