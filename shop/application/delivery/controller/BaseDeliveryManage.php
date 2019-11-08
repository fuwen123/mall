<?php


namespace app\delivery\controller;
use think\Lang;

class BaseDeliveryManage extends BaseDelivery {
    
    public function _initialize()
    {
        parent::_initialize();
        if (session('delivery_login')!=1) {
            $this->redirect(DELIVERY_SITE_URL.'/Login/login.html');
        }
    }
    
}
?>
