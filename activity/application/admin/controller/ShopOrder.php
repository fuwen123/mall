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
use app\common\model\Order;
use app\common\model\Shop;
use app\common\util\TpshopException;
use think\Page;
use think\Db;

class ShopOrder extends Base {
    public  $order_status;
    public  $pay_status;
    public  $shipping_status;
    public function _initialize() {
        parent::_initialize();
        $this->order_status = C('ORDER_STATUS');
        $this->pay_status = C('PAY_STATUS');
        $this->shipping_status = C('SHIPPING_STATUS');
    }

    /**
     * 订单列表
     */
    public function index(){
        header("Content-type: text/html; charset=utf-8");
exit("商业用途必须购买正版,使用盗版将追究法律责任");
    }

    /**
     * 核销页
     */
    public function off()
    {
        $shop_order_wait_off_num = Db::name('shop_order')->alias('s')
            ->join('__ORDER__ o','o.order_id = s.order_id')->where(['s.is_write_off' => 0,'order_status'=>1,'o.prom_type'=>['eq',9]])->count('s.shop_order_id');
        $this->assign('shop_order_wait_off_num', $shop_order_wait_off_num);
        return $this->fetch();
    }
    /**
     * ajax 获取自提订单信息
     * order_id
     */
    public function getOrderGoodsInfo()
    {
        $order_id = input("order_id/d",0);
        $Order = new Order();
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
     * 数据统计
     */
    public function statistic()
    {
        $take_time_start = input('take_time_start/d', strtotime("-3 month"));
        $take_time_end = input('take_time_end/d', strtotime('+1 days'));
        $shop_id = input('shop_id/d');
        $where = ['o.pay_status' => 1, 'o.order_status' => ['in', [1, 2, 4]]];
        if($shop_id){
            $where['s.shop_id'] = $shop_id;
        }
        if($take_time_start || $take_time_end){
            $where['s.add_time'] = ['between', [$take_time_start, $take_time_end]];
        }
        $where['o.prom_type']= ['eq',9]; //自提订单prom_type为9
        $Shop = new Shop();
        $ShopOrder = new \app\common\model\ShopOrder();
        $now_date = date('Y-m-d');
        $shop_list = $Shop->where(['deleted'=>0])->cache(true)->select();
        $shop_order_today_count = $ShopOrder->alias('s')->join('__ORDER__ o','s.order_id = o.order_id')
            ->where(["DATE_FORMAT(FROM_UNIXTIME(s.add_time,'%Y-%m-%d'), '%Y-%m-%d')"=>['eq', $now_date], 'o.pay_status' => 1, 'o.order_status' => ['in', [1, 2, 4]],'o.prom_type'=> ['eq',9]])->count();//今日销售总额

        $shop_order_sum_list = $ShopOrder->alias('s')->join('__ORDER__ o','s.order_id = o.order_id')
            ->field("DATE_FORMAT(FROM_UNIXTIME(s.add_time,'%Y-%m-%d'), '%Y-%m-%d' ) as date,COUNT(s.shop_order_id) as order_count")->where($where)->group("date")->select();
        //$this->ajaxReturn($where );
        $this->assign('shop_list', $shop_list);
        $this->assign('take_time_start', $take_time_start);
        $this->assign('take_time_end', $take_time_end);
        $this->assign('shop_order_today_count', $shop_order_today_count);
        $this->assign('shop_order_sum_list', $shop_order_sum_list);
        //开始拼装数组
        $start_date = date("Y-m-d", $take_time_start);
        $end_date = date("Y-m-d", $take_time_end);
        $start_time = strtotime($start_date);
        $end_time = strtotime($end_date);
        $date_arr = [];
        $order_count_arr = [];
        while ($start_time <= $end_time) {
            $date_current = date('Y-m-d', $start_time);
            foreach ($shop_order_sum_list as $shop_order_sum) {
                if ($date_current == $shop_order_sum['date']) {
                    $order_count_arr[] = $shop_order_sum['order_count'];
                    break;
                }
            }
            $order_count_arr[] = 0;
            $date_arr[] = $date_current;//得到dataarr的日期数组。
            $start_time = $start_time + 86400;
        }
        $table['order_count_list'] = $order_count_arr;
        $table['date_list'] = $date_arr;
        $this->assign('table', json_encode($table));
        return $this->fetch();
    }


    /**
     * 导出订单
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function export_order()
    {
        $order_ids = I('order_ids');
        $shop_id = input('shop_id/d');
        $is_write_off = input('is_write_off');
        $add_time_start = input('add_time_start/s', date('Y-m-d H:i:s', strtotime("-3 month")));
        $add_time_end = input('add_time_end/s', date('Y-m-d H:i:s', strtotime(date('Y-m-d') . ' 23:59:59')));
        $take_time_start = input('take_time_start/s');
        $take_time_end = input('take_time_end/s');
        $keywords = input('keywords/s');
        $where = [];
        if($order_ids){
            $where['o.order_id'] = ['in', $order_ids];
        }
        if($shop_id){
            $where['s.shop_id'] = $shop_id;
        }
        if($is_write_off == '0' || $is_write_off){
            $where['s.is_write_off'] = $is_write_off;
            $where['o.order_status'] = 1;
        }
        if($add_time_start || $add_time_end){
            $add_time_start = str_replace('+',' ',$add_time_start);
            $add_time_end = str_replace('+',' ',$add_time_end);
            $where['o.add_time'] = ['between',[strtotime($add_time_start), strtotime($add_time_end)]];
        }
        if($keywords){
            $where['s.order_sn'] = $keywords;
        }
        if ($take_time_start || $take_time_end) {
            $where['s.take_time'] = ['between', [$take_time_start, $take_time_end]];
            unset($where['o.add_time']);
        }

        $where['o.prom_type'] = ['eq',9];//自提订单为9
        $ShopOrder = new \app\common\model\ShopOrder();
        $orderList = $ShopOrder->alias('s')->join('__ORDER__ o', 's.order_id = o.order_id')->where($where)->order('o.order_id')->select();
        $strTable ='<table width="500" border="1">';
        $strTable .= '<tr>';
        $strTable .= '<td style="text-align:center;font-size:12px;width:120px;">订单编号</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">会员昵称</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="100">日期</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">收货人</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">电话</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">自提点</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">自提时间</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">配送方式</td>';
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
            $region	= get_region_list();
            foreach($orderList as $k=>$val){
                $orderGoods = D('order_goods')->where('order_id='.$val['order_id'])->select();
                foreach($orderGoods as $goods){
                    $strTable .= '<tr>';
                    $strTable .= '<td style="text-align:center;font-size:12px;">&nbsp;'.$val['order_sn'].'</td>';
                    $user_name = D('users')->where(['user_id'=>$val['user_id']])->value('nickname');
                    $strTable .= '<td style="text-align:left;font-size:12px;">'.$user_name.' </td>';
                    $strTable .= '<td style="text-align:left;font-size:12px;">'.date('Y-m-d H:i:s',$val['add_time']).' </td>';
                    $strTable .= '<td style="text-align:left;font-size:12px;">'.$val['consignee'].'</td>';
                    $strTable .= '<td style="text-align:left;font-size:12px;">'.$val['mobile'].'</td>';
                    $strTable .= '<td style="text-align:left;font-size:12px;">'.$val['shop']['shop_name'].'</td>';
                    $strTable .= '<td style="text-align:left;font-size:12px;">'.$val['take_time'].'</td>';
                    $strTable .= '<td style="text-align:left;font-size:12px;">自提</td>';
                    $strTable .= '<td style="text-align:left;font-size:12px;">'.$goods['member_goods_price'].'</td>';
                    $strTable .= '<td style="text-align:left;font-size:12px;">'.$val['goods_price'].'</td>';
                    $strTable .= '<td style="text-align:left;font-size:12px;">'.$val['order_amount'].'</td>';
                    $strTable .= '<td style="text-align:left;font-size:12px;">'.$val['pay_name'].'</td>';
                    $strTable .= '<td style="text-align:left;font-size:12px;">'.$this->pay_status[$val['pay_status']].'</td>';
                    $strTable .= '<td style="text-align:left;font-size:12px;">'.$this->order_status[$val['order_status']].'</td>';
                    $strTable .= '<td style="text-align:left;font-size:12px;">'.$this->shipping_status[$val['shipping_status']].'</td>';
                    $strGoods="";
                    $goods_num = 0;
                    $goods_num = $goods_num + $goods['goods_num'];
                    $strGoods .= " 商品名称：".$goods['goods_name'];
                    if ($goods['spec_key_name'] != '') $strGoods .= " 规格：".$goods['spec_key_name'];
//	    			$strGoods .= "<br />";
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


}
