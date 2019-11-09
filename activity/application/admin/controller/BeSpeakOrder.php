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
use app\common\logic\ShopTemplate;
use app\common\model\BespeakTemplateUnit;
use app\common\model\Order;
use app\common\model\Goods;
use app\common\model\Shop;
use app\common\util\TpshopException;
use think\Page;
use think\Db;

class BeSpeakOrder extends Base {
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
        /*code_16门店管理模块代码*/
        $shop_id = input('shop_id/d');
        $is_write_off = input('is_write_off');
        $add_time_start = input('add_time_start/s', date('Y-m-d H:i:s', strtotime("-3 month")));
        $add_time_end = input('add_time_end/s', date('Y-m-d H:i:s', strtotime(date('Y-m-d') . ' 23:59:59')));
        $take_time_start = input('take_time_start/s');
        $take_time_end = input('take_time_end/s');
        $statistic = input('statistic/d');//是否是从数据统计页进来的
        $pay_status = input('pay_status/d',-1);
        $mobile = input('mobile/s');
        $take_time = input('take_time/s');
        $keywords = input('keywords/s');
        $where = [];
        if($shop_id){
            $where['s.shop_id'] = $shop_id;
        }
        if($mobile){
            $where['o.mobile'] = $mobile;
        }
        if($take_time){
            $where['s.take_time'] = $take_time;
        }
        if($pay_status >=0){
            $where['o.pay_status'] = $pay_status;
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
        if ($statistic) {
            $where['o.pay_status'] = 1;
            $where['o.order_status'] = ['in', [1, 2, 4]];
        }
        $where['prom_type'] = ['eq',7];  //预约订单类型为7
        //$where['s.goods_id'] = ['>',0];  //区分自提订单
        $ShopOrder = new \app\common\model\ShopOrder();

        $count = $ShopOrder->alias('s')->join('__ORDER__ o', 's.order_id = o.order_id')->where($where)->group('s.order_id')->count('s.shop_order_id');
        $Page = new Page($count, 10);
        $list = $ShopOrder->alias('s')->join('__ORDER__ o', 's.order_id = o.order_id')->where($where)->limit($Page->firstRow.','.$Page->listRows)->order(['o.add_time'=>'desc'])->group('s.order_id')->select();
        $Shop = new Shop();
        $shop_list = $Shop->where(['deleted'=>0])->cache(true)->select();
        $this->assign('shop_list', $shop_list);
        $this->assign('page', $Page);
        $this->assign('list', $list);
        $this->assign('pay_status', $pay_status);
        $this->assign('mobile', $mobile);
        $this->assign('take_time', $take_time);
        $this->assign('add_time_start', $add_time_start);
        $this->assign('add_time_end', $add_time_end);
        return $this->fetch();
        /*code_16门店管理模块代码*/
    }

    /**
     * 核销页
     */
    public function off()
    {
        $shop_order_wait_off_num = Db::name('shop_order')->alias('s')
            ->join('__ORDER__ o','o.order_id = s.order_id')->where(['s.is_write_off' => 0,'s.goods_id' =>['>',0],'o.order_status'=>1,'o.prom_type'=>7])->count('s.shop_order_id');
        $this->assign('shop_order_wait_off_num', $shop_order_wait_off_num);
        return $this->fetch();
    }
    /**
     * ajax 获取预约订单信息
     * order_id
     */
    public function getOrderGoodsInfo()
    {
        $order_id = input("order_id/d",0);
        $Order = new Order();
        $order = $Order->with(['shop','shop_order'])->where(['order_id'=>$order_id])->find();
        foreach ($order->shop_order as $k =>$v){
            $v->append(['shop_code']);
        }
        $this->ajaxReturn($order);
    }
    /**
     * ajax 获取预约表单信息
     * order_id
     */
    public function getOrderBespeakFormInfo()
    {
        $order_id = input("order_id/d",0);
        $BespeakTemplateUnit = new BespeakTemplateUnit();
        //获取表信息
        $order_bespeak = $BespeakTemplateUnit::hasWhere('OrderBespeak',['order_id'=>$order_id])->field('OrderBespeak.*')->order('sort desc')->select();
        $this->ajaxReturn($order_bespeak);
    }
    /**
     * 核销
     */
    public function writeOff()
    {
        $shop_order_code = input('shop_order_id/a', 0);
        $shop_order_code = array_values(array_filter($shop_order_code));
        $order_id = input('order_id/d', 0);
        $ShopOrderLogic = new \app\common\logic\BeSpeakOrder();
        $ShopOrderLogic->setShopDatarModel($shop_order_code);
        if($order_id){
            //在订单详细里面验证
            $ShopOrderLogic->setShopOrderById($order_id);
        }else{
            //在外面核销
            $ShopOrderLogic->setShopOrderFindById();
        }
        try {
            $ShopOrderLogic->writeOff();
            $this->ajaxReturn(['status' => 1, 'msg'=>'核销成功']);
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
//            $where['s.take_time'] = ['between', [date('Y-m-d',$take_time_start), date('Y-m-d',$take_time_end)]];
            $where['s.add_time'] = ['between', [$take_time_start, $take_time_end]];
        }
        $where['o.prom_type']= ['eq',7]; //7为预约订单
        $Shop = new Shop();
        $ShopOrder = new \app\common\model\ShopOrder();
        $now_date = date('Y-m-d');
        $shop_list = $Shop->where(['deleted'=>0])->cache(true)->select();
        $shop_order_today_count = $ShopOrder->alias('s')->join('__ORDER__ o','s.order_id = o.order_id')
            ->where(["DATE_FORMAT(FROM_UNIXTIME(s.add_time,'%Y-%m-%d'), '%Y-%m-%d')"=>['eq', $now_date],'s.goods_id' =>['>',0], 'o.pay_status' => 1, 'o.order_status' => ['in', [1, 2, 4]]])->group("s.order_id")->count('s.order_id');//今日销售总额
        //COUNT(DISTINCT s.order_id) DISTINCT 函数返回指定列的不同值的数目：类似group分组
        $shop_order_sum_list = $ShopOrder->alias('s')->join('__ORDER__ o','s.order_id = o.order_id')
            ->field("DATE_FORMAT(FROM_UNIXTIME(s.add_time,'%Y-%m-%d'), '%Y-%m-%d' ) as date,COUNT(DISTINCT s.order_id) as order_count")->where($where)->order('s.add_time desc')->group("date")->select();
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
            $order_count_arr_status = 1;
            foreach ($shop_order_sum_list as $shop_order_sum) {
                if ($date_current == $shop_order_sum['date']) {
                    $order_count_arr_status =0;
                    $order_count_arr[] = $shop_order_sum['order_count'];
                    break;
                }
            }
            if($order_count_arr_status){
                $order_count_arr[] = 0;
            }
            $date_arr[] = $date_current;//得到dataarr的日期数组。
            $start_time = $start_time + 86400;
        }
        $table['order_count_list'] = $order_count_arr;
        $table['date_list'] = $date_arr;
        $this->assign('table', json_encode($table));
        return $this->fetch();
    }


    /**
     * 预约详情
     */
    public function shop_info(){
        $order_id = I('order_id/d');
        // 获取操作表
        $Order = new Order();
        $ShopOrder = new \app\common\model\ShopOrder();
        $order = $Order::get(['order_id'=>$order_id]);
        $shop_order = $ShopOrder->where(array('order_id'=>$order_id))->select();
        if($order['pay_status'] == 1){
            if(!empty($shop_order)){
                foreach($shop_order as $shopKey => $shopVal){
                    if(strtotime($shopVal['take_time']) < time()&& $shopVal['is_write_off'] == 0 ){ //看看有没有过期
                        $shop_order[$shopKey]['is_write_off'] = 2;
                        $shopVal->is_write_off = 2;
                        $shopVal->save();
                        (new \app\common\logic\BeSpeakOrder())->setInvalidRefund(['goods_id'=>$shopVal->goods_id,'order_id'=>$shopVal->order_id]);

                    }
                }
            }
        }
        $this->assign('shop_order',$shop_order);
        $BespeakTemplateUnit =  new BespeakTemplateUnit();
        //获取表信息
        $order_bespeak = $BespeakTemplateUnit::hasWhere('OrderBespeak',['order_id'=>$order_id])->field('OrderBespeak.*')->order('sort desc')->select();
        $this->assign('order_bespeak', $order_bespeak);
        $this->assign('order',$order);
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
//        $keyType =   I("key_type");  //查找类型
//        $keywords = I('keywords','','trim');
//        $where= ['add_time'=>['between',"$this->begin,$this->end"]];
//        if(!empty($keywords)){
//            $keyType == 'mobile'   ? $where['mobile']  = $keywords : false;
//            $keyType == 'order_sn' ? $where['order_sn'] = $keywords: false;
//            $keyType == 'consignee' ? $where['consignee'] = $keywords: false;
//        }
        $shop_id = input('shop_id/d');
        $is_write_off = input('is_write_off');
        $add_time_start = input('add_time_start/s', date('Y-m-d H:i:s', strtotime("-3 month")));
        $add_time_end = input('add_time_end/s', date('Y-m-d H:i:s', strtotime(date('Y-m-d') . ' 23:59:59')));
        $take_time_start = input('take_time_start/s');
        $take_time_end = input('take_time_end/s');
        $statistic = input('statistic/d');//是否是从数据统计页进来的
        $pay_status = input('pay_status/d',-1);
        $mobile = input('mobile/s');
        $take_time = input('take_time/s');
        $keywords = input('keywords/s');
        $where = [];
        if($shop_id){
            $where['s.shop_id'] = $shop_id;
        }
        if($mobile){
            $where['o.mobile'] = $mobile;
        }
        if($take_time){
            $where['s.take_time'] = $take_time;
        }
        if($pay_status >=0){
            $where['o.pay_status'] = $pay_status;
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
        if ($statistic) {
            $where['o.pay_status'] = 1;
            $where['o.order_status'] = ['in', [1, 2, 4]];
        }
        $where['prom_type'] = 7;
        if($order_status>-1 && $order_status != ''){
            $where['order_status'] = $order_status;
        }
        if($order_ids){
            $where['o.order_id'] = ['in', $order_ids];
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

        $ShopOrder = new \app\common\model\ShopOrder();
        $orderList = $ShopOrder->alias('s')->join('__ORDER__ o', 's.order_id = o.order_id')->where($where)->order(['o.add_time'=>'desc'])->group('s.order_id')->select();
        $strTable ='<table width="500" border="1">';
        $strTable .= '<tr>';
        $strTable .= '<td style="text-align:center;font-size:12px;width:130px;">订单编号</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;width:120px;">预约门店</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">预约人</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">预约人电话</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">预约号</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">使用状态</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;width:120px;">预约时间</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="100">下单日期</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">商品价格</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">订单金额</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">实际支付</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">支付方式</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">支付状态</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">订单状态</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">商品编码</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">商品名称</td>';
        $strTable .= '</tr>';
        if(is_array($orderList)){
            foreach($orderList as $k=>$val){
                $orderGoods = D('order_goods')->where('order_id='.$val['order_id'])->select();
                foreach($orderGoods as $goods){
                    $strTable .= '<tr>';
                    $strTable .= '<td style="text-align:center;font-size:12px;">&nbsp;'.$val['order_sn'].'</td>';
                    $strTable .= '<td style="text-align:center;font-size:12px;">&nbsp;'.$val['shop']['shop_name'].'</td>';
                    $strTable .= '<td style="text-align:left;font-size:12px;">'.$val['consignee'].'</td>';
                    $strTable .= '<td style="text-align:left;font-size:12px;">'.$val['mobile'].'</td>';
                    $strTable .= '<td style="text-align:left;font-size:12px;">'.$val['shop_code'].'</td>';
                    $strTable .= '<td style="text-align:left;font-size:12px;">'.($val['is_write_off']==1?"已核销":"未核销").'</td>';
                    $strTable .= '<td style="text-align:left;font-size:12px;">'.$val['take_time'].' </td>';
                    $strTable .= '<td style="text-align:center;font-size:12px;">&nbsp;'.date('Y-m-d H:i:s',$val['add_time']).'</td>';
                    $strTable .= '<td style="text-align:left;font-size:12px;">'.$goods['member_goods_price'].'</td>';
                    $strTable .= '<td style="text-align:left;font-size:12px;">'.$val['goods_price'].'</td>';
                    $strTable .= '<td style="text-align:left;font-size:12px;">'.$val['order_amount'].'</td>';
                    $strTable .= '<td style="text-align:left;font-size:12px;">'.$val['pay_name'].'</td>';
                    $strTable .= '<td style="text-align:left;font-size:12px;">'.$this->pay_status[$val['pay_status']].'</td>';
                    $strTable .= '<td style="text-align:left;font-size:12px;">'.$this->order_status[$val['order_status']].'</td>';
                    $strGoods="";
                    $strGoods .= " 商品名称：".$goods['goods_name'];
                    if ($goods['spec_key_name'] != '') $strGoods .= " 规格：".$goods['spec_key_name'];
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
