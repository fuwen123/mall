<?php
/**
 * tpshop
 * ============================================================================
 * 版权所有 2015-2027 深圳搜豹网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.tp-shop.cn
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用 .
 * 不允许对程序代码以任何形式任何目的的再发布。
 * 采用TP5助手函数可实现单字母函数M D U等,也可db::name方式,可双向兼容
 * ============================================================================
 */

namespace app\admin\controller;

use app\common\util\TpshopException;
use app\common\model\saas\AppService as AppServiceModel;

class AppService extends Base
{
    private $appService;

    public function _initialize()
    {
        $saas_cfg = $GLOBALS['SAAS_CONFIG'];
        $service_id = $saas_cfg['service_id'];
        $AppService = new AppServiceModel();
        $this->appService = $AppService->where('service_id', $service_id)->find();
    }
    /**
     * 解绑小程序
     */
    public function unbindMiniApp()
    {
        $appServiceLogic = new \app\common\logic\saas\AppService($this->appService);
        try{
            $appServiceLogic->unbindMiniApp();
            $this->ajaxReturn(['status' => 1, 'msg' => '解绑成功']);
        }catch (TpshopException $t){
            $this->ajaxReturn($t->getErrorArr());
        }
    }
	
	
	    
    /**
     * 解绑公众号
     */
    public function unbindPublic()
    {
        $appServiceLogic = new \app\common\logic\saas\AppService($this->appService);
        try{
            $appServiceLogic->unbindPublic();
            $this->ajaxReturn(['status' => 1, 'msg' => '解绑成功']);
        }catch (TpshopException $t){
            $this->ajaxReturn($t->getErrorArr());
        }
    }
}