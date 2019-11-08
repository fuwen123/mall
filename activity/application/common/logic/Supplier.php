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

use app\common\util\TpshopException;
use think\Model;
use think\Db;

/**
 * 门店管理员类
 */
class Supplier extends Model
{
    private $supplier_account;
    //private $shopper;
    private $supplier;

    public function setSupplierAccount($supplier_account)
    {
        
        $this->supplier_account = $supplier_account;
        
    }

    public function login($password)
    {
        
        $this->supplier = Db::name('suppliers')->where('suppliers_account',  $this->supplier_account)->find();
		if(empty($this->supplier)){
            throw new TpshopException('供应商登录',0,['status' => 0, 'msg' => '供应商账号不存在']);
        }
        if($this->supplier['deleted'] == 1){
            throw new TpshopException('供应商登录',0,['status' => 0, 'msg' => '供应商已经被删除']);
        }
        if($this->supplier['is_check'] == 0){
            throw new TpshopException('供应商登录',0,['status' => 0, 'msg' => '供应商已关闭，请联系平台客服']);
        }
        $user_id = $this->supplier['user_id'];
        if(empty($user_id)){
            throw new TpshopException('供应商登录',0,['status' => 0, 'msg' => '供应商没有绑定前台会员']);
        }
		$user = Db::name('users')->where('user_id', $user_id)->where('password', $password)->find();
        if(!$user){
            throw new TpshopException('供应商登录',0,['status' => 0, 'msg' => '密码错误']);
        }
        session('supplier', $this->supplier);
        session('suppliers_id', $this->supplier['suppliers_id']);
        $last_login_time = time();
        Db::name('suppliers')->where('suppliers_id', $this->supplier['suppliers_id'])->update(['last_login_time' => $last_login_time]);
        $this->autoTransfer($this->supplier['suppliers_id']);
		$this->log("供应商管理中心登录");
		
    }

    /**
     * 管理员操作记录
     * @param $content|记录信息
     */
    public function log($content)
    {
        
        $log['log_time'] = time();
        $log['log_suppliers_id'] = $this->supplier['suppliers_id'];
        $log['log_suppliers_account'] = $this->supplier['suppliers_account'];
        $log['log_content'] = $content;
        $log['log_ip'] = request()->ip();
        $log['log_url'] = request()->action();
        Db::name('shopper_log')->add($log);
        
    }
	
	/**
     * 自动给供应商结算
     * @param $suppliers_id
     * @return bool
     */
    public function autoTransfer($suppliers_id){
        
        
        // 确认收货多少天后 自动结算给 商家
        $today_time = time();
        $auto_transfer_date = tpCache('shopping.auto_transfer_date');
        $time = strtotime("-$auto_transfer_date day");  // 计算N天以前的时间戳
        $where =[
            'suppliers_id' => $suppliers_id,
            'order_status'=> ['in','2,4'],
            'confirm_time'=> ['lt',$time],
            'order_statis_id' => 0,
			'deleted' => 0,
			'pay_status' => 1
        ];
        $list = Db::name('order')->where($where)->order('confirm_time')->select();
        if(empty($list)) return false;// 没有数据直接跳出
        $data = array(
            'start_date' => $list[0]['confirm_time'], // 结算开始时间
            'end_date'   => $time, //结算截止时间
            'create_date'=>  $today_time, // 记录创建时间            
            'suppliers_id'   => $suppliers_id, // 店铺id
			'sum_cost_price' => 0, //供货总价
			'sum_shipping_price' => 0, //运费总价
			'sum_real_postage' => 0,  //真实运费总价，sum_shipping_price是对会员而言的总运费（免邮的订单运费不算），sum_real_postage是对供应商而言的运费（免邮时订单的运费也算）
			'order_amount' => 0,  //订单总价
        );
        foreach($list as $key => $val)
        {
        	$return_goods = M('return_goods')->where("order_id = {$val['order_id']} and status not in (-2,5)")->select();
        	if($return_goods) continue;//如果有售后申请未完成，则不结算
			$order_goods = M('order_goods')->where(array('order_id' => $val['order_id']))->select();//订单商品
			foreach ($order_goods as $k => $v) {
				if ($v['is_send'] == 1  || $v['is_send'] == 2) {
					$data['sum_cost_price'] += $v['cost_price'] * $v['goods_num'];
					$data['order_amount'] += $v['member_goods_price'] * $v['goods_num'];
				}
			}
			$data['sum_shipping_price'] += $val['shipping_price'];
            $data['sum_real_postage'] += $val['real_shipping_price'];
            $order_id_arr[] = $val['order_id'];
        }
		$data['statis_totals'] = $data['sum_cost_price'] + $data['sum_real_postage']; //应付金额（供货总价+真实运费）
        if(!empty($order_id_arr)){
            $order_statis_id = M('order_statis')->add($data); // 添加一笔结算统计
            $order_ids = implode(',', $order_id_arr);
            Db::name('order')->whereIn('order_id',$order_ids)->save(array('order_statis_id' =>$order_statis_id)); // 标识为已经结算
            // 给供应商加钱 记录日志
            supplier_account_log($suppliers_id, $data['statis_totals'], '平台订单结算');
        }
        
    }
}