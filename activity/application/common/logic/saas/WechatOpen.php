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
 * @Author: yjp
 */
namespace app\common\logic\saas;

use app\common\util\TpshopException;
use app\common\model\saas\Miniapp;
use app\common\logic\saas\wechat\Wx3rdPlatform;
use app\common\model\saas\WechatPublicAuthorizer;
use app\common\logic\saas\wechat\WxCommon;

/**
 * 微信开放平台逻辑处理
 */
class WechatOpen extends WxCommon
{
    private $wechatOpen;
    
    private $wechatPublic;
    
    private $wechatMiniApp;
       

    public function __construct(\app\common\model\saas\WechatOpen $open)
    {
        $this->wechatOpen = $open;
    }
   
    
    public function setWechatPublic(WechatPublicAuthorizer $wechatPublic){
        $this->wechatPublic = $wechatPublic;
    }
    
    public function setMiniApp(Miniapp $wechatMiniApp){
        $this->wechatMiniApp = $wechatMiniApp;
    }
    
    /**
     * 绑定或解绑公众号/小程序
     * @param unknown $type
     * @param number $status 1 = 绑定  0 = 解绑
     * @return boolean|multitype:number NULL |multitype:number string
     */
    public function  bind($type,$status = 1){        
       $access_token = $this->checkAccessToken($type);
       if(!$access_token){          
           return false;
       }
 
       $appid = ($type == 1 ? $this->wechatPublic['authorizer_appid'] : $this->wechatMiniApp['appid']);     
       $wx3rd = Wx3rdPlatform::getInstance();
       if($status == 1){
           $rs = $wx3rd->bindWx3rdOpen($appid,$this->wechatOpen['open_appid'],$access_token);
       }else{
           $rs = $wx3rd->unbindWx3rdOpen($appid,$this->wechatOpen['open_appid'],$access_token);
       }
       if(!$rs){
           $this->setError($wx3rd->getError());
           return false;
       }
       if($type == 1){
           $this->wechatPublic->open_bind = $status;
           $this->wechatPublic->save();
       }else{
           $this->wechatMiniApp->open_bind = $status;
           $this->wechatMiniApp->save();
       }
        return true;
    }
    
    

    
    /**
     * 获取授权公众号或小程序的authorizer_access_token
     */
    public function checkAccessToken($type)
    {
        if($type == 1){
           $public = new \app\common\logic\saas\WechatPublicLogic($this->wechatPublic);
           if(!$public->getAccessToken()){
               $this->setError($public->getError());
               return false;
           }
           return $public->getAccessToken();
        }else{
            $miniapp = new \app\common\logic\saas\wechat\MiniApp3rd($this->wechatMiniApp);
            if(!$miniapp->getAccessToken()){
               $this->setError($miniapp->getError());
               return false;
            }
            return $miniapp->getAccessToken();
        }
    }
    
    
}