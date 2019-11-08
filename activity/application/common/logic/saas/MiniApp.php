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

use app\common\model\saas\UserMiniapp;
use app\common\model\saas\Users;
use app\common\util\TpshopException;
use app\common\logic\saas\wechat\MiniApp3rd;

/**
 * 微信小程序逻辑处理
 */
class MiniApp
{
    private $miniApp;

    public function __construct(\app\common\model\saas\Miniapp $mini_app)
    {
        $this->miniApp = $mini_app;
    }
    /**
     * 绑定体验者
     * @param $wechat_id
     * @throws TpshopException
     */
    public function bindTester($wechat_id)
    {
        $miniApp3rd = new MiniApp3rd($this->miniApp);
        $return = $miniApp3rd->bindTester($wechat_id);
        if ($return === false) {
            throw new TpshopException('绑定体验者', -1 ,['status'=>-1,'msg'=>$miniApp3rd->getError()]);
        }
        $testers = $this->miniApp->testers;
        if (!in_array($wechat_id, $testers)) {
            $testers[] = $wechat_id;
        }
        $this->miniApp->testers = $testers;
        $this->miniApp->save();
    }

    /**
     * 解绑体验者
     * @param $wechat_id
     * @throws TpshopException
     */
    public function unbindTester($wechat_id)
    {
        $miniApp3rd = new MiniApp3rd($this->miniApp);
        $return = $miniApp3rd->unbindTester($wechat_id);

        if ($return === false) {
            throw new TpshopException('解绑体验者', -1 ,['status'=>-1, 'msg'=>$miniApp3rd->getError()]);
        }

        $testers = $this->miniApp->testers;
        foreach ($testers as $k => $tester) {
            if ($wechat_id == $tester) {
                unset($testers[$k]);
                break;
            }
        }
        $this->miniApp->testers = $testers;
        $this->miniApp->save();
    }

    /**
     * 提交小程序模板
     * @throws TpshopException
     */
    public function commitTemplate()
    {
        $template = $this->miniApp->app_service->app->miniapp_template;
        if(empty($template)){
            throw new TpshopException('提交小程序模板', -1 ,['status'=>-1, 'msg'=>'暂无模板可使用，联系客服']);
        }
        $store_name = tpCache("shop_info.store_name");
        $store_logo = tpCache("shop_info.store_logo");
        $miniapp_domain = $this->miniApp->app_service->app['miniapp_domain'];
        $extCfg = json_encode([
            'extAppid' => $this->miniApp->appid,
            'ext' => [
                'store_name'  => $store_name,
                'store_logo'  => $store_logo,
                'request_url' => 'https://'.$miniapp_domain,
                'default_url' => $this->miniApp->app_service->app->miniapp_domain,
                'is_refactor' => 1,
                'saas_app'    => $this->miniApp->app_service->domain
            ],
        ], JSON_UNESCAPED_UNICODE);
        $miniApp3rd = new MiniApp3rd($this->miniApp);
        $version = 'v3.0.0';
        $description = 'service_id:'.$this->miniApp['service_id'];
        $return = $miniApp3rd->commit($template['miniapp_tpl_id'], $extCfg, $version, $description);
        if ($return === false) {
            throw new TpshopException('提交小程序模板', -1 ,['status'=>-1, 'msg'=>$miniApp3rd->getError()]);
        }
        $user_miniApp_data = [
            'request_url' => 'https://'.$miniapp_domain,
            'store_name'=>$store_name,
            'store_logo'=> $store_logo,
            'version'=> $version,
            'description'=> $description,
            'ext_config'=> $extCfg,
            'add_time'=> time(),
            'status'=>UserMiniapp::STATUS_TEST
        ];
        //只能有一个体验版
        $user_miniApp = UserMiniapp::get(['user_id' => $this->miniApp['user_id'], 'miniapp_id' => $this->miniApp['miniapp_id'], 'status' => UserMiniapp::STATUS_TEST]);
        if ($user_miniApp) {
            $user_miniApp->data($user_miniApp_data);
            $user_miniApp->save();
        } else {
            $user_miniApp_data['user_id'] =  $this->miniApp['user_id'];
            $user_miniApp_data['miniapp_id'] = $this->miniApp['miniapp_id'];
            UserMiniapp::create($user_miniApp_data);
        }
    }

    /**
     * 小程序审核
     * @param $data
     * @throws TpshopException
     */
    public function audit($data)
    {
        $test_mini_app = UserMiniapp::get(['user_id' => $this->miniApp['user_id'], 'miniapp_id' => $this->miniApp['miniapp_id'], 'status' => UserMiniapp::STATUS_TEST]);
        if (empty($test_mini_app)) {
            throw new TpshopException('小程序提交审核', -1 ,['status'=>-1, 'msg'=>'体验版本不存在，不能提交审核']);
        }
        $audit_mini_app = UserMiniapp::get(['miniapp_id' => $this->miniApp['miniapp_id'], 'status' => ['in', [UserMiniapp::STATUS_AUDITING, UserMiniapp::STATUS_AUDIT_DONG]]]);
        if ($audit_mini_app) {
            throw new TpshopException('小程序提交审核', -1 ,['status'=>-1, 'msg'=>'已有在审核中的版本或审核通过尚未发布的版本']);
        }
        $data_categories = explode(',', $data['categories']);
        $data_first_id = $data_categories[0];
        $data_second_id = isset($data_categories[1]) ? $data_categories[1] : '';
        $data_third_id = isset($data_categories[2]) ? $data_categories[2] : '';

        /* 检查服务类目合法性 */
        $miniApp3rd = new MiniApp3rd($this->miniApp);
        $categories = $this->miniApp->categories ?: [];
        if (!$categories) {
            $categories = $miniApp3rd->getCategory();
            if ($categories === false) {
                throw new TpshopException('小程序提交审核', -1 ,['status'=>-1, 'msg'=>$miniApp3rd->getError()]);
            }
            if (!$categories) {
                throw new TpshopException('小程序提交审核', -1 ,['status'=>-1, 'msg'=>'服务类目为空']);
            }
            $this->miniApp->categories = $categories;
            $this->miniApp->save();
        }
        $category = [];
        $is_find_category = false;
        foreach ($categories as $category) {
            $category['second_id'] = isset($category['second_id']) ? $category['second_id'] : '';
            $category['third_id'] = isset($category['third_id']) ? $category['third_id'] : '';
            if ($category['first_id'] == $data_first_id
                && $category['second_id'] == $data_second_id
                && $category['third_id'] == $data_third_id) {
                $is_find_category = true;
                break;
            }
        }
        if (!$is_find_category) {
            throw new TpshopException('小程序提交审核', -1 ,['status'=>-1, 'msg'=>'服务类目不在已设置的范围内']);
        }

        /* 提交审核的参数 */
        $itemList[0] = [
            'address'=>'pages/index/index/index', //目前只固定为首页，还没做定制,可从getPage获取
            'tag'=>$data['tag'],
            'first_class'=>$category['first_class'],
            'second_class'=> isset($category['second_class']) ? $category['second_class'] : '',
            'third_class'=> isset($category['third_class']) ? $category['third_class'] : '',
            'first_id'=> $data_first_id,
            'second_id'=> $data_second_id,
            'third_id'=> $data_third_id,
            'title'=> $data['title'],
        ];
        $auditId = $miniApp3rd->submitAudit($itemList);
        if ($auditId === false) {
            throw new TpshopException('小程序提交审核', -1 ,['status'=>-1, 'msg'=>$miniApp3rd->getError()]);
        }
        $test_mini_app->audit_id = $auditId;
        $test_mini_app->status = UserMiniapp::STATUS_AUDITING;
        $test_mini_app->audit_time = time();
        $test_mini_app->save();
    }

    /**
     * 发布小程序
     */
    public function release()
    {
        $miniApp3rd = new MiniApp3rd($this->miniApp);
        if ($miniApp3rd->release() === false) {
            throw new TpshopException('小程序提交审核', -1 ,['status'=>-1, 'msg'=>'发布小程序']);
        }

        //同步一下可访问状态，不处理错误
        $miniApp3rd->changeVisitStatus($this->miniApp->is_show_release);

        //以前上线的已无效
        UserMiniapp::update(['status' => UserMiniapp::STATUS_INVALID], [
            'miniapp_id' => $this->miniApp['miniapp_id'],
            'status' => UserMiniapp::STATUS_ON_RELEASE
        ]);

        //审核通过的改为已上线
        UserMiniapp::update(['status' => UserMiniapp::STATUS_ON_RELEASE, 'release_time' => time()], [
            'miniapp_id' =>  $this->miniApp['miniapp_id'],
            'status' => UserMiniapp::STATUS_AUDIT_DONG
        ]);
		
		  //发布上线后改为已发布
        $this->miniApp->is_release = 1;
        $this->miniApp->save(); 
		
    }

    /**
     * 更新小程序信息
     */
    public function update()
    {
        $logic = new Wx3rdLogic;
        $return = $logic->getAuthUserInfo($this->miniApp['appid']);
        if ($return['status'] == 1) {
            $this->miniApp->data($return['result']);
            $this->miniApp->save();
            $this->miniApp->user->head_img = $this->miniApp->head_img;//更新头像
        }
    }
	
	/**
     * 撤回小程序
     */
    public function back(){
        $miniApp3rd = new MiniApp3rd($this->miniApp);
        $rs = $miniApp3rd->auditBack();
        if ($rs === false) {
            throw new TpshopException('小程序撤回审核', -1 ,['status'=>-1, 'msg'=>$miniApp3rd->getError()]);
        }
        return true;
    }
	
}