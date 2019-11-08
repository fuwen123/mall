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
namespace app\supplier\controller;
use app\supplier\logic\RefundLogic;
use app\supplier\logic\KdniaoLogic;
use app\common\logic\PlaceOrder;
use app\common\model\Order as OrderModel;
use app\common\logic\Pay;
use app\common\model\OrderGoods;
use app\common\logic\OrderLogic;
use app\common\logic\MessageFactory;
use app\common\model\ReturnGoods;
use app\common\util\TpshopException;
use think\AjaxPage;
use think\Page;
use think\Db;

class Order extends Base {
    public  $order_status;
    public  $pay_status;
    public  $shipping_status;
    /**
     * 初始化操作
     */
    public function _initialize() {
        parent::_initialize();
        C('TOKEN_ON',false); // 关闭表单令牌验证
        $this->order_status = C('ORDER_STATUS');
        $this->pay_status = C('PAY_STATUS');
        $this->shipping_status = C('SHIPPING_STATUS');
        // 订单 支付 发货状态
        $this->assign('order_status',$this->order_status);
        $this->assign('pay_status',$this->pay_status);
        $this->assign('shipping_status',$this->shipping_status);
    }

    /**
     *订单首页
     */
    public function index(){
        return $this->fetch();
    }

    /**
     * Ajax首页
     */
    public function ajaxindex(){
        $begin = $this->begin;
        $end = $this->end;
        // 搜索条件
        //$condition = array('shop_id'=>0);
        $keyType = I("key_type");
        $keywords = I('keywords','','trim');
        $consignee =  ($keyType && $keyType == 'consignee') ? $keywords : I('consignee','','trim');
        $consignee ? $condition['consignee'] = ['like','%'.trim($consignee).'%'] : false;
        $nickname =  ($keyType && $keyType == 'nickname') ? $keywords : I('nickname','','trim');
        $nickname ? $condition['nickname'] = ['like','%'.trim($nickname).'%'] : false;

        if($begin && $end){
            $condition['add_time'] = array('between',"$begin,$end");
        }
//        $condition['prom_type'] = array('lt',5);
        $condition['prom_type'] = array('in',[0,1,2,3,4,8]);
        $order_sn = ($keyType && $keyType == 'order_sn') ? $keywords : I('order_sn') ;
        $order_sn ? $condition['order_sn'] = trim($order_sn) : false;
        
        I('order_status') != '' ? $condition['order_status'] = I('order_status') : false;
        I('pay_status') != '' ? $condition['pay_status'] = I('pay_status') : false;
        //I('pay_code') != '' ? $condition['pay_code'] = I('pay_code') : false;
        if(I('pay_code')){
            switch (I('pay_code')){
                case '余额支付':
                    $condition['pay_name'] = I('pay_code');
                    break;
                case '积分兑换':
                    $condition['pay_name'] = I('pay_code');
                    break;
                case 'alipay':
                    $condition['pay_code'] = ['in',['alipay','alipayMobile']];
                    break;
                case 'weixin':
                    $condition['pay_code'] = ['in',['weixin','weixinH5','miniAppPay']];
                    break;
                case '其他方式':
                    $condition['pay_name'] = '';
                    $condition['pay_code'] = '';
                    break;
                default:
                    $condition['pay_code'] = I('pay_code');
                    break;
            }
        }

        I('shipping_status') != '' ? $condition['shipping_status'] = I('shipping_status') : false;
        I('user_id') ? $condition['user_id'] = trim(I('user_id')) : false;
        $sort_order = I('order_by','DESC').' '.I('sort');
        $Order = new \app\common\model\Order();
		//获取该供应商下通过审核的商品id
		$condition['suppliers_id'] = $this->supplier['suppliers_id'];
        $count = $Order->alias('o')
			->field('order_id')
			->join('__USERS__ u','u.user_id = o.user_id','left')
			->where($condition)
			->count();
        $Page  = new AjaxPage($count,20);
        $show = $Page->show();
        $orderList = $Order->alias('o')
			->field('o.*,nickname')
			->join('__USERS__ u','u.user_id = o.user_id','left')
			->where($condition)
			->limit($Page->firstRow,$Page->listRows)->order($sort_order)->select();
        $this->assign('orderList',$orderList);
        $this->assign('page',$show);// 赋值分页输出
        $this->assign('pager',$Page);
        return $this->fetch();
    }
	
	/**
     * 导出订单
     */
    public function export_order()
    {
        //搜索条件
        $order_status = I('order_status','');
        $order_ids = I('order_ids');
        $prom_type = I('prom_type'); //订单类型
        $keyType =   I("key_type");  //查找类型
        $keywords = I('keywords','','trim');
        $where= ['add_time'=>['between',"$this->begin,$this->end"]];
        if(!empty($keywords)){
            $keyType == 'mobile'   ? $where['mobile']  = $keywords : false;
            $keyType == 'order_sn' ? $where['order_sn'] = $keywords: false;
            $keyType == 'consignee' ? $where['consignee'] = $keywords: false;
        }
        $prom_type != '' ? $where['prom_type'] = $prom_type : $where['prom_type'] = ['in',[0,1,2,3,4,7,8]];
        if($order_status>-1 && $order_status != ''){
            $where['order_status'] = $order_status;
        }
        if($order_ids){
            $where['order_id'] = ['in', $order_ids];
        }
        if(I('pay_code')){
            switch (I('pay_code')){
                case '余额支付':
                    $where['pay_name'] = I('pay_code');
                    break;
                case '积分兑换':
                    $where['pay_name'] = I('pay_code');
                    break;
                case 'alipay':
                    $where['pay_code'] = ['in',['alipay','alipayMobile']];
                    break;
                case 'weixin':
                    $where['pay_code'] = ['in',['weixin','weixinH5','miniAppPay']];
                    break;
                case '其他方式':
                    $where['pay_name'] = '';
                    $where['pay_code'] = '';
                    break;
                default:
                    $where['pay_code'] = I('pay_code');
                    break;
            }
        }
        I('pay_status') != '' ? $where['pay_status'] = I('pay_status') : false;
        if($where['order_status'] == 3){
            $where['pay_status'] = ['gt',0];
            $where['shipping_status'] = 0;
            unset($where['add_time']);
            unset($where['prom_type']);
        }
		$where['suppliers_id'] = $this->supplier['suppliers_id'];
        $orderList = Db::name('order')->field("*,FROM_UNIXTIME(add_time,'%Y-%m-%d') as create_time")->where($where)->order('order_id')->select();

        $strTable ='<table width="500" border="1">';
        $strTable .= '<tr>';
        $strTable .= '<td style="text-align:center;font-size:12px;width:120px;">订单编号</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">会员昵称</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="100">日期</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">收货人</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">收货地址</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">电话</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">商品价格</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">订单金额</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">实际支付</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">支付方式</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">支付状态</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">订单状态</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">发货状态</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">商品数量</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">商品编码</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">商品名称</td>';
        $strTable .= '</tr>';
        if(is_array($orderList)){
            $region = get_region_list();
            foreach($orderList as $k=>$val){
                $orderGoods = D('order_goods')->where('order_id='.$val['order_id'])->select();
                foreach($orderGoods as $goods){
                $strTable .= '<tr>';
                $strTable .= '<td style="text-align:center;font-size:12px;">&nbsp;'.$val['order_sn'].'</td>';
                $user_name = D('users')->where(['user_id'=>$val['user_id']])->value('nickname');
                $strTable .= '<td style="text-align:left;font-size:12px;">'.$user_name.' </td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">'.$val['create_time'].' </td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">'.$val['consignee'].'</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">'."{$region[$val['province']]},{$region[$val['city']]},{$region[$val['district']]},{$region[$val['twon']]}{$val['address']}".' </td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">'.$val['mobile'].'</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">'.$goods['member_goods_price'].'</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">'.$val['goods_price'].'</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">'.$val['order_amount'].'</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">'.($val['pay_name'] ? $val['pay_name'] : '其他方式').'</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">'.$this->pay_status[$val['pay_status']].'</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">'.$this->order_status[$val['order_status']].'</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">'.$this->shipping_status[$val['shipping_status']].'</td>';
                $strGoods="";
                $goods_num = 0;
                    $goods_num = $goods_num + $goods['goods_num'];
                    $strGoods .= " 商品名称：".$goods['goods_name'];
                    if ($goods['spec_key_name'] != '') $strGoods .= " 规格：".$goods['spec_key_name'];
//                  $strGoods .= "<br />";
                $strTable .= '<td style="text-align:left;font-size:12px;">'.$goods_num.' </td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">'.$goods['goods_sn'].' </td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">'.$strGoods.' </td>';
                $strTable .= '</tr>';
                }
                unset($orderGoods);

            }

        }
        $strTable .='</table>';
        unset($orderList);
        downloadExcel($strTable,'order');
        exit();
    }
	
	/**
     * 导出发货订单数据
     */
    public function export_delivery_order()
    {
		$condition = array();
		$order_ids = I('order_ids');
		if($order_ids){
            $condition['order_id'] = ['in', $order_ids];
        } else {
			I('consignee') ? $condition['consignee'] = trim(I('consignee')) : false;
			I('nickname') ? $condition['nickname'] = trim(I('nickname')) : false;
			I('order_sn') != '' ? $condition['order_sn'] = trim(I('order_sn')) : false;
			$shipping_status = I('shipping_status');
			$condition['shipping_status'] = empty($shipping_status) ? array('neq',1) : $shipping_status;
			$condition['order_status'] = array('in','1,2,4');
			$condition['prom_type'] = ['neq',5];
			$condition['shop_id'] = 0;
			$condition['suppliers_id'] = $this->supplier['suppliers_id'];
		}
		if($shipping_status)
			$field = "o.*,nickname,IF(shipping_name='','无需物流',shipping_name) as shipping_name";
		else
			$field = "o.*,nickname";
        $orderList = M('order')
			->alias('o')
			->join('__USERS__ u','u.user_id = o.user_id','left')
			->where($condition)
			->field($field)
			->order('add_time DESC')->select();
			
        $strTable ='<table width="500" border="1">';
        $strTable .= '<tr>';
        $strTable .= '<td style="text-align:center;font-size:12px;width:120px;">订单编号</td>';
		$strTable .= '<td style="text-align:center;font-size:12px;" width="*">下单时间</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">会员昵称</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">收货人</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">收货地址</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">联系电话</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">所选物流</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">物流费用</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">支付时间</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">订单总价</td>';
        $strTable .= '</tr>';
        if(is_array($orderList)){
            $region = get_region_list();
            foreach($orderList as $k=>$val){
                $orderGoods = D('order_goods')->where('order_id='.$val['order_id'])->select();
                foreach($orderGoods as $goods){
                $strTable .= '<tr>';
                $strTable .= '<td style="text-align:center;font-size:12px;">&nbsp;'.$val['order_sn'].'</td>';
				$strTable .= '<td style="text-align:left;font-size:12px;">'.date('Y-m-d H:i', $val['add_time']).'</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">'.$val['nickname'].'</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">'.$val['consignee'].'</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">'."{$region[$val['province']]},{$region[$val['city']]},{$region[$val['district']]},{$region[$val['twon']]}{$val['address']}".' </td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">'.$val['mobile'].'</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">'.$val['shipping_name'].'</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">'.$val['shipping_price'].'</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">'.date('Y-m-d H:i', $val['pay_time']).'</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">'.$val['total_amount'].'</td>';
                $strTable .= '</tr>';
                }
                unset($orderGoods);

            }

        }
        $strTable .='</table>';
        unset($orderList);
        downloadExcel($strTable,'order');
        exit();
    }
	
	/**
     * 导出发货单中包含的发货商品
     */
    public function exportDeliveryGoods()
    {
        $order_ids = I('ids4');
        if(empty($order_ids)){
            $this->error('没有选中订单', U('Supplier/order/deliveryList'));
        }
        $where['order_id'] = ['in', $order_ids];
        $orderList = Db::name('order')->field('order_sn,order_id,total_amount')->where($where)->order('order_id')->select();
        if(!$orderList){
            $this->error('没找到相关订单信息', U('Supplier/order/deliveryList'));
        }
        $strTable ='<table width="500" border="1">';
        $strTable .= '<tr>';
        $strTable .= '<td style="text-align:center;font-size:12px;width:125px;">订单编号</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">订单总价</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">商品信息</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">对应商品规格</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">对应商品数量</td>';
        $strTable .= '</tr>';
            foreach($orderList as $k=>$val){
                $strTable .= '<tr>';
                $strTable .= '<td style="text-align:center;font-size:12px;">&nbsp;'.$val['order_sn'].'</td>';
                $strTable .= '<td style="text-align:center;font-size:12px;">&nbsp;'.$val['total_amount'].'</td>';
                $orderGoods = D('order_goods')->where('order_id='.$val['order_id'])->select();
                $strGoods="";
                $goods_num = '';
                $spec_key_name = '';
                foreach($orderGoods as $goods){
                    $goods_num .= '&nbsp;'.$goods['goods_num'];
                    $goods_num  .= "<br />";
                    $strGoods .= "&nbsp;商品编号：".$goods['goods_sn']." 商品名称：".$goods['goods_name'];
                    $strGoods .= "<br />";
                    $spec_key_name .= "&nbsp;".($goods['spec_key_name'] ?: '无' );
                    $spec_key_name .= "<br />";
                }
                unset($orderGoods);
                $strTable .= '<td style="text-align:left;font-size:12px;">'.$strGoods.' </td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">'.$spec_key_name.' </td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">'.$goods_num.' </td>';
                $strTable .= '</tr>';
            }

        $strTable .='</table>';
        unset($orderList);
        downloadExcel($strTable,'goods_list');
        exit();
    }
	
	/**
     * 订单详情
     * @return mixed
     */
    public function detail(){
        $order_id = input('order_id', 0);
        $orderModel = new OrderModel();
        $order = $orderModel::get(['order_id'=>$order_id]);
        if(empty($order)){
            $this->error('订单不存在或已被删除');
        }
        if($order['invoice_desc'] == '不开发票')  $order['taxpayer'] = '';
        $this->assign('order', $order);
        return $this->fetch();
    }
	
	/**
     * 获取订单操作记录
     */
    public function getOrderAction(){
        $order_id = input('order_id/d',0);
        $order_id <= 0 && $this->ajaxReturn(['status'=>1,'msg'=>'参数错误！！']);
        $orderModel = new OrderModel();
        $orderObj = $orderModel::get(['order_id'=>$order_id]);
        $order = $orderObj->toArray();
        // 获取操作记录
        $action_log = Db::name('order_action')->where(['order_id'=>$order_id])->order('log_time desc')->select();
        $admins = M("admin")->getField("admin_id , user_name", true);
        $user = M("users")->field('user_id,nickname')->where(['user_id'=>$order['user_id']])->find();
        //查找用户昵称
        foreach ($action_log as $k => $v){
			if ($v['group'] == 0) {
				if ($v['action_user'] == 0){
					$action_log["$k"]['action_user_name'] = '用户:'.$user['nickname'];
				}else{
					$action_log["$k"]['action_user_name'] = '管理员:'.$admins[$v['action_user']];
				}
			} else if ($v['group'] == 1) {
				$action_log["$k"]['action_user_name'] = '供应商:'.$this->supplier['suppliers_name'];
			}
            $action_log["$k"]["log_time"] = date('Y-m-d H:i:s',$v['log_time']);
            $action_log["$k"]["order_status"] = $this->order_status[$v['order_status']];
            $action_log["$k"]["pay_status"] = $this->pay_status[$v['pay_status']];
            $action_log["$k"]["shipping_status"] = $this->shipping_status[$v['shipping_status']];
        }
        $this->ajaxReturn(['status'=>1,'msg'=>'参数错误！！','data'=>$action_log]);
    }
	
	/**
     * 订单操作
     * @param $id
     */
    public function order_action(){   
        $orderLogic = new OrderLogic();
        $action = I('get.type');
        $order_id = I('get.order_id');
        if($action && $order_id && in_array($action, ['confirm', 'cancel'])){ //供应商后台只执行confirm、cancel两种操作
            $a = $orderLogic->orderProcessHandle($order_id,$action);
            if($action !=='pay'){
                $convert_action= C('CONVERT_ACTION')["$action"];
                $commonOrder = new \app\common\logic\Order();
                $commonOrder->setOrderById($order_id);
                $res = $commonOrder->orderActionLog(I('note'),$convert_action,$this->supplier['suppliers_id'], 1);
            }
             if($res !== false && $a !== false){
                 $this->ajaxReturn(['status' => 1,'msg' => '操作成功','url' => U('Order/detail',array('order_id'=>$order_id))]);
             }else{
                $this->ajaxReturn(['status' => 0,'msg' => '操作失败','url' => U('Order/index')]);
             }
        }else{
            $this->ajaxReturn(['status' => 0,'msg' => '参数错误','url' => U('Order/index')]);
        }
    }
	
	/**
     * ajax 获取自提订单信息
     * order_id
     */
    public function getOrderGoodsInfo()
    {
        $order_id = input("order_id/d",0);
        $Order = new OrderModel();
        $order = $Order->with("shop,shop_order")->where(['order_id'=>$order_id])->find();
        $order_info = $order->append(['delivery_method','shipping_status_desc'])->toArray();
        $this->ajaxReturn($order_info);
    }
	
	/**
     * 核销
     */
    public function writeOff()
    {
        $shop_order_id = input('shop_order_id/d', '');
        $bar_code = input('bar_code/d', '');
        $status = input('status/d','');

        $ShopOrderLogic = new \app\common\logic\ShopOrder();
        if($shop_order_id) $ShopOrderLogic->setShopOrderById($shop_order_id);
        if($bar_code) $ShopOrderLogic->setShopOrderByBarCode($bar_code);

        try {
            if($status == 'write_off'){
                $ShopOrderLogic->writeOff();
                $this->ajaxReturn(['status' => 1, 'msg' => '核销成功']);
            }else{
                $this->ajaxReturn(['status' => 2, 'msg' => '获取成功','result' => $ShopOrderLogic->getShopOrderModel()->append(['order'])->toArray() ]);
            }
        } catch (TpshopException $t) {
            $error = $t->getErrorArr();
            $this->ajaxReturn($error);
        }
    }
	
	/**
     * 订单打印
     * @param string $id
     * @return mixed
     */
    public function order_print($id=''){
        if($id){
            $order_id = $id;
        }else{
            $order_id = I('order_id');
        }
        $orderModel = new OrderModel();
        $orderObj = $orderModel::get(['order_id'=>$order_id]);
        $order =$orderObj->append(['full_address','orderGoods','delivery_method'])->toArray();
//        halt($order['orderGoods']);
        $order['province'] = getRegionName($order['province']);
        $order['city'] = getRegionName($order['city']);
        $order['district'] = getRegionName($order['district']);
        $order['full_address'] = $order['province'].' '.$order['city'].' '.$order['district'].' '. $order['address'];
        if($id){
            return $order;
        }else{
            $shop = tpCache('shop_info');
            $area_list = Db::name('region')->where('id', 'IN', [$shop['province'], $shop['city'], $shop['district']])->order('level asc')->select();
            $shop['address'] =$area_list[0]['name'].' '.$area_list[1]['name'].' '.$area_list[2]['name'].' '.$shop['address'];
            $this->assign('order',$order);
            $this->assign('shop',$shop);
            $template = I('template','picking');
            return $this->fetch($template);
        }
    }
	
	/**
     *批量打印发货单
     */
    public function delivery_print(){
        $ids =input('print_ids');
        $order_ids=trim($ids,',');
        $orderModel= new OrderModel();
        $orderObj = $orderModel->whereIn('order_id',$order_ids)->select();
        if ($orderObj){
            $order = collection($orderObj)->append(['orderGoods','full_address'])->toArray();
        }
        $shop = tpCache('shop_info');
        $this->assign('order',$order);
        $this->assign('shop',$shop);
        $template = I('template','print');
        return $this->fetch($template);
    }
	
	/**
     *批量打印快递单
     */
    public function shippingPrintBatch(){
        $ids=I('post.ids3');
        $ids=substr($ids,0,-1);
        $ids=explode(',', $ids);
        if(!is_numeric($ids[0])){
            unset($ids[0]);
        }

        $shippings=array();
        foreach ($ids as $k => $v) {
            $shippings[$k]=$this->shipping_print($v);
        }
        $this->assign('shipping',$shippings);
        return $this->fetch("print_express");
    }
	
	public function delivery_info($id=''){
        if($id){
           $order_id=$id; 
        }else{
           $order_id = I('order_id');
        }

        $orderGoodsMdel = new OrderGoods();
        $orderModel = new OrderModel();
        $orderObj = $orderModel->where(['order_id'=>$order_id])->find();
        $order =$orderObj->append(['full_address'])->toArray();
        $orderGoods = $orderGoodsMdel::all(['order_id'=>$order_id,'is_send'=>['lt',2]]);
        if($id){
            if(!$orderGoods){
                $this->error('所选订单有商品已完成退货或换货');//已经完成售后的不能再发货
            }
        }else{
            if(!$orderGoods){
                $this->error('此订单商品已完成退货或换货');//已经完成售后的不能再发货  
            }
        }

        if($id){ 
            $order['orderGoods']=$orderGoods;
            $order['goods_num']=count($orderGoods);
            return $order;
        }else{
            $delivery_record = M('delivery_doc')->alias('d')->join('__ADMIN__ a','a.admin_id = d.admin_id')->where('d.order_id='.$order_id)->select();
            $invoice_no_arr = get_id_val($delivery_record ,'id','invoice_no');
            $this->assign('invoice_no_arr',$invoice_no_arr);
            $this->assign('order',$order);
            $this->assign('orderGoods',$orderGoods);
            $this->assign('delivery_record',$delivery_record);//发货记录
            $shipping_list = Db::name('shipping')->field('shipping_name,shipping_code')->select();
            $this->assign('shipping_list',$shipping_list);
            $express_switch = tpCache('express.express_switch');
            $this->assign('express_switch',$express_switch);
            return $this->fetch();    
        }
    }
	
	/**
     * 快递单打印
     */
    public function shipping_print($id=''){
        $orderLogic = new OrderLogic();
        $data['order_id'] = I('order_id/d');
        $data['shipping'] = 2;
        $data['send_type'] = 2;
		$data['group'] = 1;
		$data['admin_id'] = $this->supplier['suppliers_id'];
        $res = $orderLogic->deliveryHandle($data);
        if($res['status'] == 1){
            if($data['send_type'] == 2 && !empty($res['printhtml'])){
                $this->assign('printhtml',$res['printhtml']);
                return $this->fetch('print_online');
            }
        }else{
            $this->error($res['msg'],U('Supplier/Order/delivery_info',array('order_id'=>$data['order_id'])));
        }

    }
	
	/**
     * 生成发货单
     */
    public function deliveryHandle(){
        $orderLogic = new OrderLogic();
        $data = I('post.');
		$data['group'] = 1;
		$data['admin_id'] = $this->supplier['suppliers_id'];
        $res = $orderLogic->deliveryHandle($data);
        if($res['status'] == 1){
            if($data['send_type'] == 2 && !empty($res['printhtml'])){
                $this->assign('printhtml',$res['printhtml']);
                return $this->fetch('print_online');
            }
            $this->success('操作成功',U('Supplier/Order/delivery_info',array('order_id'=>$data['order_id'])));
        }else{
            $this->error($res['msg'],U('Supplier/Order/delivery_info',array('order_id'=>$data['order_id'])));
        }
    }
	
	/**
     * 取消电子面单
     */
    public function cancelEOrder(){
        $orderLogic = new OrderLogic();
        $data['rec_id'] = I('rec_id/a');
        $res = $orderLogic->cancelEOrder($data);
        $this->ajaxReturn($res);
    }
	
	/**
     * 发货单列表
     */
    public function deliveryList(){
        return $this->fetch();
    }
	
	/**
     * ajax 发货订单列表
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function ajaxDelivery(){
        $condition = array();
        I('consignee') ? $condition['consignee'] = ['like', '%'.trim(I('consignee')).'%'] : false;
        I('nickname') ? $condition['nickname'] = ['like', '%'.trim(I('nickname')).'%'] : false;
        I('order_sn') != '' ? $condition['order_sn'] = ['like', '%'.trim(I('order_sn')).'%'] : false;
        $shipping_status = I('shipping_status');
        $condition['shipping_status'] = empty($shipping_status) ? array('neq',1) : $shipping_status;
        $condition['order_status'] = array('in','1,2,4');
        $condition['prom_type'] = ['neq',5];
        $condition['shop_id'] = 0;
		$condition['suppliers_id'] = $this->supplier['suppliers_id'];
        $count = db('order')->alias('o')->join('__USERS__ u','u.user_id = o.user_id','left')->where($condition)->count();
        $Page  = new AjaxPage($count,10);
        //搜索条件下 分页赋值
        foreach($condition as $key=>$val) {
            if(!is_array($val)){
                $Page->parameter[$key]   =   urlencode($val);
            }
        }
        $show = $Page->show();
        if($shipping_status)
            $orderList = db('order')->alias('o')->join('__USERS__ u','u.user_id = o.user_id','left')->where($condition)->limit($Page->firstRow.','.$Page->listRows)->field("o.*,nickname,IF(shipping_name='','无需物流',shipping_name) as shipping_name")->order('add_time DESC')->select();
        else
            $orderList = db('order')->alias('o')->join('__USERS__ u','u.user_id = o.user_id','left')->where($condition)->field("o.*,nickname")->limit($Page->firstRow.','.$Page->listRows)->order('add_time DESC')->select();
        $this->assign('orderList',$orderList);
        $this->assign('page',$show);// 赋值分页输出
        $this->assign('pager',$Page);
        return $this->fetch();
    }
	
	/**
    *批量发货
    */
    public function delivery_batch(){
        /*code_18订单批量发货*/
        $ids=I('post.ids');
        $ids=substr($ids,0,-1);
        $ids=explode(',', $ids);
        if(!is_numeric($ids[0])){
            unset($ids[0]);
        }

        $orders=array();
        foreach ($ids as $k => $v) {
           $orders[$k]=$this->delivery_info($v);
        }
       
        $shipping_list = Db::name('shipping')->field('shipping_name,shipping_code')->where('')->select();
        $this->assign('orders',$orders);
        $this->assign('num',count($ids));
        $this->assign('shipping_list',$shipping_list);
        return $this->fetch();
        /*code_18订单批量发货*/
    }
	
	/**
     * 退货单列表
     */
    public function returnList(){
        return $this->fetch();
    }

    /*
     * ajax 退货订单列表
     */
    public function ajax_return_list(){
        // 搜索条件
        $order_sn =  trim(I('order_sn'));
        $order_by = I('order_by','') ? I('order_by') : 'id';
        $sort_order = I('sort_order') ? I('sort_order') : 'desc';
        $status =  I('status');
        $where = [];
        if($order_sn){
            $where['order_sn'] =['like', '%'.$order_sn.'%'];
        }
        if($status != ''){
            $where['status'] = $status;
        }
		$where['suppliers_id'] = $this->supplier['suppliers_id'];
        $ReturnGoods = new ReturnGoods();
        $count = $ReturnGoods->where($where)->count();
        $Page  = new AjaxPage($count,13);
        $show = $Page->show();
        $list = $ReturnGoods->where($where)->order("$order_by $sort_order")->limit("{$Page->firstRow},{$Page->listRows}")->select();
        $state = C('REFUND_STATUS');
        $return_type = C('RETURN_TYPE');
        $this->assign('state',$state);
        $this->assign('return_type',$return_type);
        $this->assign('list',$list);
        $this->assign('pager',$Page);
        $this->assign('page',$show);// 赋值分页输出
        return $this->fetch();
    }
	
	/**
     * 导出退换货订单
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function export_return_order()
    {
        //搜索条件
        $return_id = I('order_ids');
        $status = I('status');
        $return_type = C('RETURN_TYPE');//退货订单信息
        $refund_status = C('REFUND_STATUS');
        $returnGoods = new ReturnGoods();
        $where =[];
        if($return_id){
            $where['id'] = ['in',$return_id];
        }
        if($status !=''){
            $where['status'] = $status;
        }
		$where['suppliers_id'] = $this->supplier['suppliers_id'];
        $orderList = $returnGoods->where($where)->select();
        $strTable ='<table width="500" border="1">';
        $strTable .= '<tr>';
        $strTable .= '<td style="text-align:center;font-size:12px;width:120px;">订单编号</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">会员昵称</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="100">申请日期</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="200">退款详情</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">服务类型</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">售后申请原因</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">售后申请描述</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">审核状态</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">处理备注</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">商品数量</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">商品编码</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">商品名称</td>';
        $strTable .= '</tr>';
        if(is_array($orderList)){
            foreach($orderList as $k=>$val){
                $orderGoods = D('order_goods')->where(['rec_id'=>$val['rec_id']])->select();
                foreach($orderGoods as $goods){
                    $strTable .= '<tr>';
                    $strTable .= '<td style="text-align:center;font-size:12px;">&nbsp;'.$val['order']['order_sn'].'</td>';
                    $user_name = D('users')->where(['user_id'=>$val['order']['user_id']])->value('nickname');
                    $strTable .= '<td style="text-align:left;font-size:12px;">'.$user_name.' </td>';
                    $strTable .= '<td style="text-align:left;font-size:12px;">'.date('Y-m-d H:i:s',$val['addtime']).' </td>';
                    $str = '';
                   if($val['refund_money'] > 0){
                       $str .= '需退还金额：'.$val['refund_money'];
                   }
                    if($val['refund_deposit'] > 1){
                        $str .= '需退还余额：'.$val['refund_deposit'];
                    }
                    if($val['refund_integral'] > 2){
                        $str .= '需退还积分：'.$val['refund_integral'];
                    }
                    $strTable .= '<td style="text-align:left;font-size:12px;">'.$str.'</td>';

                    $strTable .= '<td style="text-align:left;font-size:12px;">'.$return_type[$val['type']].'</td>';
                    $strTable .= '<td style="text-align:left;font-size:12px;">'.$val['reason'].'</td>';
                    $strTable .= '<td style="text-align:left;font-size:12px;">'.$val['describe'].'</td>';
                    $strTable .= '<td style="text-align:left;font-size:12px;">'.$refund_status[$val['status']].'</td>';
                    $strTable .= '<td style="text-align:left;font-size:12px;">'.$val['remark'].'</td>';
                    $strGoods="";
                    $strGoods .= " 商品名称：".$goods['goods_name'];
                    if ($goods['spec_key_name'] != '') $strGoods .= " 规格：".$goods['spec_key_name'];
                    $strTable .= '<td style="text-align:left;font-size:12px;">'.$val['goods_num'].' </td>';
                    $strTable .= '<td style="text-align:left;font-size:12px;">'.$goods['goods_sn'].' </td>';
                    $strTable .= '<td style="text-align:left;font-size:12px;">'.$strGoods.' </td>';
                    $strTable .= '</tr>';
                }
                unset($orderGoods);

            }

        }
        $strTable .='</table>';
        unset($orderList);
        downloadExcel($strTable,'order');
        exit();
    }
	
	/**
     * 退换货操作
     */
    public function return_info()
    {
        $return_id = I('id');
        $return_goods = M('return_goods')->where(['id'=> $return_id])->find();
        !$return_goods && $this->error('非法操作!');
        $user = M('users')->where(['user_id' => $return_goods['user_id']])->find();
        $order = M('order')->where(array('order_id'=>$return_goods['order_id']))->find();
        $order['goods'] = M('order_goods')->where(['rec_id' => $return_goods['rec_id']])->find();
        $return_goods['delivery'] = unserialize($return_goods['delivery']);  //订单的物流信息，服务类型为换货会显示
        $return_goods['seller_delivery'] = unserialize($return_goods['seller_delivery']);  //订单的物流信息，服务类型为换货会显示
        if($return_goods['imgs']) $return_goods['imgs'] = explode(',', $return_goods['imgs']);
        $this->assign('id',$return_id); // 用户
        $this->assign('user',$user); // 用户
        $this->assign('return_goods',$return_goods);// 退换货
        $this->assign('order',$order);//退货订单信息
        $this->assign('return_type',C('RETURN_TYPE'));//退货订单信息
        $this->assign('refund_status',C('REFUND_STATUS'));
        return $this->fetch();
    }
	
	/**
     *确认收货和确认发货
     */
    public function checkReturnInfo()
    {
        $orderLogic = new OrderLogic();
        $post_data = I('post.');
        $return_goods = Db::name('return_goods')->where(['id'=>$post_data['id']])->find();
        !$return_goods && $this->ajaxReturn(['status'=>-1,'msg'=>'非法操作!']);
        $type_msg = C('RETURN_TYPE');
        $status_msg = C('REFUND_STATUS');
        $post_data['status'] == 3 && $post_data['receivetime'] = time(); //卖家收货时间
        if($return_goods['type'] > 0  && $post_data['status'] == 4){
            $post_data['seller_delivery']['express_time'] = date('Y-m-d H:i:s');
            $post_data['seller_delivery'] = serialize($post_data['seller_delivery']); //换货的物流信息
            Db::name('order_goods')->where(['rec_id'=>$return_goods['rec_id']])->save(['is_send'=>2]);
        }
        $note ="退换货:{$type_msg[$return_goods['type']]}, 状态:{$status_msg[$post_data['status']]},处理备注：{$post_data['remark']}";
        M('return_goods')->where(['id'=>$post_data['id']])->save($post_data);
        $commonOrder = new \app\common\logic\Order();
        $commonOrder->setOrderById($return_goods['order_id']);
        $commonOrder->orderActionLog($note,'退换货',$this->supplier['suppliers_id'],1);
        $this->ajaxReturn(['status'=>1,'msg'=>'修改成功','url'=>'']);
    }
}
