<?php

/*
 * 自提点管理中心
 */

namespace app\delivery\controller;

class Manage extends BaseDeliveryManage {

    public function _initialize() {
        parent::_initialize();
    }
    /**
     * ajax验证用户名是否存在
     */
    public function check_delivery() {
        $conditon = array();
        $dlyp_id = intval(input('get.did'));
        if ($dlyp_id <= 0) {
            echo 'false';die;
        }
        $conditon['dlyp_id'] = array('neq', $dlyp_id);
        
        $dlyp_name = input('get.dname');
        if(empty($dlyp_name)){
            echo 'false';die;
        }
        $conditon['dlyp_name'] = $dlyp_name;
        $dp_info = model('deliverypoint')->getDeliverypointInfo($conditon);
        if (empty($dp_info)) {
            echo 'true';die;
        } else {
            echo 'false';die;
        }
    }
    public function apply_again() {
        if (!request()->isPost()) {
            return $this->fetch();
        } else {
            $dlyp_id = input('post.did');
            if ($dlyp_id <= 0) {
                ds_json_encode(10001,lang('ds_common_op_fail'));
            }
            $update = array();
            $update['dlyp_name'] = input('post.dname');
            $update['dlyp_passwd'] = md5(input('post.dpasswd'));
            $update['dlyp_truename'] = input('post.dtruename');
            $update['dlyp_mobile'] = input('post.dmobile');
            $update['dlyp_telephony'] = input('post.dtelephony');
            $update['dlyp_address_name'] = input('post.daddressname');
            $update['dlyp_area_2'] = input('post.area_id_2');
            $update['dlyp_area_3'] = input('post.area_id');
            $update['dlyp_area_info'] = input('post.area_info');
            $update['dlyp_address'] = input('post.daddress');
            $update['dlyp_idcard'] = input('post.didcard');
            $update['dlyp_addtime'] = TIMESTAMP;
            $update['dlyp_state'] = 10;
            $update['dlyp_failreason'] = '';
            
            
            if (!empty($_FILES['didcardimg']['name'])) {
                $upload_file = BASE_UPLOAD_PATH . DS . ATTACH_DELIVERY;
                $file = request()->file('didcardimg');
                $info = $file->rule('uniqid')->validate(['ext' => ALLOW_IMG_EXT])->move($upload_file);
                if ($info) {
                    $update['dlyp_idcard_image'] = $info->getFilename();
                }else{
                    ds_json_encode(10001,$file->getError());
                }
            }
            
            $result = model('deliverypoint')->editDeliverypoint($update, array('dlyp_id' => $dlyp_id));
            if ($result) {
                ds_json_encode(10000,'操作成功，等待管理员审核');
            } else {
                ds_json_encode(10001,lang('ds_common_op_fail'));
            }
        }
    }

    /**
     * 操作中心
     */
    public function index() {
        $dorder_model = model('deliveryorder');
        $condition = array();
        $condition['dlyp_id'] = session('dlyp_id');
        $search_name = input('get.search_name');
        if ($search_name != '') {
            $condition['order_sn|shipping_code|reciver_mobphone'] = array('like', '%' . $search_name . '%');
            $this->assign('search_name', $search_name);
        }
        if (input('get.hidden_success') == 1) {
            $dorder_list = $dorder_model->getDeliveryorderDefaultAndArriveList($condition, '*', 10);
        } else {
            $dorder_list = $dorder_model->getDeliveryorderList($condition, '*', 10);
        }
        $this->assign('dorder_list', $dorder_list);
        $this->assign('show_page', $dorder_model->page_info->render());

        $dorder_state = $dorder_model->getDeliveryorderState();
        $this->assign('dorder_state', $dorder_state);
        return $this->fetch();
    }

    /**
     * 详细资料
     */
    public function information() {
        $deliverypoint_model = model('deliverypoint');
        $delivery_info = $deliverypoint_model->getDeliverypointInfo(array('dlyp_id' => session('dlyp_id')));
        $this->assign('delivery_info', $delivery_info);
        $this->assign('delivery_state', $deliverypoint_model->getDeliveryState());
        return $this->fetch();
    }

    /**
     * 修改密码
     */
    public function change_password() {
        if (!request()->isPost()) {
            return $this->fetch();
        } else {
            if (input('post.password') != input('post.passwd_confirm')) {
                ds_json_encode(10001,'新密码与确认密码填写不同');
            }
            $deliverypoint_model = model('deliverypoint');
            $condition = array();
            $condition['dlyp_id'] = session('dlyp_id');
            $condition['dlyp_passwd'] = md5(input('post.old_password'));
            $dp_info = $deliverypoint_model->getDeliverypointInfo($condition);
            if (empty($dp_info)) {
                ds_json_encode(10001,'原密码填写错误');
            }
            $deliverypoint_model->editDeliverypoint(array('dlyp_passwd' => md5(input('post.password'))), $condition);
            session(null);
            ds_json_encode(10000,'修改成功');
        }
    }

    /**
     * 查看物流
     */
    public function get_express() {
        return $this->fetch();
    }

    /**
     * 从第三方取快递信息
     */
    public function ajax_get_express() {
        $result = model('express')->queryExpress(input('param.express_code'),input('param.shipping_code'));
        if ($result['Success'] != true){
            exit(json_encode(false));
        }
        $content['Traces'] = array_reverse($result['Traces']);
        $output = array();
        if (is_array($content['Traces'])) {
            foreach ($content['Traces'] as $k => $v) {
                if ($v['AcceptTime'] == '')
                    continue;
                $output[] = $v['AcceptTime'] . '&nbsp;&nbsp;' . $v['AcceptStation'];
            }
        }
        if (empty($output))
            exit(json_encode(false));
        echo json_encode($output);
    }

    /**
     * 取件通知
     */
    public function arrive_point() {
        $order_id = intval(input('get.order_id'));
        if ($order_id <= 0) {
            ds_json_encode(10001,lang('param_error'));
        }
        $pickup_code = $this->createPickupCode();
        // 更新提货订单表数据
        $update = array();
        $update['dlyo_pickup_code'] = $pickup_code;
        model('deliveryorder')->editDeliveryorderArrive($update, array('order_id' => $order_id, 'dlyp_id' => session('dlyp_id')));
        // 更新订单扩展表数据
        model('order')->editOrdercommon($update, array('order_id' => $order_id));
        // 发送短信提醒
        \mall\queue\QueueClient::push('sendPickupcode', array('pickup_code' => $pickup_code, 'order_id' => $order_id));
        ds_json_encode(10000,'操作成功');
    }

    /**
     * 提货验证
     */
    public function pickup_parcel() {
        if (!request()->isPost()) {
            return $this->fetch();
        }else{
            $order_id = intval(input('post.order_id'));
            $pickup_code = intval(input('post.pickup_code'));
            if ($order_id <= 0 || $pickup_code <= 0) {
                ds_json_encode(10001,lang('param_error'));
            }
            $dorder_model = model('deliveryorder');
            $dorder_info = $dorder_model->getDeliveryorderInfo(array('order_id' => $order_id, 'dlyp_id' => session('dlyp_id'), 'dlyo_pickup_code' => $pickup_code));
            if (empty($dorder_info)) {
                ds_json_encode(10001,'提货码错误');
            }
            $result = $dorder_model->editDeliveryorderPickup(array(), array('order_id' => $order_id, 'dlyp_id' => session('dlyp_id'), 'dlyo_pickup_code' => $pickup_code));
            if ($result) {
                // 更新订单状态
                $order_info = model('order')->getOrderInfo(array('order_id' => $order_id));
                model('order','logic')->changeOrderStateReceive($order_info, 'buyer', '自提服务站', '自提服务站确认收货');
                ds_json_encode(10000,'操作成功，订单完成');
            } else {
                ds_json_encode(10001,'操作失败');
            }
        }
    }

    /**
     * 生成提货码
     */
    private function createPickupCode() {
        return rand(1, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9);
    }

}
