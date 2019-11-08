<?php

namespace app\admin\controller;

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
 * 控制器
 */
class Link extends AdminControl {

    public function _initialize() {
        parent::_initialize();
        Lang::load(APP_PATH . 'admin/lang/'.config('default_lang').'/link.lang.php');
    }

    public function index() {
        $condition = array();
        $link_title = input('get.link_title');
        if ($link_title) {
            $condition['link_title'] = ['like', "%$link_title%"];
        }
        $link_model = model('link');
        $link_list = $link_model->getLinkList($condition, 10);

        $this->assign('link_list', $link_list);
        $this->assign('show_page', $link_model->page_info->render());
        
        $this->assign('filtered', $condition ? 1 : 0); //是否有查询条件
        $this->setAdminCurItem('index');
        return $this->fetch('');
    }

    /**
     * 新增友情链接
     * */
    public function add() {
        if (!(request()->isPost())) {
            $link = [
                'link_id' => '',
                'link_title' => '',
                'link_pic' => '',
                'link_url' => '',
                'link_sort' => 255,
            ];
            $this->assign('link', $link);
            return $this->fetch('form');
        } else {
            //上传图片
            $link_pic = '';
            if ($_FILES['link_pic']['name'] != '') {
                $file = request()->file('link_pic');
                $file_name = date('YmdHis') . rand(10000, 99999);
                $upload_file = BASE_UPLOAD_PATH . DS . DIR_ADMIN . DS . 'link';
                $result = $file->validate(['ext' => ALLOW_IMG_EXT])->move($upload_file, $file_name);
                if ($result) {
                    $link_pic = $result->getFilename();
                }
            }

            $data = array(
                'link_title' => input('post.link_title'),
                'link_pic' => $link_pic,
                'link_url' => input('post.link_url'),
                'link_sort' => input('post.link_sort'),
            );
            $link_validate = validate('link');
            if (!$link_validate->scene('add')->check($data)) {
                $this->error($link_validate->getError());
            }

            $result = model('link')->addLink($data);
            if ($result) {
                dsLayerOpenSuccess(lang('ds_common_save_succ'),url('Link/index'));
            } else {
                $this->error(lang('ds_common_save_fail'));
            }
        }
    }

    /**
     * 编辑友情链接
     * */
    public function edit() {
        $link_id = input('param.link_id');
        if (empty($link_id)) {
            $this->error(lang('param_error'));
        }
        $link = model('link')->getOneLink($link_id);
        if (!request()->isPost()) {
            $this->assign('link', $link);
            return $this->fetch('form');
        } else {
            $data = array(
                'link_title' => input('post.link_title'),
                'link_sort' => input('post.link_sort'),
                'link_url' => input('post.link_url'),
            );
            //上传图片
            if ($_FILES['link_pic']['name'] != '') {
                $file = request()->file('link_pic');
                $file_name = date('YmdHis') . rand(10000, 99999);
                $upload_file = BASE_UPLOAD_PATH . DS . DIR_ADMIN . DS . 'link';
                $result = $file->validate(['ext' => ALLOW_IMG_EXT])->move($upload_file, $file_name);
                if ($result) {
                    $data['link_pic'] = $result->getFilename();
                    //删除原有友情链接图片
                    @unlink($upload_file . DS . $link['link_pic']);
                }
            }

            $link_validate = validate('link');
            if (!$link_validate->scene('edit')->check($data)) {
                $this->error($link_validate->getError());
            }

            $result = model('link')->editLink($data, $link_id);
            if ($result>=0) {
                dsLayerOpenSuccess(lang('ds_common_save_succ'),url('Link/index'));
            } else {
                $this->error(lang('ds_common_save_fail'));
            }
        }
    }

    public function drop() {
        $link_id = intval(input('param.link_id'));
        if (empty($link_id)) {
            $this->error(lang('param_error'));
        }
        $result = model('link')->delLink($link_id);
        if ($result) {
            ds_json_encode(10000, lang('ds_common_op_succ'));
        } else {
            ds_json_encode(10001, lang('ds_common_op_fail'));
        }
    }

    /**
     * ajax操作
     */
    public function ajax() {
        $result = -1;
        switch (input('get.branch')) {
            case 'link':
                $model_link = model('link');
                $link_id = intval(input('get.id'));
                $update_array = array();
                $update_array[input('get.column')] = trim(input('get.value'));
                $result = $model_link->editLink($update_array, $link_id);
                break;
        }
        if ($result >= 0) {
            echo 'true';
        }
    }

    /**
     * 获取卖家栏目列表,针对控制器下的栏目
     */
    protected function getAdminItemList() {
        $menu_array = array(
            array(
                'name' => 'index',
                'text' => lang('ds_manage'),
                'url' => url('Link/index')
            ),
            array(
                'name' => 'add',
                'text' => lang('ds_add'),
                'url' => "javascript:dsLayerOpen('".url('Link/add')."','".lang('ds_add')."')"
            )
        );
        return $menu_array;
    }

}
