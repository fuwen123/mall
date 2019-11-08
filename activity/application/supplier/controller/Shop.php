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
 * Author: 当燃
 * 拼团控制器
 * Date: 2016-06-09
 */

namespace app\shop\controller;

use think\Loader;
use think\Db;
use think\Page;

class Shop extends Base
{
    public function index()
    {
        $where = ['deleted' => 0,'shop_id'=>$this->shopper['shop_id']];
        $Shop = new \app\common\model\Shop();
        $count = $Shop->where($where)->count();
        $Page = new Page($count, 10);
        $list = $Shop->where($where)->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('page', $Page);
        $this->assign('list', $list);
        return $this->fetch();
    }

    /**
     * 门店自提点
     * @return mixed
     */
    public function info()
    {
        $shop_id = input('shop_id/d');
        if ($shop_id) {
            $Shop = new \app\common\model\Shop();
            $shop = $Shop->where(['shop_id' => $shop_id,'deleted' => 0])->find();
            if (empty($shop)) {
                $this->error('非法操作');
            }
            $city_list = Db::name('region')->where(['parent_id'=>$shop['province_id'],'level'=> 2])->select();
            $district_list = Db::name('region')->where(['parent_id'=>$shop['city_id']])->select();
            $shop_image_list = Db::name('shop_images')->where(['shop_id'=>$shop['shop_id']])->select();
            $this->assign('city_list', $city_list);
            $this->assign('district_list', $district_list);
            $this->assign('shop_image_list', $shop_image_list);
            $this->assign('shop', $shop);
        }
        $province_list = Db::name('region')->where(['parent_id'=>0,'level'=> 1])->cache(true)->select();
        $suppliers_list = Db::name("suppliers")->where(['is_check'=>1])->select();
        $this->assign('suppliers_list', $suppliers_list);
        $this->assign('province_list', $province_list);
        return $this->fetch();
    }

    public function save(){
        $data = input('post.');
        $shop_images = input('shop_images/a',[]);
        $shopValidate = Loader::validate('Shop');
        if (!$shopValidate->scene('edit')->batch()->check($data)) {
            $this->ajaxReturn(['status' => 0, 'msg' => '操作失败', 'result' => $shopValidate->getError()]);
        }
        if(count($shop_images) == 0){
            $this->ajaxReturn(['status' => 0, 'msg' => '操作失败', 'result' => ['shop_images'=>'门店照片必须']]);
        }
        $Shop = new \app\common\model\Shop();
        $shop = $Shop->where(['shop_id'=>$data['shop_id']])->find();
        if(empty($shop)){
            $this->ajaxReturn(['status' => 0, 'msg' => '非法操作', 'result' => '']);
        }
        $data['add_time'] = time();
        $shop->data($data, true);
        $row = $shop->allowField(true)->save();
        Db::name('shop_images')->where('shop_id',$shop->shop_id)->delete();
        foreach($shop_images as $image){
            if(!empty($image)){
                Db::name('shop_images')->insert(['shop_id'=>$shop->shop_id,'image_url'=>$image]);
            }
        }
        if($row !== false){
            $this->ajaxReturn(['status' => 1, 'msg' => '编辑成功', 'result' => '']);
        }else{
            $this->ajaxReturn(['status' => 0, 'msg' => '编辑失败', 'result' => '']);
        }
    }

    public function shopImageDel()
    {
        $path = input('filename','');
        Db::name('goods_images')->where("image_url",$path)->delete();
    }
}
