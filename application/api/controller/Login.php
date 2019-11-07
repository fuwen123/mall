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
 * 登录控制器
 */
class Login extends MobileMall
{

    public function _initialize()
    {
        parent::_initialize();
        Lang::load(APP_PATH . 'home/lang/' . config('default_lang') . '/login.lang.php');
    }

    /**
     * @api {POST} api/Login/index 用户登录
     * @apiVersion 1.0.0
     * @apiGroup Login
     *
     * @apiParam {String} username 用户名
     * @apiParam {String} password 密码
     * @apiParam {String} client_type 客户端类型 android wap wechat ios windows jswechat
     *
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     * @apiSuccess {String} result.token  用户token
     * @apiSuccess {Object} result.info 用户信息
     * @apiSuccess {Int} result.info.member_id  用户ID
     * @apiSuccess {String} result.info.member_name  用户名称
     * @apiSuccess {String} result.info.member_truename  真实姓名
     * @apiSuccess {String} result.info.member_avatar  头像
     * @apiSuccess {Int} result.info.member_points  积分
     * @apiSuccess {String} result.info.member_email  邮箱
     * @apiSuccess {String} result.info.member_mobile  手机号
     * @apiSuccess {String} result.info.member_qq  QQ
     * @apiSuccess {String} result.info.member_ww  旺旺
     * @apiSuccess {String} result.seller_token  卖家token
     * @apiSuccess {Object} result.seller_info  卖家信息
     * @apiSuccess {Int} result.seller_info.store_id  店铺ID
     * @apiSuccess {Int} result.seller_info.member_id  用户ID
     * @apiSuccess {Int} result.seller_info.seller_id  卖家ID
     * @apiSuccess {String} result.seller_info.seller_name  卖家账号
     * @apiSuccess {String} result.seller_info.store_avatar  店铺头像
     * @apiSuccess {Int} result.seller_info.is_platform_store  是否自营店铺 0否1是
     * @apiSuccess {Int} result.seller_info.storeclass_id  店铺分类ID
     */
    public function index()
    {
        $username = input('param.username');
        $password = input('param.password');
        $client = input('param.client_type');

        if (empty($username) || empty($password) || !in_array($client, $this->client_type_array)) {
            ds_json_encode(10001,lang('param_error'));
        }

        $member_model = model('member');

        $array = array();
        $array['member_name'] = $username;
        $array['member_password'] = md5($password);
        $member_info = $member_model->getMemberInfo($array);
        if (empty($member_info) && preg_match('/^0?(13|15|17|18|14)[0-9]{9}$/i', $username)) {//根据会员名没找到时查手机号
            $array = array();
            $array['member_mobile'] = $username;
            $array['member_password'] = md5($password);
            $member_info = $member_model->getMemberInfo($array);
        }

        if (empty($member_info) && (strpos($username, '@') > 0)) {//按邮箱和密码查询会员
            $array = array();
            $array['member_email'] = $username;
            $array['member_password'] = md5($password);
            $member_info = $member_model->getMemberInfo($array);
        }

        if (is_array($member_info) && !empty($member_info)) {
            if (!$member_info['member_state']) {
                ds_json_encode(10001, lang('login_index_account_stop'));
            }
            $this->getUserToken($member_info,$client);
        }
        else {
            ds_json_encode(10001,lang('password_error'));
        }
    }
    
    
    
    public function get_inviter(){
        $inviter_id=intval(input('param.inviter_id'));
        $member=db('member')->where('member_id',$inviter_id)->field('member_id,member_name')->find();
        ds_json_encode(10000, '',array('member' => $member));
    }
   


    /**
     * @api {POST} api/Login/register 普通注册
     * @apiVersion 1.0.0
     * @apiGroup Login
     *
     * @apiParam {String} username 用户名
     * @apiParam {String} password 密码
     * @apiParam {String} password_confirm 确认密码
     * @apiParam {String} email 邮箱
     * @apiParam {Int} inviter_id 推荐人id
     * @apiParam {String} client 客户端类型
     *
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     * @apiSuccess {Int} result.userid  用户ID
     * @apiSuccess {String} result.username  用户名称
     * @apiSuccess {String} result.token  用户token
     * @apiSuccess {Object} result.info 用户信息
     * @apiSuccess {Int} result.info.member_id  用户ID
     * @apiSuccess {Object} result.info.member_name  用户名称
     * @apiSuccess {Object} result.info.member_truename  真实姓名
     * @apiSuccess {Object} result.info.member_avatar  头像
     * @apiSuccess {Object} result.info.member_points  积分
     * @apiSuccess {Object} result.info.member_email  邮箱
     * @apiSuccess {Object} result.info.member_mobile  手机号
     * @apiSuccess {Object} result.info.member_qq  QQ
     * @apiSuccess {Object} result.info.member_ww  旺旺
     */
    public function register()
    {
        $username = input('param.username');
        $password = input('param.password');
        $password_confirm = input('param.password_confirm');
        $email = input('param.email');
        $client = input('param.client');
        $inviter_id = intval(input('param.inviter_id'));
	if($password_confirm!=$password){
            ds_json_encode(10001,lang('login_register_password_not_same'));
        }
        $member_model = model('member');
        $register_info = array();
        $register_info['member_name'] = $username;
        $register_info['member_password'] = $password;
        $register_info['member_email'] = $email;
        //添加奖励积分
        if($inviter_id){
            $register_info['inviter_id'] = $inviter_id;
        }
        
        $member_info = $member_model->register($register_info);
        if (!isset($member_info['error'])) {
            $token = $member_model->getBuyerToken($member_info['member_id'], $member_info['member_name'], $client);
            if ($token) {
                ds_json_encode(10000, '',array('info'=>$this->getMemberUser($member_info),'username' => $member_info['member_name'], 'userid' => $member_info['member_id'],'token' => $token));
            }
            else {
                ds_json_encode(10001,lang('login_usersave_regist_fail'));
            }
        }
        else {
            ds_json_encode(10001,$member_info['error']);
        }
    }
    
    

    /**
     * @api {POST} api/Login/bind 绑定用户
     * @apiVersion 1.0.0
     * @apiGroup Login
     *
     * @apiParam {Int} type 类型 1注册 0绑定
     * @apiParam {String} user 用户名
     * @apiParam {String} username 用户名
     * @apiParam {String} password 密码
     * @apiParam {String} password2 确认密码
     * @apiParam {String} email 邮箱
     * @apiParam {String} openid openid
     * @apiParam {String} unionid unionid
     * @apiParam {String} nickname 昵称
     * @apiParam {String} headimgurl 头像
     * @apiParam {String} from 来源 wx微信
     * @apiParam {Int} inviter_id 推荐人id
     * @apiParam {String} client_type 客户端类型 android wap wechat ios windows jswechat
     *
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     * @apiSuccess {Int} result.userid  用户ID
     * @apiSuccess {String} result.username  用户名称
     * @apiSuccess {String} result.token  用户token
     * @apiSuccess {Object} result.info 用户信息
     * @apiSuccess {Int} result.info.member_id  用户ID
     * @apiSuccess {Object} result.info.member_name  用户名称
     * @apiSuccess {Object} result.info.member_truename  真实姓名
     * @apiSuccess {Object} result.info.member_avatar  头像
     * @apiSuccess {Object} result.info.member_points  积分
     * @apiSuccess {Object} result.info.member_email  邮箱
     * @apiSuccess {Object} result.info.member_mobile  手机号
     * @apiSuccess {Object} result.info.member_qq  QQ
     * @apiSuccess {Object} result.info.member_ww  旺旺
     */
    public function bind() {
        $member_model = model('member');
        $type = input('param.type');
        $user = input('param.user');
        $email = input('param.email');
        $password = input('param.password');
        $password2 = input('param.password2');
        $from = input('param.from');
        $openid = input('param.openid');
        $unionid = input('param.unionid');
        $nickname = input('param.nickname');
        $headimgurl = input('param.headimgurl');
        $client = input('param.client_type');
        $inviter_id = intval(input('param.inviter_id'));
        $reg_info = array();
        $data = array(
            'member_name' => $user,
            'member_password' => $password,
        );
        switch ($from) {
            case 'wx':
                $reg_info = array(
                    'member_wxopenid' => $openid, #开发者帐号唯一标识,与公众号标识不同
                    'member_wxunionid' => $unionid,
                    'nickname' => $nickname,
                    'headimgurl' => $headimgurl,
                );
                $data = array_merge($data, array(
                    'member_wxopenid' => $openid,
                    'member_wxunionid' => $unionid,
                    'member_wxinfo' => serialize($reg_info),
                ));
                break;
            case 'qq':
                $reg_info = array(
                    'member_qqopenid' => $openid, #开发者帐号唯一标识,与公众号标识不同
                    'member_qqunionid' => $unionid,
                    'nickname' => $nickname,
                    'headimgurl' => $headimgurl,
                );
                $data = array_merge($data, array(
                    'member_qqopenid' => $openid,
                    'member_qqunionid' => $unionid,
                    'member_qqinfo' => serialize($reg_info),
                ));
                break;
            case 'sina':
                $reg_info = array(
                    'member_sinaopenid' => $openid, #开发者帐号唯一标识,与公众号标识不同
                    'member_sinaunionid' => $unionid,
                    'nickname' => $nickname,
                    'headimgurl' => $headimgurl,
                );
                $data = array_merge($data, array(
                    'member_sinaopenid' => $openid,
                    'member_sinaunionid' => $unionid,
                    'member_sinainfo' => serialize($reg_info),
                ));
                break;
        }


        if ($type == 1) {//注册
            $data = array_merge($data, array(
                'member_email' => $email,
                'member_nickname' => $reg_info['nickname'],
                'inviter_id' => $inviter_id,
            ));
            
            $login_validate = validate('member');
            if (!$login_validate->scene('register')->check($data)) {
                ds_json_encode(10001, $login_validate->getError());
            }
            $member_info = $member_model->register($data);
            if (!isset($member_info['error'])) {
                $token = $member_model->getBuyerToken($member_info['member_id'], $member_info['member_name'], $client);
                if ($token) {
                    ds_json_encode(10000, '', array('info' => $this->getMemberUser($member_info), 'username' => $member_info['member_name'], 'userid' => $member_info['member_id'], 'token' => $token));
                } else {
                    ds_json_encode(10001, lang('login_fail'));
                }
//                $headimgurl = $reg_info['headimgurl'];
//                $avatar = @copy($headimgurl, BASE_UPLOAD_PATH . '/' . ATTACH_AVATAR . "/avatar_" . $member_info['member_id'] . ".jpg");
//                if ($avatar) {
//                    $member_model->editMember(array('member_id' => $member_info['member_id']), array('member_avatar' => "avatar_" . $member_info['member_id'] . ".jpg"));
//                }
            } else {
                ds_json_encode(10001, $member_info['error']);
            }
        } else {//绑定
            $login_validate = validate('member');
            if (!$login_validate->scene('login')->check($data)) {
                ds_json_encode(10001, $login_validate->getError());
            }
            $map = array(
                'member_name' => $data['member_name'],
                'member_password' => md5($data['member_password']),
            );
            $member_info = $member_model->getMemberInfo($map);
            if ($member_info) {
                $member_model->editMember(array('member_id' => $member_info['member_id']), array('member_wxunionid' => $data['member_wxunionid'], 'member_wxinfo' => $data['member_wxinfo']));
            } else {
                ds_json_encode(10001, lang('password_error'));
            }
            $token = $member_model->getBuyerToken($member_info['member_id'], $member_info['member_name'], $client);
            if ($token) {
                ds_json_encode(10000, '', array('info' => $this->getMemberUser($member_info), 'username' => $member_info['member_name'], 'userid' => $member_info['member_id'], 'token' => $token));
            } else {
                ds_json_encode(10001, lang('login_fail'));
            }
        }
    }
    
    /**
     * @api {POST} api/Login/get_user_by_openid 第三方通过openid获取用户信息
     * @apiVersion 1.0.0
     * @apiGroup Login
     *
     * @apiParam {String} from 来源 wx微信 qq sina新浪
     * @apiParam {String} openid openid
     * @apiParam {String} unionid unionid
     * @apiParam {String} avatar 头像
     * @apiParam {String} nickname 昵称
     * @apiParam {Int} inviter_id 推荐人ID
     * @apiParam {String} client_type 客户端类型 android wap wechat ios windows jswechat
     *
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     * @apiSuccess {String} result.token  用户token
     * @apiSuccess {Object} result.info 用户信息
     * @apiSuccess {Int} result.info.member_id  用户ID
     * @apiSuccess {String} result.info.member_name  用户名称
     * @apiSuccess {String} result.info.member_truename  真实姓名
     * @apiSuccess {String} result.info.member_avatar  头像
     * @apiSuccess {Int} result.info.member_points  积分
     * @apiSuccess {String} result.info.member_email  邮箱
     * @apiSuccess {String} result.info.member_mobile  手机号
     * @apiSuccess {String} result.info.member_qq  QQ
     * @apiSuccess {String} result.info.member_ww  旺旺
     * @apiSuccess {String} result.seller_token  卖家token
     * @apiSuccess {Object} result.seller_info  卖家信息
     * @apiSuccess {Int} result.seller_info.store_id  店铺ID
     * @apiSuccess {Int} result.seller_info.member_id  用户ID
     * @apiSuccess {Int} result.seller_info.seller_id  卖家ID
     * @apiSuccess {String} result.seller_info.seller_name  卖家账号
     * @apiSuccess {String} result.seller_info.store_avatar  店铺头像
     * @apiSuccess {Int} result.seller_info.is_platform_store  是否自营店铺 0否1是
     * @apiSuccess {Int} result.seller_info.storeclass_id  店铺分类ID
     */
    public function get_user_by_openid(){
        $from = input('param.from');
        $openid = input('param.openid');
        $unionid = input('param.unionid');
        $client = input('param.client_type');
        $avatar = input('param.avatar');
        $nickname = input('param.nickname');
        $inviter_id = intval(input('param.inviter_id'));
 
        if(!$from || !$openid || !in_array($client, $this->client_type_array)){
            ds_json_encode(10001, lang('param_error'));
        }
        $condition=array();
        switch($from){
            case 'wx':
                if($unionid){
                    $condition['member_wxunionid']=$unionid;
                }else{
                    $condition['member_wxopenid']=$openid;
                }
                break;
            case 'qq':
                $condition['member_qqopenid']=$openid;
                break;
            case 'sina':
                $condition['member_sinaopenid']=$openid;
                break;
            default:
                ds_json_encode(10001, lang('param_error'));
                break;
        }
        $member_model=model('member');
        $member_info = $member_model->getMemberInfo($condition);
        if(!$member_info){
            //自动注册
            if(config('auto_register')) {//如果开启了自动注册
                $logic_connect_api = model('connectapi', 'logic');
                //注册会员信息 返回会员信息
                $reg_info = array(
                    'member_wxopenid' => $openid,
                    'member_wxunionid' => $unionid,
                    'nickname' => $nickname,
                    'inviter_id'=>$inviter_id,#推荐人ID
//                    'headimgurl' => $avatar,#提高体验暂时不对图片进行处理
                );
                $wx_member = $logic_connect_api->wx_register($reg_info, $from);
                if (!empty($wx_member)) {
                    $token = $member_model->getBuyerToken($wx_member['member_id'], $wx_member['member_name'], $client);
                    ds_json_encode(10000, '',array('token'=>$token,'info'=>$this->getMemberUser($wx_member)));
                } else {
                     ds_json_encode(10001, lang('login_usersave_regist_fail'));
                }
            }else{
                ds_json_encode(10000);//没有用户，进入绑定页
            }
        }else{
            if($member_info['member_state']==0){
                ds_json_encode(10001, lang('member_state_0'));
            }
            $this->getUserToken($member_info,$client);
        }
    }
    
    private function getUserToken($member_info,$client){
        
        $member_model=model('member');
            $token = $member_model->getBuyerToken($member_info['member_id'], $member_info['member_name'], $client);
            if ($token) {
                $result = array();
                $result['token'] = $token;
                $result['info'] = $this->getMemberUser($member_info);
                //是否有卖家账户
                $seller_model = model('seller');
                $seller_info = $seller_model->getSellerInfo(array('member_id' => $member_info['member_id']));
                if($seller_info){
                    $token = Sellerlogin::_get_seller_token($seller_info['seller_id'], $seller_info['seller_name'], $client);
                    if(!$token){
                        ds_json_encode(10001,lang('login_fail'));
                    }
                    //读取店铺信息
                    $store_model = model('store');
                    $store_info = $store_model->getStoreInfoByID($seller_info['store_id']);
                    $result['seller_token'] = $token;
                    $result['seller_info'] = $this->getSellerUser($seller_info,$store_info);
                }
                ds_json_encode(10000, '',$result);
            }
            else {
                ds_json_encode(10001,lang('login_fail'));
            }
    }

}

?>
