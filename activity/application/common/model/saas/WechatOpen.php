<?php
/**
 * tpshop
 * ============================================================================
 * 版权所有 2015-2027 深圳搜豹网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.tp-shop.cn
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用 .
 * 不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * @author: yjp
 */

namespace app\common\model\saas;

use think\Model;
use think\Db;

class WechatOpen extends SaasModel
{

    public function getWechatPublicAuthorizer($value,$data){
         return Db::name('wechat_public_authorizer')->where('user_id', $data['user_id'])->find();
    }
    
    public function getMiniApp($value,$data){
        return Db::name('miniapp')->where('user_id', $data['user_id'])->find();
    }
}