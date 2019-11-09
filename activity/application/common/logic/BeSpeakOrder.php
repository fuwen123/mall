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
class BeSpeakOrder
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

    public function setShopOrderById($order_id){
        $this->shopOrder = \app\common\model\ShopOrder::where(['order_id'=>$order_id])->where('shop_order_id','in',$this->shop_order_id)->select();
    }
    public function setShopOrderFindById(){
        $this->shopOrder = \app\common\model\ShopOrder::where('shop_order_id','in',$this->shop_order_id)->select();
    }
    public function setShopper($shopper)
    {
        $this->shopper = $shopper;
    }

    public function writeOff()
    {
        if (empty($this->shopOrder)) {
            throw new TpshopException("预约订单核销", 0, ['status' => 0, 'msg' => '该核销码错误']);
        }
        $str = substr($this->shopOrder[0]['add_time'],5);
        if($str.$this->shopOrder[0]['shop_order_id'] != $this->code[0].$this->shop_order_id[0]){
            throw new TpshopException("预约订单核销", 0, ['status' => 0, 'msg' => '该核销码错误']);
        }
        if(!empty($this->shopper)){
            if ($this->shopOrder[0]['shop_id'] != $this->shopper[0]['shop_id']) {
                throw new TpshopException("预约订单核销", 0, ['status' => 0, 'msg' => '订单不属于本门店,不容许核销']);
            }
        }
        if ($this->shopOrder[0]['is_write_off'] == 1) {
            throw new TpshopException("预约订单核销", 0, ['status' => 0, 'msg' => '该核销码已核销']);
        }
        if($this->shopOrder[0]['is_write_off'] == 2){
            //if ($this->shopOrder[0]->goods['invalid_refund'] == 1) {
                throw new TpshopException("预约订单核销", 0, ['status' => 0, 'msg' => '该核销码已过期失效不能使用']);
            //}
        }

        $order = $this->shopOrder[0]->order;
        if ($order['shipping_status'] == 1) {
            throw new TpshopException("预约订单核销", 0, ['status' => 0, 'msg' => '该订单已发货']);
        }
        if (in_array($order['order_status'],[3,5])) {
            throw new TpshopException("预约订单核销", 0, ['status' => 0, 'msg' => '该订单已失效']);
        }
        if ($order['pay_status'] != 1) {
            throw new TpshopException("预约订单核销", 0, ['status' => 0, 'msg' => '该订单未付款或者已失效']);
        }
        if ($order['order_status'] == 0) {
            throw new TpshopException("预约订单核销", 0, ['status' => 0, 'msg' => '先确认订单才能发货']);
        }
        $shop_order_count = db('shopOrder')->where(['is_write_off'=>['neq',1],'order_id'=>$order->order_id])->count();
        //shopOrder表的预约数量全部弄完才去更新订单数据
        if($shop_order_count == count($this->shopOrder)){
            $orderLogic = new Order();
            $orderLogic->setOrderModel($order);
            $orderLogic->deliveryConfirm();
            $orderLogic->orderActionLog('预约订单核销', config('CONVERT_ACTION.delivery_confirm'));
            db('order_goods')->where(['order_id'=>$order->order_id])->save(['is_send'=>1]);  //把订单状态改为已收货

            $goods = Db::name('goods')->where('goods_id',$this->shopOrder[0]['goods_id'])->field('goods_name,original_img')->find();
            $send_data = [
                'message_title' => '商品待评价',
                'message_content' => $goods['goods_name'],
                'img_uri' => $goods['original_img'],
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
        foreach ($this->shopOrder as $k=>$v){
            $v->save(['is_write_off' => 1, 'write_off_time' => time()]);
        }
    }
	
	public function getShippingStatus($value)
    {
        $status = [0=>'未发货',1=>'已发货'];
        return $status[$value];
    }
	
	public function getDistributionMode($value)
    {
		if($value > 0){
        	$status = '门店预约';
        }else{
        	$status = '快递配送';
		}
		return $status;
    }

    //预约过期失效修改订单状态
        public function setInvalidRefund($value)
    {
        $goods = Goods::get($value['goods_id']);
        $order =  \app\common\model\Order::get($value['order_id']);

        //如果订单有部分支付了，自动退款
        if($order['pay_status'] == 0 && ($order['user_money'] > 0 || $order['integral'] > 0)){
            $logic = new OrderLogic();
            $logic->cancel_order($order['user_id'], $value['order_id']);
        }

        if($goods->invalid_refund ==1){
            $data['order_status']=5;
            $data['order_id']=$value['order_id'];
            \app\common\model\Order::update($data);
        }elseif ($goods->invalid_refund ==2){
           if($order['pay_status']==0){
               $data['order_status']=5;
               $data['order_id']=$value['order_id'];
               \app\common\model\Order::update($data);
           }
        }
        //如果订单有部分支付了，自动退款
        if($order['pay_status'] == 0 && ($order['user_money'] > 0 || $order['integral'] > 0)){

        }else{
            //记订单日记
            $commonOrder = new \app\common\logic\Order();
            $commonOrder->setOrderById($value['order_id']);
            $dec = '用户预约订单过期失效';
            $commonOrder->orderActionLog($dec,'订单作废',1);
        }


    }

}