<?php

/**
 * tpshop
 * ============================================================================
 * 版权所有 2015-2027 深圳搜豹网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.tp-shop.cn
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用 .
 * 不允许对程序代码以任何形式任何目的的再发布。
 * 采用最新Thinkphp5助手函数特性实现单字母函数M D U等简写方式
 * ============================================================================
 * Author: 当燃
 * Date: 2015-09-09
 */

namespace app\supplier\controller;

use think\Controller;
use think\Db;
use think\Session;

class Base extends Controller
{

    public $begin;
    public $end;
    public $page_size = 0;
    public $supplier;

    /**
     * 析构函数
     */
    function __construct()
    {
        Session::start();
        header("Cache-control: private");  // history.back返回后输入框值丢失问题 参考文章 http://www.tp-shop.cn/article_id_1465.html  http://blog.csdn.net/qinchaoguang123456/article/details/29852881
        parent::__construct();
    }

    /**
     * 初始化操作
     */
    public function _initialize()
    {
        //过滤不需要登陆的行为
        if (!in_array(ACTION_NAME, array('login','logon' ,'verify'))) {
            if (session('suppliers_id') <= 0) {
                (ACTION_NAME == 'index') && $this->redirect(U('Supplier/Admin/login'));
                $this->error('请先登录', U('Supplier/Admin/login'), null, 1);
            }
        }
        $this->public_assign();
    }

    /**
     * 保存公告变量到 smarty中 比如 导航
     */
    public function public_assign()
    {
        $this->supplier = session('supplier');
        $tpshop_config = array();
        $tp_config = M('config')->cache(true)->select();
        foreach ($tp_config as $k => $v) {
            $tpshop_config[$v['inc_type'] . '_' . $v['name']] = $v['value'];
        }
        if (I('start_time')) {
            $begin = $begin = I('start_time');
            $end = I('end_time');
        } else {
            $begin = date('Y-m-d', strtotime("-3 month"));//30天前
            $end = date('Y-m-d', strtotime('+1 days'));
        }
        $this->assign('start_time', $begin);
        $this->assign('end_time', $end);
        $this->begin = strtotime($begin);
        $this->end = strtotime($end) + 86399;
        $this->page_size = C('PAGESIZE');
        $this->assign('tpshop_config', $tpshop_config);
    }


    public function ajaxReturn($data, $type = 'json')
    {
        exit(json_encode($data));
    }
}