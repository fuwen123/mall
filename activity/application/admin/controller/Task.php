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
 * Date: 2015-09-21
 */

namespace app\admin\controller;
use think\Db;
use think\Page;
use app\admin\logic\GoodsLogic;
use app\common\model\Goods;
use app\common\model\Users;
use app\admin\controller\System;

class Task extends Base{

    /**
    *创建.bat文件和php脚本
    */
    public function addTask() {

        $server_name = $_SERVER['SERVER_NAME'];
        $php_path = $_SERVER['PHPRC'];
        $document_path = $_SERVER['DOCUMENT_ROOT'];
        
        $content = "<?php"."\r\n"."file_get_contents('http://$server_name/admin/Task/toSystemLoginTask');";
        file_put_contents($document_path.'/task.php', $content);

        $code = "\r\n" ."$php_path"."php.exe "."$document_path"."/task.php";
        file_put_contents('D:/task.bat', $code);

        //生成 发货超时单--短信通知bat文件
        $delivery_timeout_code = "\r\n cd " ."$document_path"."\r\n php think delivery_timeout";
        file_put_contents('D:/delivery_timeout.bat', $delivery_timeout_code);
    }

    public function toSystemLoginTask() {

        $ip = $_SERVER["REMOTE_ADDR"];
        $REMOTE_HOST = $_SERVER['SERVER_ADDR'];

        if ($ip == '127.0.0.1' && $REMOTE_HOST == '127.0.0.1') {
            //调用管理员登录后 处理相关操作
            $System = new System();
            $System->login_task();
        }

    }
}