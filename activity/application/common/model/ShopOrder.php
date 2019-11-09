<?php
/**
 * tpshop
 * ============================================================================
 * 版权所有 2015-2027 深圳搜豹网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.tp-shop.cn
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用 .
 * 不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * Author: IT宇宙人
 * Date: 2015-09-09
 */
namespace app\common\model;

use think\Db;
use think\Model;

class ShopOrder extends Model
{
    //自定义初始化
    protected static function init()
    {
        //TODO:自定义的初始化
    }

    public function order()
    {
        return $this->hasOne('Order', 'order_id', 'order_id');
    }

    public function shop()
    {
        return $this->hasOne('Shop', 'shop_id', 'shop_id');
    }
    public function goods()
    {
        return $this->hasOne('Goods', 'goods_id', 'goods_id');
    }

    public function getGoodsDecAttr($value, $data)
    {
        $dec = '';
        switch ($data['goods']['invalid_refund']){
            case 1:
//                $dec = '已过期失效（不能使用）';
                $dec = '不能使用';
                break;
            case 2:
//                $dec = '已过期（可退款）';
                $dec = '可退款';
                break;
        }
        return  $dec;
    }

    public function getShopDecAttr($value, $data)
    {
        $dec = '';
        switch ($data['is_write_off']){
            case 0:
                $dec = '未使用';
                break;
            case 1:
                $dec = '已使用';
                break;
            case 2:
                $dec = '已过期';
                break;
        }
        return  $dec;
    }

    //设置核销码
    public function getShopCodeAttr($value, $data)
    {
        $str = substr($data['add_time'],5);
        return $str.$data['shop_order_id'];
    }
}
