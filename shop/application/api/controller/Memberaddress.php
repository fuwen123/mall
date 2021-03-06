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
 * 用户收货地址控制器
 */
class Memberaddress extends MobileMember
{
    public function _initialize()
    {
        parent::_initialize();
        Lang::load(APP_PATH . 'home/lang/'.config('default_lang').'/memberaddress.lang.php');
    }

    /**
     * @api {POST} api/memberaddress/address_list 获取用户地址列表
     * @apiVersion 1.0.0
     * @apiGroup MemberAddress
     * 
     * @apiHeader {String} X-DS-KEY 用户授权token
     * 
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     * @apiSuccess {String} result.address_list 地址列表
     * @apiSuccess {String} result.address_list.address_id 地址ID
     * @apiSuccess {String} result.address_list.member_id 用户ID
     * @apiSuccess {String} result.address_list.address_realname 收货人姓名
     * @apiSuccess {String} result.address_list.city_id 城市ID
     * @apiSuccess {String} result.address_list.area_id 地区ID
     * @apiSuccess {String} result.address_list.area_info 地区内容
     * @apiSuccess {String} result.address_list.address_detail 详细地址
     * @apiSuccess {String} result.address_list.address_tel_phone 座机
     * @apiSuccess {String} result.address_list.address_mob_phone 手机
     * @apiSuccess {String} result.address_list.address_is_default 是否默认地址
     * @apiSuccess {String} result.address_list.address_longitude 经度
     * @apiSuccess {String} result.address_list.address_latitude 纬度
     */
    public function address_list()
    {
        $address_model = model('address');
        $address_list = $address_model->getAddressList(array('member_id' => $this->member_info['member_id']));
        ds_json_encode(10000,'',array('address_list' => $address_list));
    }

    /**
     * @api {POST} api/memberaddress/address_info 获取地址详细信息
     * @apiVersion 1.0.0
     * @apiGroup MemberAddress
     * 
     * @apiHeader {String} X-DS-KEY 用户授权token
     * 
     * @apiParam {String} address_id 地址ID
     * 
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     * @apiSuccess {String} result.address_info 地址信息
     * @apiSuccess {String} result.address_info.address_id 地址ID
     * @apiSuccess {String} result.address_info.member_id 用户ID
     * @apiSuccess {String} result.address_info.address_realname 收货人姓名
     * @apiSuccess {String} result.address_info.city_id 城市ID
     * @apiSuccess {String} result.address_info.area_id 地区ID
     * @apiSuccess {String} result.address_info.area_info 地区内容
     * @apiSuccess {String} result.address_info.address_detail 详细地址
     * @apiSuccess {String} result.address_info.address_tel_phone 座机
     * @apiSuccess {String} result.address_info.address_mob_phone 手机
     * @apiSuccess {String} result.address_info.address_is_default 是否默认地址
     * @apiSuccess {String} result.address_info.address_longitude 经度
     * @apiSuccess {String} result.address_info.address_latitude 纬度
     */
    public function address_info()
    {
        $address_id = intval(input('post.address_id'));

        $address_model = model('address');

        $condition = array();
        $condition['address_id'] = $address_id;
        $address_info = $address_model->getAddressInfo($condition);
        if (!empty($address_id) && $address_info['member_id'] == $this->member_info['member_id']) {
            ds_json_encode(10000,'',array('address_info' => $address_info));
        }
        else {
            ds_json_encode(10001,lang('address_does_not_exist'));
        }
    }

    /**
     * @api {POST} api/memberaddress/address_del 删除地址
     * @apiVersion 1.0.0
     * @apiGroup MemberAddress
     * 
     * @apiHeader {String} X-DS-KEY 用户授权token
     * 
     * @apiParam {String} address_id 地址ID
     * @apiParam {String} address_realname 收货人姓名
     * @apiParam {String} city_id 市级ID
     * @apiParam {String} area_id 地区ID
     * @apiParam {String} area_info 地区内容
     * @apiParam {String} address_detail 详细地址
     * @apiParam {String} address_tel_phone 座机
     * @apiParam {String} address_mob_phone 手机
     * @apiParam {String} address_is_default 默认收货地址 1默认
     * @apiParam {String} address_longitude 经度
     * @apiParam {String} address_latitude 纬度
     * 
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     */
    public function address_del()
    {
        $address_id = intval(input('post.address_id'));

        $address_model = model('address');

        $condition = array();
        $condition['address_id'] = $address_id;
        $condition['member_id'] = $this->member_info['member_id'];
        $address_model->delAddress($condition);
        ds_json_encode(10000,'',1);
    }

    /**
     * @api {POST} api/memberaddress/address_add 新增地址
     * @apiVersion 1.0.0
     * @apiGroup MemberAddress
     * 
     * @apiHeader {String} X-DS-KEY 用户授权token
     * 
     * @apiParam {String} address_realname 收货人姓名
     * @apiParam {String} city_id 市级ID
     * @apiParam {String} area_id 地区ID
     * @apiParam {String} area_info 地区内容
     * @apiParam {String} address_detail 详细地址
     * @apiParam {String} address_tel_phone 座机
     * @apiParam {String} address_mob_phone 手机
     * @apiParam {String} address_is_default 默认收货地址 1默认
     * @apiParam {String} address_longitude 经度
     * @apiParam {String} address_latitude 纬度
     * 
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     */
    public function address_add()
    {
        $address_model = model('address');

        $address_info = $this->_address_valid();

        $result = $address_model->addAddress($address_info);
        if ($result) {
            ds_json_encode(10000,'',array('address_id' => $result));
        }
        else {
            ds_json_encode(10001,lang('ds_common_op_fail'));
        }
    }

    /**
     * @api {POST} api/memberaddress/address_edit 编辑地址
     * @apiVersion 1.0.0
     * @apiGroup MemberAddress
     * 
     * @apiHeader {String} X-DS-KEY 用户授权token
     * 
     * @apiParam {String} address_id 地址ID
     * 
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     */
    public function address_edit()
    {
        $address_id = intval(input('post.address_id'));

        $address_model = model('address');

        //验证地址是否为本人
        $address_info = $address_model->getOneAddress($address_id);
        if ($address_info['member_id'] != $this->member_info['member_id']) {
            ds_json_encode(10001,lang('param_error'));
        }

        $address_info = $this->_address_valid();
        $result = $address_model->editAddress($address_info, array('address_id' => $address_id,'member_id'=>$this->member_info['member_id']));
        if ($result) {
            ds_json_encode(10000,'',1);
        }
        else {
            ds_json_encode(10001,lang('ds_common_op_fail'));
        }
    }

    /**
     * 验证地址数据
     */
    private function _address_valid()
    {
        $data=[
            'address_realname'=>input('post.address_realname'),
            'area_info'=>input('post.area_info'),
            'address_detail'=>input('post.address_detail'),
        ];
        $memberaddress_validate = validate('memberaddress');
        if (!$memberaddress_validate->scene('address_valid')->check($data)){
            ds_json_encode(10001,$memberaddress_validate->getError());
        }
        
        //当默认地址为1时,把当前用户的地址设置为非默认地址
        $address_is_default = intval(input('post.address_is_default'));
        if($address_is_default == 1){
            model('address')->editAddress(array('address_is_default'=>0), array('member_id'=>$this->member_info['member_id']));
        }

        $data = array();
        $data['member_id'] = $this->member_info['member_id'];
        $data['address_realname'] = input('post.address_realname');
        $data['area_id'] = intval(input('post.area_id'));
        $data['city_id'] = intval(input('post.city_id'));
        $data['area_info'] = input('post.area_info');
        $data['address_detail'] = input('post.address_detail');
        $data['address_longitude'] = input('post.address_longitude');
        $data['address_latitude'] = input('post.address_latitude');
        $data['address_tel_phone'] = !empty(input('post.address_tel_phone'))?input('post.address_tel_phone'):'';
        $data['address_mob_phone'] = !empty(input('post.address_mob_phone'))?input('post.address_mob_phone'):'';
        $data['address_is_default'] = $address_is_default;
        return $data;
    }


}