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
 * Author: dyr
 * Date: 2017-12-04
 */

namespace app\common\logic;

use app\common\model\BespeakTemplateUnit;
use app\common\model\CouponList;
use app\common\model\Goods;
use app\common\model\Order;
use app\common\model\OrderBespeak;
use app\common\model\OrderGoods;
use app\common\model\PreSell;
use app\common\model\ShopOrder;
use app\common\model\TeamActivity;
use app\common\logic\team\Team;
use app\common\model\Users;
use app\common\util\TpshopException;
use think\Cache;
use think\Hook;
use think\Model;
use think\Db;
/**
 * 提交下单类
 * Class CatsLogic
 * @package Home\Logic
 */
class PlaceOrder
{
    private $invoiceTitle;
    private $userNote;
    private $taxpayer;
    private $invoiceDesc;
    private $pay;
    private $order;
    private $userAddress;
    private $payPsw;
    private $promType;
    private $promId;
    private $consignee;
    private $mobile;
    private $shop;
    private $take_time;
    private $preSell;
    private $bespeakForm;

    /**
     * PlaceOrder constructor.
     * @param Pay $pay
     */
    public function __construct(Pay $pay)
    {
        $this->pay = $pay;
        $this->order = new Order();
    }

    public function addNormalOrder()
    {
        $this->check();
        $this->queueInc();
        $this->addOrder();
        $this->addOrderGoods();
        $this->addShopOrder();
        $this->addOrderBespeak();
        Hook::listen('user_add_order', $this->order);//下单行为
        $reduce = tpCache('shopping.reduce');
        if($reduce== 1 || empty($reduce)){
            minus_stock($this->order);//下单减库存
        }
        // 如果应付金额为0  可能是余额支付 + 积分 + 优惠券 这里订单支付状态直接变成已支付
        if ($this->order['order_amount'] == 0) {
            update_pay_status($this->order['order_sn']);
        }
        $this->deductionCoupon();//扣除优惠券
        $this->changUserPointMoney($this->order);//扣除用户积分余额
        $this->queueDec();
    }
    
    public function addTeamOrder(Team $team)
    {
        $this->setPromType(6);
        $teamActivity = $team->getTeamActivity();
        $teamFoundId = $team->getFoundId();
        if($teamFoundId){
            $team_found_queue = Cache::get('team_found_queue');
            if($team_found_queue[$teamFoundId] <= 0){
                $team_found_queue[$teamFoundId] = $team->getTeamFollowNum(); // 再次获取已参团人数
                if($team_found_queue[$teamFoundId] <= 0){
                    throw new TpshopException('提交订单', 0, ['status' => -1, 'msg' => '当前人数过多请耐心排队!',
                        'result' => '',
                      ]);
                }
            }
            $team_found_queue[$teamFoundId] = $team_found_queue[$teamFoundId] - 1;
            Cache::set('team_found_queue', $team_found_queue);
        }
        $this->setPromId($teamActivity['team_id']);
        $this->check();
        $this->queueInc();
        $this->addOrder();
        $this->addOrderGoods();
        Hook::listen('user_add_order', $this->order);//下单行为
        if($teamActivity['team_type'] != 2){
            if(tpCache('shopping.reduce') == 1){
                minus_stock($this->order);//下单减库存
            }
        }
        // 如果应付金额为0  可能是余额支付 + 积分 + 优惠券 这里订单支付状态直接变成已支付
        if ($this->order['order_amount'] == 0) {
            update_pay_status($this->order['order_sn']);
        }
        $this->queueDec();
    }

    /**
     * 预售订单下单
     * @param PreSell $preSell
     */
    public function addPreSellOrder(PreSell $preSell)
    {
        $this->preSell = $preSell;
        $this->setPromType(4);
        $this->setPromId($preSell['pre_sell_id']);
        $this->check();
        $this->queueInc();
        $this->addOrder();
        $this->addOrderGoods();
        $reduce = tpCache('shopping.reduce');
        Hook::listen('user_add_order', $this->order);//下单行为
        if($reduce == 1 || empty($reduce)){
            minus_stock($this->order);//下单减库存
        }
        //预售暂不至此积分余额优惠券支付
        // 如果应付金额为0  可能是余额支付 + 积分 + 优惠券 这里订单支付状态直接变成已支付
//            if ($this->order['order_amount'] == 0) {
//                update_pay_status($this->order['order_sn']);
//            }
//        $this->changUserPointMoney();//扣除用户积分余额
        $this->queueDec();
    }

    private function addShopOrder()
    {
        $shop = $this->pay->getShop();
        if(empty($shop)){
            return;
        }
        $shop_order_data = array();
        $payList = $this->pay->getPayList();
	if($payList[0]['is_virtual'] == 2){  //预约商品执行
		for ($i=0;$i < $payList[0]['goods_num'];$i++){
	            $shop_order_data[] = [
	                'order_id' => $this->order['order_id'],
	                'goods_id' => $payList[0]['goods_id'],
	                'order_sn' => $this->order['order_sn'],
	                'shop_id' => $shop['shop_id'],
	                'take_time' => date('Y-m-d H:i:s', $this->take_time),
	                'add_time' => time(),
	            ];
	        }	
	}else{ //自提商品执行
		$shop_order_data[] = [
	            'order_id' => $this->order['order_id'],
	            'order_sn' => $this->order['order_sn'],
	            'shop_id' => $shop['shop_id'],
	            'take_time' => date('Y-m-d H:i:s', $this->take_time),
	            'add_time' => time(),
                'bar_code' =>rand(16000001,16999999)
	        ];
	}
        
        $shopOrder = new ShopOrder();
        //$shopOrder->data($shop_order_data, true)->save();
	$shopOrder->saveAll($shop_order_data);
    }

     /**
     * 添加预约表单
     * @throws \Exception
     */
    private function addOrderBespeak()
    {
        $bespeakForm = $this->bespeakForm;
        $data = array();
        if (empty($bespeakForm)) {
            return;
        }
        foreach ($bespeakForm as $k=>$v){
            if(gettype($v)=='array' || gettype($v)=='object'){
                $v = implode(',',$v);
            }
            $data[]=['order_id'=>$this->order['order_id'],'template_unit_id'=>$k,'value'=>$v];
        }
        $orderBespeak = new OrderBespeak();
        $orderBespeak->saveAll($data);

    }


    /**
     * 验证预约信息(支付的时候还会调用此办法)
     * @param $order_goods
     * @param $shops
     * @param $take_time
     * @param int $template_unit_id
     * @throws TpshopException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function checkBespeakTemplate($order_goods, $shops, $take_time, $template_unit_id=0)
    {
        $payList = $order_goods;
        $shop = $shops;
        $this->take_time = $take_time;
        $goods_ids = get_arr_column($payList, 'goods_id');
        if (count($goods_ids) > 1) {
            throw new TpshopException('提交订单', 0, ['status' => 0, 'msg' => '不能同时预约多个商品', 'result' => '']);
        }
        $goods = new Goods();
        $goodsArr = $goods->where('goods_id', 'IN', $goods_ids)->field('goods_id,bespeak_template_id')->find();
        if($template_unit_id){
            //支付的时候进来这里
            $template_id = db('bespeak_template_unit')->where(['template_unit_id'=>$template_unit_id])->value('template_id');
            if($template_id != $goodsArr['bespeak_template_id']){
                throw new TpshopException('提交订单', 0, ['status' => 0, 'msg' => '预约信息已变，不能下单', 'result' => '']);
            }
        }

        $bespeak_template = $goodsArr['BespeakTemplate'];
        //判断时间
        if ($this->take_time <= time()) {
            throw new TpshopException('提交订单', 0, ['status' => 0, 'msg' => '可预约时间不能小于等于当前时间', 'result' => '']);
        }
        $time_day = strtotime(date('Y-m-d',strtotime("+".($bespeak_template['reserved_days']+1)." day")))-1;
        if ($this->take_time >= $time_day) {
            throw new TpshopException('提交订单', 0, ['status' => 0, 'msg' => '已超过' . $bespeak_template['reserved_days'] . '天可保留时间', 'result' => '']);
        }
        $weekday = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
        $day = $weekday[date('w', $this->take_time)];
        if ($bespeak_template[$day] == 0) {
            throw new TpshopException('提交订单', 0, ['status' => 0, 'msg' => '预约时间不在营业日范围', 'result' => '']);
        }

        $that_day = date('H:i', $this->take_time);
        $work_time = [$bespeak_template['work_am_start_time'], $bespeak_template['work_am_end_time'], $bespeak_template['work_pm_start_time'], $bespeak_template['work_pm_end_time']];
        $week_time = [$bespeak_template['weekend_am_start_time'], $bespeak_template['weekend_am_end_time'], $bespeak_template['weekend_pm_start_time'], $bespeak_template['weekend_pm_end_time']];
        //判断时间范围
        if (in_array($day, [ 'monday', 'tuesday', 'wednesday', 'thursday', 'friday'])) {
            if($work_time[0]> $that_day || ($work_time[1]< $that_day && $work_time[2] > $that_day) || $work_time[3]< $that_day ){
                throw new TpshopException('提交订单', 0, ['status' => 0, 'msg' => '预约时间不在工作日营业时间范围', 'result' => '']);
            }
        } else {
            if($week_time[0]> $that_day || ($week_time[1]< $that_day && $week_time[2] > $that_day) || $week_time[3]< $that_day ){
                throw new TpshopException('提交订单', 0, ['status' => 0, 'msg' => '预约时间不在周末营业时间范围', 'result' => '']);
            }
        }
        //判断时间段限制人数,按数量判断就查shop_order表,根据门店+数量+时间
        $ShopOrder = new ShopOrder();
        //拿出已付款的预约订单
        $count = $ShopOrder::hasWhere('Order',['Order.pay_status'=>1])
            ->where(['ShopOrder.goods_id'=>$goodsArr['goods_id'],'Order.order_status'=>['neq',2],'ShopOrder.shop_id'=>$shop['shop_id'],'ShopOrder.take_time'=>date('Y-m-d H:i:s',$this->take_time)])
            ->where(['Order.order_status'=>['neq',3]])
            ->where(['Order.order_status'=>['neq',4]])
            ->where(['Order.order_status'=>['neq',5]])
            ->count();
        if($count+$payList[0]['goods_num'] > $bespeak_template['numbers']){
            throw new TpshopException('提交订单', 0, ['status' => 0, 'msg' => '该时间段已预约满，请选其他时间', 'result' =>'']);
        }

    }


    /**
     * 验证预约表单数据
     * @param $form
     * @throws TpshopException
     * @throws \think\exception\DbException
     */
    public function validateBespeakForm($form){
        if(!$form){
            throw new TpshopException('验证码预约表单数据', 0, ['status' => 0, 'msg' => '请填写预约信息', 'result' => '']);
        }
        $ids = array_keys($form);

        $bespeak_template_unit = new BespeakTemplateUnit();
        $get_template_unit = $bespeak_template_unit::get($ids[0]);//获取模板id

        //查出改模板所有组件验证数据
        $get_template_unit = $bespeak_template_unit::all(['template_id'=>$get_template_unit['template_id'],'deleted'=>0]);

        foreach ($get_template_unit as $unit_k => $unit_v ){
            if($unit_v['required']){
                if(!$form[$unit_v['template_unit_id']]){
                    throw new TpshopException('验证码预约表单数据', 0, ['status' => 0, 'msg' => '请填写'.$unit_v['title'], 'result' => '']);
                }
            }
            //存在值才去判断
            if($form[$unit_v['template_unit_id']]){
                if($unit_v['format']){
                    $msg = validateForm($unit_v['format'],$form[$unit_v['template_unit_id']]);
                    if($msg){
                        throw new TpshopException('验证码预约表单数据', 0, ['status' => 0, 'msg' => $msg, 'result' => '']);
                    }
                }
            }


        }

        if (empty($this->consignee)) {
            throw new TpshopException('提交订单', 0, ['status' => 0, 'msg' => '请填写提货人姓名', 'result' => '']);
        }
        if (empty($this->mobile) || !check_mobile($this->mobile)) {
            throw new TpshopException('提交订单', 0, ['status' => 0, 'msg' => '提货人联系方式格式不正确', 'result' => '']);
        }

    }

    /**
     * 提交订单前检查
     * @throws TpshopException
     */
    public function check()
    {
        $shop = $this->pay->getShop();
	$payList = $this->pay->getPayList();
        if($shop['shop_id'] > 0 && $payList[0]['is_virtual'] != 2){
            if($this->take_time <= time()){
                throw new TpshopException('提交订单', 0, ['status' => 0, 'msg' => '自提时间不能小于当前时间', 'result' => '']);
            }
            $weekday = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
            $day = $weekday[date('w', $this->take_time)];
            if($shop[$day] == 0){
                throw new TpshopException('提交订单', 0, ['status' => 0, 'msg' => '自提时间不在营业日范围', 'result' => '']);
            }
            $that_day = date('Y-m-d', $this->take_time);
            $that_day_start_time = strtotime($that_day . ' '.$shop['work_start_time'] . ':00');
            $that_day_end_time = strtotime($that_day . ' '.$shop['work_end_time'] . ':00');
            if($this->take_time < $that_day_start_time || $this->take_time > $that_day_end_time){
                throw new TpshopException('提交订单', 0, ['status' => 0, 'msg' => '自提时间不在营业时间范围', 'result' => '']);
            }
            if(empty($this->consignee)){
                throw new TpshopException('提交订单', 0, ['status' => 0, 'msg' => '请填写提货人姓名', 'result' => '']);
            }
            if(empty($this->mobile) || !check_mobile($this->mobile)){
                throw new TpshopException('提交订单', 0, ['status' => 0, 'msg' => '提货人联系方式格式不正确', 'result' => '']);
            }
        }else if ($shop['shop_id'] > 0 && $payList[0]['is_virtual'] == 2) {
            
            $this->checkBespeakTemplate($payList,$shop,$this->take_time);
            $this->validateBespeakForm($this->bespeakForm);
        }
        //使用积分不用输入密码
        $user_money = $this->pay->getUserMoney();
        if ($user_money > 0) {
            $user = $this->pay->getUser();
            if ($user['is_lock'] == 1) {
                throw new TpshopException('提交订单', 0, ['status' => -5, 'msg' => "账号异常已被锁定，不能使用余额支付！", 'result' => '']);
            }
            if (empty($user['paypwd'])) {
                throw new TpshopException('提交订单', 0, ['status' => -6, 'msg' => "请先设置支付密码", 'result' => '']);
            }         
            if (empty($this->payPsw)) {
               throw new TpshopException('提交订单', 0, ['status' => -7, 'msg' => "请输入支付密码", 'result' => '']);
            }
            if ($this->payPsw !== $user['paypwd'] && encrypt($this->payPsw) !== $user['paypwd']) {
               throw new TpshopException('提交订单', 0, ['status' => -8, 'msg' => '支付密码错误', 'result' => '']);
            }
        }
    }

    private function queueInc()
    {
        $queue = Cache::get('queue');
        if($queue >= 100){
            throw new TpshopException('提交订单', 0, ['status' => -99, 'msg' => "当前人数过多请耐心排队!", 'result' => '']);
        }
        Cache::inc('queue');
    }

    /**
     * 订单提交结束
     */
    private function queueDec()
    {
        Cache::dec('queue');
    }

    /**
     * 插入订单表
     * @throws TpshopException
     */
    private function addOrder()
    {
        $OrderLogic = new OrderLogic();
        $user = $this->pay->getUser();
        $shop = $this->pay->getShop();
        $invoice_title = $this->invoiceTitle;
        if($this->invoiceTitle == "" && $this->invoiceDesc != "不开发票"){
            $invoice_title = "个人";
        }
        $orderData = [
            'order_sn' => $OrderLogic->get_order_sn(), // 订单编号
            'user_id' => $user['user_id'], // 用户id
            'email' => $user['email'],//'邮箱'
            'invoice_title' => ($this->invoiceDesc != '不开发票') ?  $invoice_title : '', //'发票抬头',
            'invoice_desc' => $this->invoiceDesc, //'发票内容',
            'goods_price' => $this->pay->getGoodsPrice(),//'商品价格',
            'shipping_price' => $this->pay->getShippingPrice(),//'物流价格',
			'real_shipping_price' => $this->pay->getRealShippingPrice(),//'真实物流价格'
            'user_money' => $this->pay->getUserMoney(),//'使用余额',
            'coupon_price' => $this->pay->getCouponPrice(),//'使用优惠券',
            'integral' => $this->pay->getPayPoints(), //'使用积分',
            'integral_money' => $this->pay->getIntegralMoney(),//'使用积分抵多少钱',
            'total_amount' => $this->pay->getTotalAmount(),// 订单总额
            'order_amount' => $this->pay->getOrderAmount(),//'应付款金额',
            'add_time' => time(), // 下单时间
        ];
        if($orderData["order_amount"] < 0){
            throw new TpshopException("订单入库", 0, ['status' => -8, 'msg' => '订单金额不能小于0', 'result' => '']);
        }
        if ($this->promType == 4) {
            //预售订单
            if ($this->preSell['deposit_price'] > 0) {
                $orderData['goods_price'] = $this->preSell['ing_price'] * $this->pay->getToTalNum();
                $orderData['total_amount'] = $this->preSell['ing_price'] * $this->pay->getToTalNum();
                $orderData['order_amount'] = $this->preSell['deposit_price'] * $this->pay->getToTalNum() - $this->pay->getIntegralMoney() - $this->pay->getUserMoney();
            }
        }
        if (!empty($shop)) {
            $orderData['shop_id'] = $shop['shop_id'];
            $orderData['consignee'] = $this->consignee;
            $orderData['mobile'] = $this->mobile;
            $orderData['province'] = $shop['province_id'];
            $orderData['city'] = $shop['city_id'];
            $orderData['district'] = $shop['district_id'];
            $orderData['address'] = $shop['shop_address'];
            $orderData['zipcode'] = $shop['shop_zip'];
        } elseif (!empty($this->userAddress)) {
            $orderData['consignee'] = $this->userAddress['consignee'];// 收货人
            $orderData['province'] = $this->userAddress['province'];//'省份id',
            $orderData['city'] = $this->userAddress['city'];//'城市id',
            $orderData['district'] = $this->userAddress['district'];//'县',
            $orderData['twon'] = $this->userAddress['twon'];// '街道',
            $orderData['address'] = $this->userAddress['address'];//'详细地址'
            $orderData['mobile'] = $this->userAddress['mobile'];//'手机',
            $orderData['zipcode'] = $this->userAddress['zipcode'];//'邮编',
        } else {
            $orderData['consignee'] = $user['nickname']?$user['nickname']:'';// 收货人
            $orderData['mobile'] = $user['mobile'];//'手机',
        }
        if (!empty($this->userNote)) {
            $orderData['user_note'] = $this->userNote;// 用户下单备注
        }
        if (!empty($this->taxpayer)) {
            $orderData['taxpayer'] = $this->taxpayer; //'发票纳税人识别号',
        }
        $orderPromId = $this->pay->getOrderPromId();
        $orderPromAmount = $this->pay->getOrderPromAmount();
        if ($orderPromId > 0) {
            $orderData['order_prom_id'] = $orderPromId;//'订单优惠活动id',
            $orderData['order_prom_amount'] = $orderPromAmount;//'订单优惠活动金额,
        }

        $payList = $this->pay->getPayList();
        if($payList[0]['is_virtual'] ==1 ){
            $this->promType =  5 ; 
            $orderData['shipping_time'] = $payList[0]['virtual_indate'];
            $orderData['mobile'] = $this->mobile;
        }else if($payList[0]['is_virtual'] ==2){
            $this->promType =  7 ;   //预约订单
            $orderData['shipping_time'] = $payList[0]['virtual_indate'];
            $orderData['mobile'] = $this->mobile;
        }else if($payList[0]['is_virtual'] !=2 && $shop['shop_id'] > 0){
            $this->promType =  9 ; //自提订单
        }else if($payList[0]['prom_type'] == 8){
            //砍价订单
            $orderData['prom_type'] = $payList[0]['prom_type'];//订单类型
            $orderData['prom_id'] = $payList[0]['prom_id'];//活动id
        }
		//添加供应商id,-1为复合订单,在付款后拆分
        $suppliers_ids = get_arr_column($payList,'suppliers_id');
		$suppliers_ids = array_unique($suppliers_ids);
		if (count($suppliers_ids) > 1) {
			$orderData['suppliers_id'] = -1;
		} else {
			//$suppliers_ids[0]为null就表示没有将suppliers_id传过来，应该排查哪里没传并修复，而不是将其写为0（可能存在suppliers_id>0,没传过来就导致数据丢失，影响此订单的后续）
			$orderData['suppliers_id'] = $suppliers_ids[0];
		}

        if ($this->promType) {
            $orderData['prom_type'] = $this->promType;//订单类型
        }
        if ($this->promId > 0) {
            $orderData['prom_id'] = $this->promId;//活动id
        }
        if ($orderData['integral'] > 0 || $orderData['user_money'] > 0) {
            $orderData['pay_name'] = $orderData['user_money']>0 ? '余额支付' : '积分兑换';//支付方式，可能是余额支付或积分兑换，后面其他支付方式会替换
        }
        $this->order->data($orderData, true);
        $orderSaveResult = $this->order->save();
        if($orderData['prom_type'] == 8){
            //更新砍价信息，绑定订单
            $this->saveBargainFirst();
        }
        if ($orderSaveResult === false) {
            throw new TpshopException("订单入库", 0, ['status' => -8, 'msg' => '添加订单失败', 'result' => '']);
        }
    }

    /**
     * 更新砍价信息，绑定订单
     */
    private function saveBargainFirst()
    {
        db('bargain_first')->where(['bargain_id'=>$this->order['prom_id'],'user_id'=>$this->order['user_id'],'order_id'=>0])->update(['order_id'=>$this->order['order_id'],'is_end'=>1]);
    }

    /**
     * 插入订单商品表
     */
    private function addOrderGoods()
    {
        if($this->pay->getOrderPromAmount() > 0) {
            $orderDiscounts = $this->pay->getOrderPromAmount() + $this->pay->getCouponPrice();  //整个订单优惠价钱
        }else{
            $orderDiscounts = $this->pay->getCouponPrice();  //整个订单优惠价钱
        }
        $payList = $this->pay->getPayList();
        $goods_ids = get_arr_column($payList,'goods_id');
        $goodsArr = Db::name('goods')->where('goods_id', 'IN', $goods_ids)->getField('goods_id,cost_price,give_integral');
        $orderGoodsAllData = [];
        //砍价可能商品价格为0，除数不能为0，所以会导致报错，
        if($this->order['goods_price']){
            $rate = ($this->order['user_money'] + $this->order['order_amount']) / $this->order['goods_price']; // 按实付比例 算积分
        }else{
            $rate = 0;
        }
        foreach ($payList as $payKey => $payItem) {
            if($this->pay->getGoodsPrice() ==0){  //清华要求加上
                $totalPriceToRatio =0;
            }else{
                $totalPriceToRatio = $payItem['member_goods_price'] / $this->pay->getGoodsPrice();  //商品价格占总价的比例
            }
            $finalPrice = round($payItem['member_goods_price'] - ($totalPriceToRatio * $orderDiscounts), 3);
            $orderGoodsData['order_id'] = $this->order['order_id']; // 订单id
            $orderGoodsData['goods_id'] = $payItem['goods_id']; // 商品id
            $orderGoodsData['goods_name'] = $payItem['goods_name']; // 商品名称
            $orderGoodsData['goods_sn'] = $payItem['goods_sn']; // 商品货号
			$orderGoodsData['suppliers_id'] = $payItem['suppliers_id']; // 商品供应商
            $orderGoodsData['goods_num'] = $payItem['goods_num']; // 购买数量
            $orderGoodsData['final_price'] = $finalPrice; // 每件商品实际支付价格
            $orderGoodsData['goods_price'] = $payItem['goods_price']; // 商品价               为照顾新手开发者们能看懂代码，此处每个字段加于详细注释
            if (!empty($payItem['spec_key'])) {
                $orderGoodsData['spec_key'] = $payItem['spec_key']; // 商品规格
                $orderGoodsData['spec_key_name'] = $payItem['spec_key_name']; // 商品规格名称
                $spec_goods_price = db('spec_goods_price')->where(['goods_id'=>$payItem['goods_id'],'key'=>$payItem['spec_key']])->find();
                $orderGoodsData['cost_price'] = $spec_goods_price['cost_price']; // 成本价
                $orderGoodsData['item_id'] = $spec_goods_price['item_id']; // 商品规格id
            } else {
                $orderGoodsData['spec_key'] = ''; // 商品规格
                $orderGoodsData['spec_key_name'] = ''; // 商品规格名称
                $orderGoodsData['cost_price'] = $goodsArr[$payItem['goods_id']]['cost_price']; // 成本价
                $orderGoodsData['item_id'] = 0; // 商品规格id
            }
            $orderGoodsData['sku'] = $payItem['sku']; // sku
            $orderGoodsData['member_goods_price'] = $payItem['member_goods_price']; // 会员折扣价
            $orderGoodsData['give_integral'] = $goodsArr[$payItem['goods_id']]['give_integral'] * $rate; // 购买商品赠送积分
            if($payItem['prom_type'] == 8){
                $orderGoodsData['give_integral'] = 0; // 砍价活动不赠送积分
            }
            if ($payItem['prom_type']) {
                $orderGoodsData['prom_type'] = $payItem['prom_type']; // 0 普通订单,1 限时抢购, 2 团购 , 3 促销优惠
                $orderGoodsData['prom_id'] = $payItem['prom_id']; // 活动id
            } else {
                $orderGoodsData['prom_type'] = 0; // 0 普通订单,1 限时抢购, 2 团购 , 3 促销优惠
                $orderGoodsData['prom_id'] = 0; // 活动id
            }
            array_push($orderGoodsAllData, $orderGoodsData);
        }
        Db::name('order_goods')->insertAll($orderGoodsAllData);
    }

    /**
     * 扣除优惠券
     */
    public function deductionCoupon()
    {
        $couponId = $this->pay->getCouponId();
        if($couponId > 0){
            $user = $this->pay->getUser();
            $couponList = new CouponList();
            $userCoupon = $couponList->where(['status'=>0,'id'=>$couponId])->find();
            if($userCoupon){
                $userCoupon->uid = $user['user_id'];
                $userCoupon->order_id = $this->order['order_id'];
                $userCoupon->use_time = time();
                $userCoupon->status =  1;
                $userCoupon->save();
                Db::name('coupon')->where('id', $userCoupon['cid'])->setInc('use_num');// 优惠券的使用数量加一
            }
        }
    }

    /**
     * 扣除用户积分余额
     * @param Order $order
     */
    public function changUserPointMoney(Order $order)
    {
        if($this->pay->getPayPoints() > 0 || $this->pay->getUserMoney() > 0){
            $user = $this->pay->getUser();
            $user = Users::get($user['user_id']);
            if($this->pay->getPayPoints() > 0){
                $user->pay_points = $user->pay_points - $this->pay->getPayPoints();// 消费积分
            }
            if($this->pay->getUserMoney() > 0){
                $user->user_money = $user->user_money - $this->pay->getUserMoney();// 抵扣余额
            }
            $user->save();
            $accountLogData = [
                'user_id' => $order['user_id'],
                'user_money' => -$this->pay->getUserMoney(),
                'pay_points' => -$this->pay->getPayPoints(),
                'change_time' => time(),
                'desc' => '下单消费',
                'order_sn'=>$order['order_sn'],
                'order_id'=>$order['order_id'],
            ];
            Db::name('account_log')->insert($accountLogData);
        }
    }
    /**
     * 这方法特殊，只限拼团使用。
     * @param $order
     */
    public function setOrder($order)
    {
        $this->order = $order;
    }
    /**
     * 获取订单表数据
     * @return Order
     */
    public function getOrder()
    {
        return $this->order;
    }

    public function setPayPsw($payPsw)
    {
        $this->payPsw = $payPsw;
        return $this;
    }

    public function setInvoiceTitle($invoiceTitle)
    {
        $this->invoiceTitle = $invoiceTitle;
        return $this;
    }
    public function setUserNote($userNote)
    {
        $this->userNote = $userNote;
        return $this;
    }
    public function setTaxpayer($taxpayer)
    {
        $this->taxpayer = $taxpayer;
        return $this;
    }
    public function setInvoiceDesc($invoice_desc)
    {
        if(empty($invoice_desc)) $invoice_desc = '不开发票';
        $this->invoiceDesc = $invoice_desc;
        return $this;
    }

    public function setUserAddress($userAddress)
    {
        $this->userAddress = $userAddress;
        return $this;
    }
    public function setShop($shop)
    {
        $this->shop = $shop;
        return $this;
    }
    public function setTakeTime($take_time)
    {
        $this->take_time = $take_time;
        return $this;
    }
    public function setConsignee($consignee)
    {
        $this->consignee = $consignee;
        return $this;
    }
    public function setMobile($mobile)
    {
        $payList = $this->pay->getPayList();
        if($payList[0]['is_virtual']){
            if($mobile){
                if(check_mobile($mobile)){
                    $this->mobile = $mobile;
                }else{
                    throw new TpshopException("提交订单",0,['status'=>-1,'msg'=>'手机号码格式错误','result'=>['']]);
                }
            }else{
                throw new TpshopException("提交订单",0,['status'=>-1,'msg'=>'请填写手机号码','result'=>['']]);
            }
        }
        $this->mobile = $mobile;
        return $this;
    }

    private function setPromType($prom_type)
    {
        $this->promType = $prom_type;
        return $this;
    }
    private function setPromId($prom_id)
    {
        $this->promId = $prom_id;
        return $this;
    }

    public function setBespeakForm($bespeakForm)
    {
        $this->bespeakForm = $bespeakForm;
        return $this;
    }
}