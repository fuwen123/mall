<?php
/**
 * tpshop
 * ============================================================================
 * 版权所有 2015-2027 深圳搜豹网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.tp-shop.cn
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用 .
 * 不允许对程序代码以任何形式任何目的的再发布。
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ============================================================================
 * Author: yhj
 * Date: 2018-6-27
 */

namespace app\common\logic;


use app\common\model\BespeakTemplateUnit;
use app\common\model\Goods;
use think\Db;

/**
 * 预约模板类
 * Class CatsLogic
 * @package app\common\logic
 */
class BespeakTemplate
{

    public $templateInfo;

    /**
     * Message constructor.
     * @param int $userId
     */
    public function __construct($userId = 0)
    {

    }

    public function setTemplateInfo($value)
    {
        $this->templateInfo = $value;
    }


    /**
     * 添加模板
     * @return array
     */
    public function templateAdd()
    {
        $data = $this->templateInfo['template'];
        $BespeakTemplateModel = new \app\common\model\BespeakTemplate();
        $find = $BespeakTemplateModel::get(['name' => $data['name']]);
        if ($find) {
            return ['status' => 0, 'msg' => '该模板名称已存在'];
        }
        $BespeakTemplateModel = new \app\common\model\BespeakTemplate();

        $data['add_time'] = time();
        $data['desc'] = $data['desc'] ? $data['desc'] : '';

        if ($BespeakTemplateModel->save($data)) {
            //插入组件
            $this->addForm($BespeakTemplateModel->template_id);
            return ['status' => 1, 'msg' => '操作成功','data'=>['template_id'=>$BespeakTemplateModel->template_id], 'url' => U('Admin/BespeakTemplate/index')];
        }
        return ['status' => 0, 'msg' => '操作失败'];
    }


    /**
     * 修改模板
     * @return array
     * @throws \think\exception\DbException
     */
    public function templateEdit()
    {
        $data = $this->templateInfo['template'];
        $find = \app\common\model\Goods::get(['bespeak_template_id'=> $this->templateInfo['template_id']]);
        if(count($find)){
            return['status' => 0,'msg' =>'该模板已被使用，不能编辑'];
        }
        $BespeakTemplateModel = new \app\common\model\BespeakTemplate();
        $find = $BespeakTemplateModel::get(['name' => $data['name'], 'template_id' => ['neq', $this->templateInfo['template_id']]]);
        if ($find) {
            return ['status' => 0, 'msg' => '该模板名称已存在'];
        }
        $data['template_id'] = $this->templateInfo['template_id'];
        $BespeakTemplateModel = new \app\common\model\BespeakTemplate();
        $find = $BespeakTemplateModel::get($data['template_id']);
        if (!$find) {
            return ['status' => 0, 'msg' => '模板不存在'];
        }
        if ($find->isUpdate(true)->save($data) !== false) {
            $this->addForm($data['template_id']);
            return ['status' => 1, 'msg' => '操作成功', 'url' => U('Admin/BespeakTemplate/index')];
        }
        return ['status' => 0, 'msg' => '操作失败'];
    }


    /**
     * 插入组件
     * @param $template_id
     * @throws \think\exception\DbException
     */
    public function addForm($template_id)
    {
        //插入组件
        if ($this->templateInfo['form']) {
            foreach ($this->templateInfo['form'] as $form_k => $form_v) {
                if ($form_v['template_unit_id']) {
                    //更新
                    $TemplateUnit = BespeakTemplateUnit::get(['template_unit_id' => $form_v['template_unit_id']]);
                } else {
                    $TemplateUnit = new BespeakTemplateUnit();
                }
                $form_v['template_id'] = $template_id;
                $TemplateUnit->save($form_v);
            }
        }
        $this->deleteForm();
    }

    /**
     * 删除组件(更新字段,虚拟删除)
     * @param $template_id
     * @throws \think\exception\DbException
     */
    public function deleteForm()
    {
        if ($this->templateInfo['delete_template']) {
            BespeakTemplateUnit::update(['deleted' => 1], ['template_unit_id' => ['in', $this->templateInfo['delete_template']]]);
        }
    }

    /**
     * 删除模板
     * @return array
     * @throws \think\exception\DbException
     */
    public function deleteTemplate()
    {
        $template_id = $this->templateInfo['template_id'];
        if ($template_id) {
            if (Goods::get(['bespeak_template_id' => $template_id])) {
                return ['status' => 0, 'msg' => '有商品再使用该模板，不能删除'];
            }
            $unit = BespeakTemplateUnit::get(['template_id'=>$template_id]);
            if ($unit['order_bespeak']['order_id']) {
                return ['status' => 0, 'msg' => '有商品再使用该模板，不能删除'];
            }
            if(!(\app\common\model\BespeakTemplate::get($template_id))){
                return ['status' => 0, 'msg' => '模板不存在'];
            }
            //没有商品在使用并且该模板没有用户数据。可以删除
            \app\common\model\BespeakTemplate::destroy($template_id);
            BespeakTemplateUnit::destroy(['template_id'=>$template_id]);
            return ['status' => 1, 'msg' => '操作成功', 'url' => U('Admin/BespeakTemplate/index')];
        }
    }

}