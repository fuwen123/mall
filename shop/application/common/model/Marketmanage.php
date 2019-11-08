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
 * 刮刮卡模型层
 */
class Marketmanage extends Model {

    /**
     * 营销活动列表
     * @author csdeshang
     * @param array $condition 检索条件
     * @param array $page 分页信息
     * @return array 数组类型的返回结果
     */
    public function getMarketmanageList($condition, $page, $limit = '',$order='marketmanage_id desc') {
        if ($page) {
            $result = db('marketmanage')->where($condition)->order($order)->paginate($page, false, ['query' => request()->param()]);
            $this->page_info = $result;
            return $result->items();
        } else {
            $result = db('marketmanage')->where($condition)->order($order)->limit($limit)->select();
            return $result;
        }
    }

    /**
     * 取单个营销活动的内容
     * @author csdeshang
     * @param array $conditions 检索条件
     * @return array 数组类型的返回结果
     */
    public function getOneMarketmanage($conditions,$lock=false) {
        return db('marketmanage')->where($conditions)->lock($lock)->find();
    }

    /**
     * 新增
     * @author csdeshang
     * @param array $data 参数内容
     * @return bool 布尔类型的返回结果
     */
    public function addMarketmanage($data) {
        if (empty($data)) {
            return false;
        }
        return db('marketmanage')->insertGetId($data);
    }

    /**
     * 更新信息
     * @author csdeshang
     * @param array $condition 条件
     * @param array $data 更新数据
     * @return bool 布尔类型的返回结果
     */
    public function editMarketmanage($condition, $data) {
        if (empty($data)) {
            return false;
        }
        return db('marketmanage')->where($condition)->update($data);
    }

    /**
     * 删除
     * @author csdeshang
     * @param array $marketmanage_id 检索条件
     * @return array $rs_row 返回数组形式的查询结果
     */
    public function delMarketmanage($marketmanage_id) {
        //删除主表
        $result = db('marketmanage')->where('marketmanage_id',$marketmanage_id)->delete();
        //删除奖品表
        db('marketmanageaward')->where('marketmanage_id',$marketmanage_id)->delete();
        //删除领取记录表
        db('marketmanagelog')->where('marketmanage_id',$marketmanage_id)->delete();
        return $result;
    }
    /**
     * 新增营销活动奖品信息
     * @author csdeshang
     * @param array $data 更新信息
     * @return array 数组类型的返回结果
     */
    public function addMarketmanageAward($data) {
        if (empty($data)) {
            return false;
        }
        $result = db('marketmanageaward')->insertGetId($data);
        return $result;
    }
    
    /**
     * 更新营销活动奖品信息
     * @author csdeshang
     * @param array $condition 检索条件
     * @param array $data 更新信息
     * @return array 数组类型的返回结果
     */
    public function editMarketmanageAward($condition,$data) {
        if (empty($data)) {
            return false;
        }
        $result = db('marketmanageaward')->where($condition)->update($data);
        return $result;
    }
    
    /**
     * 营销活动奖品记录
     * @author csdeshang
     * @param array $condition 检索条件
     * @param array $page 分页信息
     * @return array 数组类型的返回结果
     */
    public function getMarketmanageAwardList($condition,$lock=false) {
        $result = db('marketmanageaward')->where($condition)->order('marketmanageaward_level asc')->lock($lock)->select();
        return $result;
    }

    /**
     * 新增营销活动参与记录
     * @author csdeshang
     * @param array $data 信息
     * @return array 数组类型的返回结果
     */
    public function addMarketmanageLog($data) {
        if (empty($data)) {
            return false;
        }
        $result = db('marketmanagelog')->insertGetId($data);
        return $result;
    }
    
    /**
     * 营销活动参与记录列表
     * @author csdeshang
     * @param array $condition 检索条件
     * @param array $page 分页信息
     * @return array 数组类型的返回结果
     */
    public function getMarketmanageLogList($condition, $page='', $limit = '') {
        if ($page) {
            $result = db('marketmanagelog')->where($condition)->order('marketmanagelog_id desc')->paginate($page, false, ['query' => request()->param()]);
            $this->page_info = $result;
            return $result->items();
        } else {
            $result = db('marketmanagelog')->where($condition)->order('marketmanagelog_id desc')->limit($limit)->select();
            return $result;
        }
    }
    
    
    //营销活动类型
    public function marketmanage_type_list() {
        return array(
            1 => '刮刮卡',
            2 => '大转盘',
            3 => '砸金蛋',
            4 => '生肖翻翻看',
        );
    }

}
