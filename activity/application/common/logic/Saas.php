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
 * Author: lhb
 */

namespace app\common\logic;

use app\common\model\saas\AppService;
use app\common\model\saas\ModuleOrder;
use app\common\model\saas\SystemMenuPrice;
use think\Cache;
use think\Db;
use think\Config;
use think\Session;

\think\Loader::import('controller/Jump', TRAIT_PATH, EXT);

class Saas
{
    use \traits\controller\Jump;

    /**
     * @var self
     */
    static private $instance = null;

    private $isSaas = false;
    private $isBaseUser = false;
    private $app = []; //本应用配置
    private $saas = []; //saas总配置
    private $loginUrl = ''; //登录链接

    public static function instance()
    {
        if (!self::$instance) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    private function __construct()
    {
        $this->isSaas = (IS_SAAS === 1);
        $this->isBaseUser = (SAAS_BASE_USER === 1);
        $this->app = $GLOBALS['SAAS_CONFIG'];
        $this->saas = $GLOBALS['SAAS'];
        $this->loginUrl = SITE_URL.'/admin/admin/login';
    }

    public function isSaas()
    {
        return $this->isSaas;
    }

    public function isNormalUser()
    {
        return $this->isSaas && !$this->isBaseUser;
    }

    public function isBaseUser()
    {
        return $this->isBaseUser;
    }

    /**
     * 检查是否单点登录
     */
    public function checkSso()
    {
        if (!$this->isSaas()) {
            return;
        }

        //过滤不需要登陆的行为
        $action = request()->action();
        if (!in_array($action, ['login', 'vertify', 'forget_pwd'])) {
            if (!session('admin_id')) {
                $this->redirectSso();
            }
        } elseif ($action == 'login' && request()->isGet()) {
            $isLogin = input('is_login', 0);
            if ($isLogin != 1) {
                $msg = input('err_msg');
                $msg && $this->error($msg, $this->loginUrl);
                if (!session('had_redirect_sso')) {
                    session('had_redirect_sso', 1);
                    $this->redirectSso();
                } else {
                    session('had_redirect_sso', 0);
                }
            } else {
                session('had_redirect_sso', 0);
                if (!$ssoToken = input('sso_token', '')) {
                    $this->error('平台已退出登录', $this->loginUrl);
                }

                $this->verifySsoToken($ssoToken);

                $key = $this->getSsoTokenKey();
                Cache::set($key, ['session_id' => session_id(), 'sso_token' => $ssoToken], 0);

                $admin = Db::name('admin')->alias('a')->join('__ADMIN_ROLE__ ar', 'a.role_id=ar.role_id')->where('admin_id', 1)->find();
                (new AdminLogic)->handleLogin($admin, $admin['act_list']);

                $this->redirect(url('admin/index/index'));
            }
        }
    }

    private function verifySsoToken($ssoToken)
    {
        $params = http_build_query([
            'service_domain' => $this->app['domain'],
            'app_domain' => $this->saas['main_domain'],
            'sso_token' => $ssoToken,
        ]);
        $verifyUrl = 'http://'.$this->saas['saas_domain'].'/client/sso/verify_token?'.$params;
        $result = httpRequest($verifyUrl);
        if (!$result = json_decode($result, true)) {
            $this->error('请求验证sso令牌失败', $this->loginUrl);
        }
        if ($result['status'] != 1) {
            $this->error($result['msg'], $this->loginUrl);
        }
    }

    private function redirectSso()
    {
        $params = http_build_query([
            'service_domain' => $this->app['domain'],
            'app_domain' => $this->saas['main_domain'],
            'redirect' => urlencode($this->loginUrl),
        ]);
        $this->redirect('http://'.$this->saas['saas_domain'].'/client/sso/check_login?'.$params);
    }

    /**
     * admin单点登录
     */
    public function ssoAdmin($username, $password)
    {
        if (!$this->isSaas()) {
            return;
        }

        $condition['a.admin_id'] = 1;
        $condition['a.user_name'] = $username;
        $admin = Db::name('admin')->alias('a')->join('__ADMIN_ROLE__ ar', 'a.role_id=ar.role_id')->where($condition)->find();
        if (!$admin) {
            return;
        }

        $params = http_build_query([
            'service_domain' => $this->app['domain'],
            'app_domain' => $this->saas['main_domain'],
            'redirect' => urlencode($this->loginUrl),
            'password' => encrypt($password),
        ]);
        ajaxReturn(['status' => 1, 'url' => 'http://'.$this->saas['saas_domain'].'/client/sso/login?'.$params]);
    }

    private function getSsoTokenKey()
    {
        return 'sso_token';//一个应用子站只有一个ssoToken,因为只有一个admin
    }

    public function ssoLogout($ssoToken)
    {
        if (!$ssoToken) {
            return ['status' => -1, 'msg' => '令牌为空'];
        }

        $key = $this->getSsoTokenKey();
        $config = Cache::get($key);
        if (!$config) {
            return ['status' => 1, 'msg' => '子站没有令牌'];
        }
        if ($config['sso_token'] !== $ssoToken) {
            return ['status' => -1, 'msg' => '令牌不正确'];
        }

        if ($config['session_id']) {
            session_id($config['session_id']);
            Session::start();
            Session::clear();
            session_unset();
            session_destroy();
        }

        Cache::rm($key);

        return ['status' => 1, 'msg' => '登出成功'];
    }

    public function handleLogout($adminId)
    {
        if (!$this->isSaas() || $adminId != 1) {
            return;
        }

        $key = $this->getSsoTokenKey();
        if (!$config = Cache::get($key)) {
            return;
        }
        Cache::rm($key);

        if ($config['sso_token']) {
            $logoutUrl = 'http://'.$this->saas['saas_domain'].'/client/sso/logout?sso_token='.$config['sso_token'];
            $result = httpRequest($logoutUrl);
            if (!$result = json_decode($result, true)) {
                $this->error('saas平台退出失败');
            }
            if ($result['status'] != 1) {
                $this->error($result['msg']);
            }
        }
    }

    public function initSaas()
    {
        if (!$this->isSaas()) {
            return;
        }

        $database = Config::get('database');
        $saas = $this->app;
        Config::set('database', array_merge($database, $saas['database'] ?: []));
        Config::set('session.prefix', 'tp_'.$saas['domain']);
        Config::set('cookie.prefix', 'tp_'.$saas['domain']);

        $this->checkPrivilege();
    }

    /**
     * 检查是否购买模块
     */
    private function checkPrivilege()
    {
        if (!$this->isNormalUser()) {
            return;
        }

        $module     = request()->module();
        $controller = request()->controller();
        $action     = request()->action();

        if ($controller == 'Sso') {
            return;
        }
        if($module == 'admin' && ($action != 'index' || $action != 'admin')){
            $right_search = $controller .'@'.$action;
            $system_menu = Db::name('system_menu')->whereOr('right', 'LIKE', '%,' . $right_search)->whereOr('right', 'LIKE', $right_search.',%')->find();
            if($system_menu){
                $app_service = AppService::get($this->app['service_id']);
                $system_menu_price = SystemMenuPrice::get(['system_menu_id' => $system_menu['id'],'app_id'=>$app_service['app_id']]);
                if($system_menu_price){
                    $module_order = ModuleOrder::get(['user_id' => $app_service['user_id'], 'app_service_id' => $app_service['service_id'], 'status' => 1]);
                    if(!$module_order){
                        $this->exportError('未开通' . $system_menu['name'] . '模块,请联系客服!', U('admin/index/welcome'));
                    }
                }
            }
        }
    }

    /**
     * 输出错误信息
     * @param $msg
     * @param null $url
     */
    private function exportError($msg, $url = null)
    {
        if(request()->isAjax()){
            exit(json_encode(['status'=>-1,'msg'=>$msg]));
        }else{
           $this->error($msg, $url);
        }
    }
}