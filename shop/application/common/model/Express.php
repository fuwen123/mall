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
class Express extends Model {

    public $page_info;
    
    /**
     * 查询快递列表
     * @access public
     * @author csdeshang
     * @return array
     */
    public function getExpressList() {
        return rkcache('express', true);
    }

    /**
     * 根据编号查询快递列表
     * @access public
     * @author csdeshang
     * @param int $id 快递编号
     * @return array
     */
    public function getExpressListByID($id = null) {
        $express_list = rkcache('express', true);

        if (!empty($id)) {
            $id_array = explode(',', $id);
            foreach ($express_list as $key => $value) {
                if (!in_array($key, $id_array)) {
                    unset($express_list[$key]);
                }
            }
            return $express_list;
        } else {
            return array();
        }
    }

    /**
     * 查询详细信息(通过缓存获取)
     * @access public
     * @author csdeshang
     * @param int $id 快递编号
     * @return array
     */
    public function getExpressInfo($id) {
        $express_list = $this->getExpressList();
        return $express_list[$id];
    }
    /**
     * 获取单个信息
     * @param type $condition
     * @return type
     */
    public function getOneExpress($condition) {
        return db('express')->where($condition)->find();
    }
    
    /**
     * 根据快递公司ecode获得快递公司信息
     * @access public
     * @author csdeshang
     * @param $ecode string 快递公司编号
     * @return array 快递公司详情
     */
    public function getExpressInfoByECode($ecode) {
        $ecode = trim($ecode);
        if (!$ecode) {
            return array('state' => false, 'msg' => '参数错误');
        }
        $express_list = $this->getExpressList();
        $express_info = array();
        if ($express_list) {
            foreach ($express_list as $v) {
                if ($v['express_code'] == $ecode) {
                    $express_info = $v;
                }
            }
        }
        if (!$express_info) {
            return array('state' => false, 'msg' => '快递公司信息错误');
        } else {
            return array('state' => true, 'data' => array('express_info' => $express_info));
        }
    }
    /**
     * 获取快递列表
     * @param type $condition 条件
     * @param type $page 分页
     * @param type $order 排序
     * @return type
     */ 
    public function getAllExpresslist($condition,$page,$order='express_order,express_state desc,express_id'){
        if($page){
            $res = db('express')->where($condition)->order($order)->paginate($page, false, ['query' => request()->param()]);
            $this->page_info = $res;
            return $res->items();
        }else{
            return db('express')->where($condition)->order($order)->select();
        }
    }
    /**
     * 删除物流方式
     * @access public
     * @author csdeshang
     * @param array $condition 检索条件
     * @return boolean
     */
    public function delExpress($condition) {
        dkcache('express');
        return db('express')->where($condition)->delete();
    }
    
    /**
     * 添加物流方式
     * @access public
     * @author csdeshang 
     * @param array $data 参数内容
     * @return boolean
     */
    public function addExpress($data) {
        dkcache('express');
        return db('express')->insertGetId($data);
    }
    
    /**
     * 编辑物流方式
     * @access public
     * @author csdeshang 
     * @param array $condition 检索条件
     * @param array $update 更新数据
     * @return boolean
     */
    public function editExpress($condition, $update) {
        dkcache('express');
        return db('express')->where($condition)->update($update);
    }

    /**
     * 快递查询
     */
    public function queryExpress($express_code,$shipping_code){
        $requestData= "{'OrderCode':'','ShipperCode':'".$express_code."','LogisticCode':'".$shipping_code."'}";
        $datas = array(
            'EBusinessID' => config('expresscf_kdn_id'),
            'RequestType' => '1002',
            'RequestData' => urlencode($requestData) ,
            'DataType' => '2',
            'DataSign' => urlencode(base64_encode(md5($requestData.config('expresscf_kdn_key'))))
        );
        $result = http_request('http://api.kdniao.com/Ebusiness/EbusinessOrderHandle.aspx','post',$datas);
        return json_decode($result, true);
    }
}

?>
