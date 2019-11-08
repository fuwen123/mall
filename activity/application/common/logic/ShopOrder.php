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
 * Author: lhb
 * Date: 2017-05-15
 */

namespace app\common\logic;

use app\common\model\Goods;
use app\common\util\TpshopException;
use think\Model;
use think\Db;

/**
 * 自提订单类
 */
class ShopOrder
{
    private $shopOrder;
    private $shopper;
    private $code;
    private $shop_order_id;

    public function __construct()
    {
        $this->shopOrder = new \app\common\model\ShopOrder();
    }

    public function setShopOrderModel($shopOrder)
    {
        $this->shopOrder = $shopOrder;
    }



    public function getShopOrderModel()
    {
        return $this->shopOrder;
    }


    public function setShopOrderByBarCode($bar_code){
        $this->shopOrder = \app\common\model\ShopOrder::getByBarCode($bar_code);
    }

    public function setShopDatarModel($shopData)
    {
        $code =  array();$shop_order_id = array();
        foreach ($shopData as $k=>$v){
            $code[$k] =  substr($v,0,5);
            $shop_order_id[$k] =  substr($v,5);
        }
        $this->code = $code;
        $this->shop_order_id = $shop_order_id;
    }

    public function setShopOrderById($shop_order_id){
        $this->shopOrder = \app\common\model\ShopOrder::get($shop_order_id);
    }

   
    public function setShopper($shopper)
    {
        $this->shopper = $shopper;
    }

    public function writeOff()
    {
        if (empty($this->shopOrder)) {
            throw new TpshopException("自提订单核销", 0, ['status' => 0, 'msg' => '核销码错误']);
        }
        if(!empty($this->shopper)){
            if ($this->shopOrder['shop_id'] != $this->shopper['shop_id']) {
                throw new TpshopException("自提订单核销", 0, ['status' => 0, 'msg' => '订单不属于本门店,不容许核销']);
            }
        }
        if ($this->shopOrder['is_write_off'] == 1) {
            throw new TpshopException("自提订单核销", 0, ['status' => 0, 'msg' => '该订单已核销']);
        }
        $order = $this->shopOrder->order;
        if ($order['shipping_status'] == 1) {
            throw new TpshopException("自提订单核销", 0, ['status' => 0, 'msg' => '该订单已发货']);
        }
        $orderLogic = new Order();
        $orderLogic->setOrderModel($order);
        $orderLogic->deliveryConfirm();
        $orderLogic->orderActionLog('自提订单核销', config('CONVERT_ACTION.delivery_confirm'));
        $this->shopOrder->data(['is_write_off' => 1, 'write_off_time' => time()], true)->save();
        //自提点发货后修改商品发货状态，作用于数据统计
        db('order_goods')->where(['order_id'=>$order['order_id']])->save(['is_send'=>1]);
        //$goods = Db::name('goods')->where('goods_id',$this->shopOrder['goods_id'])->field('goods_name,original_img')->find();
        $send_data = [
            'message_title' => '商品待评价',
            'message_content' => '自提订单商品待评价',
            //'img_uri' => $goods['original_img'],
            'order_sn' => $order['order_sn'],
            'order_id' => $order['order_id'],
            'mmt_code' => 'evaluate_logistics',
            'type' => 4,
            'users' => [$order['user_id']],
            'category' => 2,
            'message_val' => []
        ];
        $messageFactory = new \app\common\logic\MessageFactory();
        $messageLogic = $messageFactory->makeModule($send_data);
        $messageLogic->sendMessage();   //发送待评价信息
    }
	
	public function getShippingStatus($value)
    {
        $status = [0=>'未发货',1=>'已发货'];
        return $status[$value];
    }
	
	public function getDistributionMode($value)
    {
		if($value > 0){
        	$status = '上门自提';
        }else{
        	$status = '快递配送';
		}
		return $status;
    }
}