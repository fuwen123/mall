<?php
/**
 * tpshop
 * ============================================================================
 * * 版权所有 2015-2027 深圳搜豹网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.tp-shop.cn
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用 .
 * 不允许对程序代码以任何形式任何目的的再发布。
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * 采用最新Thinkphp5助手函数特性实现单字母函数M D U等简写方式
 * ============================================================================
 * $Author: IT宇宙人 2015-08-10 $
 */
namespace app\mobile\controller;



use app\common\logic\bargain\BargainLogic;
use app\common\model\BargainFirst;
use app\common\model\PromotionBargain;

class Bargain extends MobileBase
{
    public $user_id = 0;
    public $user = array();

    /**
     * 构造函数
     */
    public function  __construct()
    {
        parent::__construct();
        if (session('?user')) {
            $user = session('user');
            $user = db('users')->where("user_id", $user['user_id'])->find();
            session('user', $user);  //覆盖session 中的 user
            $this->user = $user;
            $this->user_id = $user['user_id'];
            $this->assign('user', $user); //存储用户信息
        }
    }

    /**
     *  砍价页面
     * @return mixed
     */
    public function index()
    {
        $id = input('id');
        $BargainLogic = new BargainLogic();
        $BargainLogic->setBargainFirstId($id);
        $BargainLogic->setUserId($this->user_id);
        $data = $BargainLogic->showBargain();
        if($data['status'] == 0){
            $this->error('活动已结束');
        }
        $this->assign('data', $data['result']); //信息
        return $this->fetch();
    }

    function bargain_list()
    {
        return $this->fetch();
    }

    function order_list()
    {
        return $this->fetch();
    }

}