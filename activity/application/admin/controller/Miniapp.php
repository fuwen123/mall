<?php
/**
 * tpshop
 * ============================================================================
 * 版权所有 2015-2027 深圳搜豹网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.tp-shop.cn
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用 .
 * 不允许对程序代码以任何形式任何目的的再发布。
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ============================================================================
 * Author: 当燃
 * 拼团控制器
 * Date: 2016-06-09
 */

namespace app\admin\controller;

use app\common\logic\saas\wechat\MiniApp3rd;
use app\common\logic\saas\wechat\Wx3rdPlatform;
use app\common\model\saas\AppService;
use app\common\model\saas\Miniapp as MiniappModel;
use app\common\model\saas\UserMiniapp;
use app\common\util\TpshopException;
use think\Loader;
use think\Db;
use think\Page;

class Miniapp extends Base
{

	private $appService;
	private $saas;

	public function _initialize()
	{
		$saas_cfg = $GLOBALS['SAAS_CONFIG'];
		$service_id = $saas_cfg['service_id'];
		$AppService = new AppService();
		$this->appService = $AppService->where('service_id', $service_id)->find();
		$this->saas = $GLOBALS['SAAS'];
	}

	public function index()
	{
		$miniapp = MiniappModel::get(['user_id' => $this->appService['user_id'], 'miniapp_id' => $this->appService['miniapp_id']]);
		$this->assign('miniapp', $miniapp);
		$this->assign('saas', $this->saas);
		$this->assign('app_service', $this->appService);
		return $this->fetch();
	}

	public function release_manage(){
		$miniapp = MiniappModel::get(['user_id' => $this->appService['user_id'], 'miniapp_id' => $this->appService['miniapp_id']]);
		if (!$miniapp) {
			$this->error('小程序不存在', 'admin/Miniapp/index');
		}
		if($miniapp['is_auth'] != 1){
			$this->error('小程序未绑定', 'admin/Miniapp/index');
		}
		$UserMiniApp = new UserMiniapp();
		$test_user_miniApp = $UserMiniApp->where(['miniapp_id' => $miniapp['miniapp_id'], 'status' => UserMiniapp::STATUS_TEST])->order('add_time desc')->find();
		$audit_status = [UserMiniapp::STATUS_AUDITING, UserMiniapp::STATUS_AUDIT_DONG, UserMiniapp::STATUS_AUDIT_FAIL];
		$audit_user_miniApp = $UserMiniApp->where(['miniapp_id' => $miniapp['miniapp_id'], 'status' => ['in', $audit_status]])->order('audit_time desc')->find();
		$release_user_miniApp = $UserMiniApp->where(['miniapp_id' => $miniapp['miniapp_id'], 'status' => UserMiniapp::STATUS_ON_RELEASE])->order('release_time desc')->find();
		$this->assign('miniapp', $miniapp);
		$this->assign('test_user_miniApp', $test_user_miniApp);
		$this->assign('audit_user_miniApp', $audit_user_miniApp);
		$this->assign('release_user_miniApp', $release_user_miniApp);
		return $this->fetch();
	}
	
	
	/**
	 * 小程序预提交模板删除
	 */
    public function delete(){
        $id = input('id');
        $miniapp = MiniappModel::get(['user_id' => $this->appService['user_id'], 'miniapp_id' => $this->appService['miniapp_id']]);
        if (!$miniapp) {
            $this->ajaxReturn(['status' => -1, 'msg' => '小程序不存在','result'=>'Miniapp/index']);
        }
        if($miniapp['is_auth'] != 1){
            $this->ajaxReturn(['status' => -1, 'msg' => '小程序未绑定','result'=>'Miniapp/index']);
        }
        $UserMiniApp = new UserMiniapp();       
        //$audit_status = [UserMiniapp::STATUS_AUDITING, UserMiniapp::STATUS_AUDIT_DONG, UserMiniapp::STATUS_AUDIT_FAIL];
        $audit_user_miniApp = $UserMiniApp->where(['miniapp_id' => $miniapp['miniapp_id'], 'id' => $id])->find();   
        if(!$audit_user_miniApp){
            $this->ajaxReturn(['status' => 1, 'msg' => '小程序已删除']);
        }
        $audit_user_miniApp->delete();
        $this->ajaxReturn(['status' => 1, 'msg' => '删除成功']);
    }
	

	/**
	 * 设置体验者页
	 */
	public function tester()
	{
		$miniapp = MiniappModel::get(['user_id' => $this->appService['user_id'], 'miniapp_id' => $this->appService['miniapp_id']]);
		if (!$miniapp) {
			$this->error('小程序不存在', 'admin/Miniapp/index');
		}
		if($miniapp['is_auth'] != 1){
			$this->error('小程序未绑定', 'admin/Miniapp/index');
		}

		$this->assign('miniapp', $miniapp);
		return $this->fetch();
	}

	/**
	 * 设置小程序是否可见（可访问）
	 */
	public function set_visit_status()
	{
		$status = input('status');
		$miniapp = MiniappModel::get(['user_id' => $this->appService['user_id'], 'miniapp_id' => $this->appService['miniapp_id']]);
		$miniApp3rd = new MiniApp3rd($miniapp);
		if ($miniApp3rd->changeVisitStatus($status) === false) {
			$this->ajaxReturn(['status' => -1, 'msg' => $miniApp3rd->getError()]);
		}
		$miniapp->save(['is_show_release' => $status]);
		$this->ajaxReturn(['status' => 1, 'msg' => '设置成功']);
	}

	/**
	 * 提交审核页
	 */
	public function audit()
	{
		$miniapp = MiniappModel::get(['user_id' => $this->appService['user_id'], 'miniapp_id' => $this->appService['miniapp_id']]);
		if (!$miniapp) {
			$this->error('小程序不存在', 'admin/Miniapp/index');
		}
		if($miniapp['is_auth'] != 1){
			$this->error('小程序未绑定', 'admin/Miniapp/index');
		}

		//废弃审核失败的
		if ($userMiniapp = UserMiniapp::get(['user_id' => $this->appService['user_id'], 'status' => UserMiniapp::STATUS_AUDIT_FAIL])) {
		    $userMiniapp->save(['status' => UserMiniapp::STATUS_ABANDON]);//废弃
		}

// 		if (!UserMiniapp::get(['user_id' => $this->appService['user_id'], 'status' => UserMiniapp::STATUS_TEST])) {
// 			$this->error('体验版本不存在，不能提交审核');
// 		}

		$miniApp3rd = new MiniApp3rd($miniapp);
		$categories = $miniApp3rd->getCategory();
		if ($categories === false) {
			$this->error($miniApp3rd->getError());
		}

		//该服务分类不能控制，只能每次拉取的时候更新
		$miniapp->save(['categories' => $categories]);

		//废弃审核失败的
		//if ($userMiniapp = UserMiniapp::get(['user_id' => $this->appService['user_id'], 'status' => UserMiniapp::STATUS_AUDIT_FAIL])) {
		//	$userMiniapp->save(['status' => UserMiniapp::STATUS_ABANDON]);//废弃
		//}

		$this->assign('categories', $categories);
		return $this->fetch();
	}
	/**
	 * 发布小程序
	 */
	public function release()
	{
		$mini_app = MiniappModel::get(['user_id' => $this->appService['user_id'], 'miniapp_id' => $this->appService['miniapp_id']]);
		$miniLogic = new \app\common\logic\saas\MiniApp($mini_app);
		try{
			$miniLogic->release();
			$this->ajaxReturn(['status'=>1,'msg'=>'绑定成功']);
		}catch (TpshopException $t){
			$this->ajaxReturn($t->getErrorArr());
		}
	}

	/**
	 * 获取授权链接
	 */
	public function auth_url()
	{
		$wx3rd = Wx3rdPlatform::getInstance();
		$auth_url = $wx3rd->getAuthUrl();
		if ($auth_url === false) {
			$this->ajaxReturn(['status' => -1, 'msg' => $wx3rd->getError()]);
		}
		$this->ajaxReturn(['status' => 1, 'msg' => '获取成功', 'result' => $auth_url]);
	}
	/**
	 * 提交审核
	 */
	public function submitAudit()
	{
		$data = input('post.');
		$validate = Loader::validate('MiniAppAudit');
		if (!$validate->batch()->check($data)) {
			$error = $validate->getError();
			$error_msg = array_values($error);
			$this->ajaxReturn(['status' => -1, 'msg' => $error_msg[0], 'result' => $validate->getError()]);
		}
		$mini_app = MiniappModel::get(['user_id' => $this->appService['user_id'], 'miniapp_id' => $this->appService['miniapp_id']]);
		$miniLogic = new \app\common\logic\saas\MiniApp($mini_app);
		try{
			$miniLogic->audit($data);
			$this->ajaxReturn(['status'=>1,'msg'=>'提交审核成功']);
		}catch (TpshopException $t){
			$this->ajaxReturn($t->getErrorArr());
		}
	}
	
	
	  /**
     * 小程序撤回审核
     */
	public function auditBack(){
	    $mini_app = MiniappModel::get(['user_id' => $this->appService['user_id'], 'miniapp_id' => $this->appService['miniapp_id']]);
	    $miniLogic = new \app\common\logic\saas\MiniApp($mini_app);
	    try{
	        $rs = $miniLogic->back();
	         if($rs){
	         	//废弃审核撤回的
        		if ($userMiniapp = UserMiniapp::get(['user_id' => $this->appService['user_id'], 'status' => UserMiniapp::STATUS_AUDITING])) {
        		    $userMiniapp->save(['status' => UserMiniapp::STATUS_ABANDON]);//废弃
        		}
	         }
	        $this->ajaxReturn(['status'=>1,'msg'=>'撤回审核成功']);
	    }catch (TpshopException $t){
	        $this->ajaxReturn($t->getErrorArr());
	    }
	}
	

	/**
	 * 绑定体验者
	 */
	public function bindTester()
	{
		$wechat_id = input('wechat_id/s');
		if (empty($wechat_id)) {
			$this->ajaxReturn(['status' => -1, 'msg' => '微信号不能为空!']);
		}
		$mini_app = MiniappModel::get(['user_id' => $this->appService['user_id'], 'miniapp_id' => $this->appService['miniapp_id']]);
		$miniLogic = new \app\common\logic\saas\MiniApp($mini_app);
		try{
			$miniLogic->bindTester($wechat_id);
			$this->ajaxReturn(['status'=>1,'msg'=>'绑定成功']);
		}catch (TpshopException $t){
			$this->ajaxReturn($t->getErrorArr());
		}
	}

	/**
	 * 解绑体验者
	 */
	public function unbindTester()
	{
		$wechat_id = input('wechat_id/s');
		$mini_app = MiniappModel::get(['user_id' => $this->appService['user_id'], 'miniapp_id' => $this->appService['miniapp_id']]);
		$miniLogic = new \app\common\logic\saas\MiniApp($mini_app);
		try{
			$miniLogic->unbindTester($wechat_id);
			$this->ajaxReturn(['status'=>1,'msg'=>'解绑成功']);
		}catch (TpshopException $t){
			$this->ajaxReturn($t->getErrorArr());
		}
	}

	/**
	 * 提交小程序模板|提交体验版
	 */
	public function commitTemplate()
	{
		$mini_app = MiniappModel::get(['user_id' => $this->appService['user_id'], 'miniapp_id' => $this->appService['miniapp_id']]);
		$miniLogic = new \app\common\logic\saas\MiniApp($mini_app);
		try{
			$miniLogic->commitTemplate();
			$this->ajaxReturn(['status' => 1, 'msg' => '提交成功']);
		}catch (TpshopException $t){
			$this->ajaxReturn($t->getErrorArr());
		}
	}
	/**
	 * 获取体验二维码图片
	 */
	public function test_qrcode()
	{
		$miniapp = MiniappModel::get(['user_id' => $this->appService['user_id'], 'miniapp_id' => $this->appService['miniapp_id']]);
		if (!$miniapp) {
			exit('小程序尚不存在');
		}
		if($miniapp['is_auth'] != 1){
			exit('小程序未绑定');
		}
		$miniApp3rd = new MiniApp3rd($miniapp);
		$content = $miniApp3rd->getTestQrcode();
		if ($content === false) {
			exit($miniApp3rd->getError());
		}
		header('Content-type: image/jpeg');
		exit($content);
	}
}
