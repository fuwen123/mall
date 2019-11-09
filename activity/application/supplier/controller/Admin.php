<?php
/**
 * tpshop
 * ============================================================================
 * 版权所有 2015-2027 深圳搜豹网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.tp-shop.cn
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用 .
 * 不允许对程序代码以任何形式任何目的的再发布。
 * 采用最新Thinkphp5助手函数特性实现单字母函数M D U等简写方式
 * ============================================================================
 * Author: 当燃      
 * Date: 2015-09-09
 */

namespace app\supplier\controller;

use app\common\logic\Supplier;
use app\common\util\TpshopException;
use think\Session;
use think\Verify;
use think\Db;

class Admin extends Base {

	/**
	 * 管理员登录页
	 * @return mixed
	 */
	public function login(){
		if (session('?suppliers_id') && session('suppliers_id') > 0) {
			$this->error("您已登录", U('Supplier/Index/index'));
		}
		return $this->fetch();
	}

	/**
	 * 管理员登录
	 */
	public function logon()
	{
		$code = input('post.verify');
		$username = input('post.username/s');
		$password = input('post.password/s');
		$verify = new Verify();
		if (!$verify->check($code, "supplier_login")) {
			$this->ajaxReturn(['status' => 0, 'msg' => '验证码错误']);
		}
		$password = encrypt($password);
		$supplier = new Supplier();
		$supplier->setSupplierAccount($username);
		try{
			$supplier->login($password);//这里面登录成功后自动结算订单
			$url = session('from_url') ? session('from_url') : U('Supplier/Index/index');
			$this->ajaxReturn(['status' => 1, 'msg' => '成功登陆','url'=>$url]);
		}catch (TpshopException $t){
			$this->ajaxReturn($t->getErrorArr());
		}
	}

    /**
     * 退出登陆
     */
    public function logout()
    {
		session_unset();
		session_destroy();
		Session::clear();
        $this->success("退出成功",U('Supplier/Admin/login'));
    }
    
    /**
     * 验证码获取
     */
    public function verify()
    {
        $config = array(
            'fontSize' => 30,
            'length' => 4,
            'useCurve' => false,
            'useNoise' => false,
        	'reset' => false
        );    
        $Verify = new Verify($config);
        $Verify->entry("supplier_login");
        exit();
    }


}