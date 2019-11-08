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

namespace app\admin\controller;

use think\Page;

class BespeakTemplate extends Base
{

    public function _initialize()
    {
        parent::_initialize();
    }


    /**
     * 预约模板列表
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $temlate = new \app\common\model\BespeakTemplate();
        $count = $temlate->count();
        $Page = new Page($count, 10);
        $list = $temlate->limit($Page->firstRow . ',' . $Page->listRows)->order(['add_time' => 'desc'])->select();
        $this->assign('page', $Page);
        $this->assign('list', $list);
        return $this->fetch();
    }


    /**
     * 添加预约模板
     */
    public function add()
    {
        if (IS_POST) {
            $post = input('');
            $BespeakTemplate = new \app\common\logic\BespeakTemplate();
            $BespeakTemplate->setTemplateInfo($post);
            $msg = $BespeakTemplate->templateAdd();
            $this->ajaxReturn($msg);
        }
        return $this->fetch();
    }


    /**
     * 修改预约模板
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function edit()
    {
        $post = input('');
        if (IS_POST) {
            $BespeakTemplate = new \app\common\logic\BespeakTemplate();
            $BespeakTemplate->setTemplateInfo($post);
            $msg = $BespeakTemplate->templateEdit();
            $this->ajaxReturn($msg);
        }
        $BespeakTemplateModel = new \app\common\model\BespeakTemplate();
        $find = $BespeakTemplateModel->where(['template_id' => $post['template_id']])->find();
        $this->assign('list', $find);
        return $this->fetch('add');
    }

    /**
     * 删除预约模板
     * @return mixed
     */
    public function delete()
    {
        $post = input('');
        if (IS_POST) {
            $BespeakTemplate = new \app\common\logic\BespeakTemplate();
            $BespeakTemplate->setTemplateInfo($post);
            $msg = $BespeakTemplate->deleteTemplate();
            $this->ajaxReturn($msg);
        }
        return $this->fetch();
    }

    /**
     * 关闭预约模板
     * @return mixed
     */
    public function closeTemplate(){
        $BespeakTemplate = new \app\common\model\BespeakTemplate();
        $BespeakTemplate = $BespeakTemplate->find(['template_id'=>input('template_id')]);
        if($BespeakTemplate['deleted']){
            $find = \app\common\model\Goods::get(['bespeak_template_id'=>input('template_id')]);
            if(count($find)){
                $this->ajaxReturn(['status' => 0,'msg' =>'还有商品在用，不能关闭模板']);
            }
            $BespeakTemplate->save(['deleted'=>0,'template_id'=>input('template_id')]);
        }else{
            $BespeakTemplate->save(['deleted'=>1,'template_id'=>input('template_id')]);
        }
        $this->ajaxReturn(['status' => 1,'msg' =>'操作成功']);
    }

    public function preview(){
        return $this->fetch();
    }



}
