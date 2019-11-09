<?php
namespace app\admin\validate;

use think\Validate;
use think\Db;

class Supplier extends Validate
{
    /*code_26供货商逻辑代码*/
    // 验证规则
    protected $rule = [
		'suppliers_id'              => 'require',
		'suppliers_account'         => 'require|unique:suppliers',
        'user_name'                 => 'require|checkUserName',
        'password'                  => 'require|checkPassword',
        'suppliers_name'            => 'require|checkSuppliersName|unique:suppliers',
        'suppliers_desc'            => 'require',
        'suppliers_contacts'        => 'require',
        'suppliers_phone'           => 'require|checkSupplierPhone',
		'province_id'               => 'require|gt:0',
		'city_id'                   => 'require|gt:0',
		'district_id'               => 'require|gt:0',
		'supplier_address'          => 'require',
    ];
    protected $scene = [
        'add' => ['suppliers_account','user_name','password','suppliers_name','suppliers_desc','suppliers_contacts','suppliers_phone','province_id','city_id','district_id','supplier_address'],
        'edit' => ['suppliers_id','suppliers_name','suppliers_desc','suppliers_contacts','suppliers_phone','province_id','city_id','district_id','supplier_address'],
    ];
	//错误消息
    protected $message = [
		'suppliers_id.require'                => '参数错误',
        'suppliers_account.require'           => '供应商后台账号不能为空',
        'suppliers_account.unique'            => '已有相同的供应商账号',
        'user_name.require'                   => '会员账号必须',
        'password.require'                    => '密码必须',
        'suppliers_name.require'              => '供应商名称必须',
        'suppliers_name.max'                  => '供应商名称长度不得超过20字符',
		'suppliers_name.unique'               => '已有相同的供应商名称',
        'suppliers_desc.require'              => '供应商描述不能为空',
        'suppliers_contacts.require'          => '供应商联系人不能为空',
        'suppliers_phone.require'             => '供应商联系人电话不能为空',
		'suppliers_phone.checkSupplierPhone'  => '供应商联系人电话格式错误',
		'province_id.require'                 => '请输入省份',
		'city_id.require'                     => '请输入城市',
		'district_id.require'                 => '请输入地区',
		'province_id.gt'                      => '请输入省份',
		'city_id.gt'                          => '请输入城市',
		'district_id.gt'                      => '请输入地区',
		'supplier_address.require'            => '请输入详细地址',
    ];
    /*code_26供货商逻辑代码*/
    
    /**
     * 检查会员账号
     * @param $value |验证数据
     * @param $rule |验证规则
     * @param $data |全部数据
     * @return bool|string
     */
    protected function checkUserName($value, $rule, $data)
    {
        /*code_26供货商逻辑代码*/
        $isEmail = check_email($value);
        $isMobile = check_mobile($value);
        if ($isEmail == false && $isMobile == false) {
            return '请输入正确的手机或者邮箱';
        }
        if ($isEmail) {
            $where['email'] = $value;
        } else {
            $where['mobile'] = $value;
        }
        $user_id = Db::name('users')->where($where)->getField('user_id');
        if ($user_id) {
            $userSupplierCount = Db::name('suppliers')->where(['user_id' => $user_id])->count();
            if (userSupplierCount) {
                return '该会员已经是供应商';
            } else {
                return true;
            }
        } else {
            return true;
        }
        /*code_26供货商逻辑代码*/
    }

	/**
     * 检查供应商名字
     * @param $value |验证数据
     * @param $rule |验证规则
     * @param $data |全部数据
     * @return bool|string
     */
    protected function checkSuppliersName($value, $rule, $data)
    {
        $strLen = mb_strlen($value,"utf-8");
		if ($strLen < 2 || $strLen > 15) {
			return '供应商名称长度必须在2到15之间';
		}
		return true;
    }
	
    /**
     * 检查供应商电话
     * @param $value |验证数据
     * @param $rule |验证规则
     * @param $data |全部数据
     * @return bool|string
     */
    protected function checkSupplierPhone($value, $rule, $data)
    {
        if (check_telephone($value) || check_mobile($value)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 检查密码
     * @param $value |验证数据
     * @param $rule |验证规则
     * @param $data |全部数据
     * @return bool|string
     */
    protected function checkPassword($value, $rule, $data)
    {
        $password_len = strlen($value);
        if($password_len < 6 || $password_len > 18){
            return '密码长度必须在6到18之间';
        }
        return true;
    }
    /**
     * 是否定位坐标
     * @param $value |验证数据
     * @param $rule |验证规则
     * @param $data |全部数据
     * @return bool|string
     */
    /*protected function checkLongitude($value, $rule, $data)
    {
        if(ceil($data['longitude']) == 0){
            return '请在地图定位中标记坐标';
        }
        return true;
    }*/
    /**
     * 是否定位坐标
     * @param $value |验证数据
     * @param $rule |验证规则
     * @param $data |全部数据
     * @return bool|string
     */
    /*protected function checkLatitude($value, $rule, $data)
    {
        if(ceil($data['latitude']) == 0){
            return '请在地图定位中标记坐标';
        }
        return true;
    }*/

}
