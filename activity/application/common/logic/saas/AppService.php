<?php
/**
 * tpshop
 * ============================================================================
 * * 版权所有 2015-2027 深圳搜豹网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.tp-shop.cn
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用 .
 * 不允许对程序代码以任何形式任何目的的再发布。
 * 采用TP5助手函数可实现单字母函数M D U等,也可db::name方式,可双向兼容
 * ============================================================================
 * @Author: lhb
 */
namespace app\common\logic\saas;

use app\common\model\saas\AppService as AppServiceModel;
use app\common\util\TpshopException;

class AppService
{

    private $appService;

    public function __construct(AppServiceModel $appService)
    {
        $this->appService = $appService;
    }

    /**
     * 解绑小程序
     * @throws TpshopException
     */
    public function unbindMiniApp()
    {
        if(empty($this->appService->miniapp)){
            throw new TpshopException('解绑小程序', -1, ['status' => -1, 'msg' => '没关联过小程序']);
        }
        $this->appService->miniapp->service_id = 0;
        $this->appService->miniapp->save();
        $this->appService->miniapp_id = 0;
        $this->appService->save();
    }
	
	  /**
     * 解绑公众号
     * @throws TpshopException
     */
    public function unbindPublic()
    {
        if(empty($this->appService->wechatpublic)){
            throw new TpshopException('解绑公众号', -1, ['status' => -1, 'msg' => '没绑定过公众号']);
        }
        $this->appService->wechatpublic->service_id = 0;
        $this->appService->wechatpublic->deleted = 1;
        $this->appService->wechatpublic->save();
        $this->appService->pa_id = 0;
        $this->appService->save();
    }
}