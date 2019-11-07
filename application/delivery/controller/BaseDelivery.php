<?php

/**
 * 自提点公共类
 */


namespace app\delivery\controller;

use think\Controller;

class BaseDelivery extends Controller
{
    public function _initialize()
    {
        parent::_initialize();
        $this->assign('html_title','自提服务站      ' . config('site_name') . '');
        $this->assign('seo_keywords','');
        $this->assign('seo_description','');
    }
    
    
}



?>
