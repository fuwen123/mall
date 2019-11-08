<?php

/*
 * 自提点用户申请以及登录
 */

namespace app\delivery\controller;
use think\Validate;

class Login extends BaseDelivery {

    public function _initialize() {
        parent::_initialize();
    }

    public function login() {
        if (!request()->isPost()) {
            return $this->fetch();
        } else {
            $condition = array();
            $condition['dlyp_name'] = input('post.dname');
            $condition['dlyp_passwd'] = md5(input('post.dpasswd'));
            $dp_info = model('deliverypoint')->getDeliverypointInfo($condition);
            if (!empty($dp_info)) {
                //设置 session
                session('delivery_login', 1);
                session('dlyp_id', $dp_info['dlyp_id']);
                session('dlyp_name', $dp_info['dlyp_name']);
                $this->success('登录成功', url('Manage/index'));
            } else {
                $this->error('登录失败');
            }
        }
    }
    
    public function logout() {
        session(null);
        ds_json_encode(10000,'退出成功');
    }

    public function apply() {
        if (!request()->isPost()) {
            return $this->fetch();
        } else {
            $insert = array();
            $insert['dlyp_name'] = input('post.dname');
            $insert['dlyp_passwd'] = md5(input('post.dpasswd'));
            $insert['dlyp_truename'] = input('post.dtruename');
            $insert['dlyp_mobile'] = input('post.dmobile');
            $insert['dlyp_telephony'] = input('post.dtelephony');
            $insert['dlyp_addressname'] = input('post.daddressname');
            $insert['dlyp_area_2'] = input('post.area_id_2');
            $insert['dlyp_area_3'] = input('post.area_id');
            $insert['dlyp_area_info'] = input('post.area_info');
            $insert['dlyp_address'] = input('post.daddress');
            $insert['dlyp_idcard'] = input('post.didcard');
            $insert['dlyp_addtime'] = TIMESTAMP;
            $insert['dlyp_state'] = 10;
            
            
            //验证数据  BEGIN
            $rule = [
                ['dlyp_name', 'require', '账户为必填'],
                ['dlyp_passwd', 'require', '密码为必填'],
                ['dlyp_truename', 'require', '真实姓名为必填'],
                ['dlyp_idcard', 'require', '身份证为必填'],
            ];
            $validate = new Validate($rule);
            $validate_result = $validate->check($insert);
            if (!$validate_result) {
                ds_json_encode(10001,$validate->getError());
            }
            
            
            
            $deliverypoint_model = model('deliverypoint');
            //判断
            $dp_info = $deliverypoint_model->getDeliverypointInfo(array('dlyp_name'=>$insert['dlyp_name']));
            if($dp_info){
                ds_json_encode(10001,'自提点用户名已存在');
            }
            
            //图片上传
            $upload_file = BASE_UPLOAD_PATH . DS . ATTACH_DELIVERY;
            if (!empty($_FILES['didcardimg']['name'])) {
                $file = request()->file('didcardimg');
                $info = $file->rule('uniqid')->validate(['ext' => ALLOW_IMG_EXT])->move($upload_file);
                if ($info) {
                    $insert['dlyp_idcard_image'] = $info->getFilename();
                } else {
                    // 上传失败获取错误信息
                    ds_json_encode(10001,$file->getError());
                }
            }
            $result = $deliverypoint_model->addDeliverypoint($insert);
            
            if ($result) {
                ds_json_encode(10000,lang('操作成功，等待管理员审核'));
            } else {
                ds_json_encode(10001,lang('ds_common_op_fail'));
            }
        }
    }
    
     /**
     * ajax验证用户名是否存在
     */
    public function check() {
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

}

?>
