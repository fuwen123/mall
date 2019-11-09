<?php
namespace app\supplier\validate;

use think\Validate;
use think\Db;

class Supplier extends Validate
{
    // 验证规则
    protected $rule = [
		'suppliers_id'              => 'require',
        'old_password'              => 'require|checkOldPassword',
		'new_password'              => 'require|checkNewPassword',
		'confirm_password'          => 'require|checkConfirmPassword',
        'suppliers_desc'            => 'require',
        'suppliers_contacts'        => 'require',
        'suppliers_phone'           => 'require|checkSupplierPhone',
		'province_id'               => 'require|gt:0',
		'city_id'                   => 'require|gt:0',
		'district_id'               => 'require|gt:0',
		'supplier_address'          => 'require',
    ];
    protected $scene = [
        'change_password' => ['old_password','new_password','confirm_password','suppliers_id'],
        'edit' => ['suppliers_id','suppliers_desc','suppliers_contacts','suppliers_phone','province_id','city_id','district_id','supplier_address'],
    ];
	//错误消息
    protected $message = [
		'suppliers_id.require'                => '参数错误',
        'old_password.require'                => '当前登录密码必须',
		'new_password.require'                => '新登录密码必须',
		'confirm_password.require'            => '确认密码必须',
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
     * 检查当前密码
     * @param $value |验证数据
     * @param $rule |验证规则
     * @param $data |全部数据
     * @return bool|string
     */
    protected function checkOldPassword($value, $rule, $data)
    {
		$userId = Db::name('suppliers')->where('suppliers_id', $data['suppliers_id'])->getField('user_id');
		$password = encrypt($value);
		$user = Db::name('users')->where(['user_id' => $userId, 'password' => $password])->find();
		if ($user) {
			return true;
		} else {
			return '密码不正确';
		}
    }

    /**
     * 检查新密码
     * @param $value |验证数据
     * @param $rule |验证规则
     * @param $data |全部数据
     * @return bool|string
     */
    protected function checkNewPassword($value, $rule, $data)
    {
        $passwordLen = strlen($value);
        if($passwordLen < 6 || $passwordLen > 18){
            return '密码长度必须在6到18之间';
        }
        return true;
    }
	
	/**
     * 检查新密码
     * @param $value |验证数据
     * @param $rule |验证规则
     * @param $data |全部数据
     * @return bool|string
     */
    protected function checkConfirmPassword($value, $rule, $data)
    {
        if($value != $data['new_password']){
            return '两次输入密码不一致';
        }
        return true;
    }
}
