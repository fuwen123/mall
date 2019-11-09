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
use app\common\logic\saas\wechat\Wx3rdPlatform;
use app\common\logic\saas\wechat\WxCommon; 
use app\common\model\saas\WechatOpen as open;

/**
 * 微信授权公众号逻辑处理
 */
class WechatPublicLogic extends WxCommon
{
    private $wechatPublic;

    public function __construct(\app\common\model\saas\WechatPublicAuthorizer $public)
    {
        $this->wechatPublic = $public;
    }
   
    /**
     * 创建开放平台账号
     */
    public function  createWx3rdOpen(){     
        $access_token = $this->getAccessToken();        
        if(!$access_token){
            return false;
        }
        
        $wx3rd = Wx3rdPlatform::getInstance();
        $open_id = $wx3rd->createWx3rdOpen($this->wechatPublic['authorizer_appid'],$access_token);       

        if(!$open_id){
            $this->setError($wx3rd->getError());
            return false;
        }
        
        if(!$check = open::get(['open_appid'=>$open_id])){
            $data = [
                'open_appid'   => $open_id,
                'create_time'  => time(),
                'type'         => 1,
                'user_id'      => $this->wechatPublic['user_id']
            ];
           
            $check = open::create($data);
        }else{
            $check->save(['open_appid'=>$open_id,'update_time'=>time()]);
        }           
        return true;        
    }
    
    
    /**
     * 获取授权公众号的authorizer_access_token
     */
    public function getAccessToken()
    {
        $config = $this->wechatPublic;
        if (empty($config) || !$config['authorizer_appid'] || !$config['authorizer_access_token'] || !$config['authorizer_refresh_token']) {
            $this->setError("授权信息不全，请先授权");
            return false;
        }
    
        //判断是否过了缓存期
        if ($config['expires_in'] > time()) {
            return $config['authorizer_access_token'];
        }
    
        $wx3rd = Wx3rdPlatform::getInstance();
        $return = $wx3rd->getAuthorizerToken($config['authorizer_appid'], $config['authorizer_refresh_token']);
        if ($return === false) {
            $this->setError($wx3rd->getError());
            $this->config['expires_in'] = 0;
            $this->wechatPublic->save(['expires_in' => 0], ['user_id' => $config['user_id']]);
            return false;
        }
        $data = [
            'authorizer_access_token'          => $return['authorizer_access_token'],
            'expires_in'  => $return['expires_in'] + time() - 200, //提前200s失效
            'authorizer_refresh_token'         => $return['authorizer_refresh_token'],
        ];
        $this->wechatPublic->save($data, ['user_id' => $config['user_id']]);
        $this->wechatPublic = array_merge($config, $data);
    
        return $return['authorizer_access_token'];
    }
}