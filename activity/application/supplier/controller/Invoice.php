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
 * 发票控制器
 * Author: 545
 * Date: 2017-10-23
 */

namespace app\supplier\controller;

use think\AjaxPage;
use think\Db;
use think\Page;

class Invoice extends Base
{
    /*
     * 初始化操作
     */
    public function _initialize()
    {
        parent::_initialize();
        C('TOKEN_ON', false); // 关闭表单令牌验证
    }

    /*
     * 发票列表
     */
    public function index()
    {
        $invoice = new \app\common\model\Invoice();
        /*code_14发票模块逻辑代码*/
        //待开发票
        $this->assign('wait', $invoice->hasWhere("order",['order_sn'=>['>',0],'order_status'=>['in','1,2,4'],'suppliers_id'=>$this->supplier['suppliers_id']])->where(['status' => 0])->count());
        //累计开发票数
        $this->assign('total', $invoice->hasWhere("order",['order_sn'=>['>',0],'order_status'=>['in','1,2,4'],'suppliers_id'=>$this->supplier['suppliers_id']])->where(['status' => 1])->count());
        return $this->fetch();
        /*code_14发票模块逻辑代码*/
    }

    /**
     * 发票列表 ajax
     * @date 2017/10/23
     */
    public function ajaxindex()
    {
        /*code_14发票模块逻辑代码*/
        $begin = input("start_time");
        $end = input("end_time");
        $status = input('status');
        $map = [];
        if (!empty($begin) && !empty($end)) {
            $this->assign('start_time', $begin);
            $this->assign('end_time', $end);
            $map['ctime'] = array('between', array(strtotime($begin), strtotime($end) + 86399));
        }
        if($status >= 0){
            $map['status'] = $status;
        }
        $invoice = new \app\common\model\Invoice();
        $count = $invoice
			->hasWhere("order",['order_sn'=>['>',0],'order_status'=>['in','1,2,4'],'suppliers_id'=>$this->supplier['suppliers_id']])
			->where($map)
			->count();
        $Page = new AjaxPage($count, 15);
        $show = $Page->show();
        $invoice_list = $invoice
			->hasWhere("order",['order_sn'=>['>',0],'order_status'=>['in','1,2,4'],'suppliers_id'=>$this->supplier['suppliers_id']])
			->where($map)
			->limit($Page->firstRow . ',' . $Page->listRows)->order('invoice_id desc')->select();
        $this->assign('page', $show);
        $this->assign('pager', $Page);
        $this->assign('list', $invoice_list);
        return $this->fetch();
        /*code_14发票模块逻辑代码*/
    }

    //开票时间
    function changetime()
    {
        /*code_14发票模块逻辑代码*/
        $invoice_id = I('invoice_id');
        empty($invoice_id) && $this->ajaxReturn(['status' => -1, 'msg' => '无此发票记录', 'result' => '']);
        $map['invoice_id'] = $invoice_id;
        (M('invoice')->where($map)->save(['atime' => time()])) ? $status = 1 : $status = -1;
        $result = ['status' => $status, 'msg' => '', 'result' => ''];
        $this->ajaxReturn($result);
        /*code_14发票模块逻辑代码*/
    }

    public function exportInvoice()
    {
        /*code_14发票模块逻辑代码*/
        $invoice_ids = I('post.invoice_ids');
        $status = I('post.status');
        $where = ['i.ctime' => ['between', "$this->begin,$this->end"]];
        if ($invoice_ids) {
            $where['i.invoice_id'] = ['in', $invoice_ids];
        }
        if ($status >= 0) {
            $where['i.status'] = $status;
        }
        $list = Db::name('invoice')
			->alias('i')
			->join('order o', 'i.order_id=o.order_id', 'left')
			->where(['o.order_sn'=>['>',0],'o.order_status'=>['in','1,2,4'],'o.suppliers_id'=>$this->supplier['suppliers_id']])
			->where($where)
			->select();
        if (count($list) > 0) {
            $strTable = '<table width="500" border="1">';
            $strTable .= '<tr>';
            $strTable .= '<td style="text-align:center;font-size:12px;width:120px;">发票编号</td>';
            $strTable .= '<td style="text-align:center;font-size:12px;" width="150">订单编号</td>';
            $strTable .= '<td style="text-align:center;font-size:12px;" width="*">用户名</td>';
            $strTable .= '<td style="text-align:center;font-size:12px;" width="*">发票类型</td>';
            $strTable .= '<td style="text-align:center;font-size:12px;" width="*">开票金额</td>';
            $strTable .= '<td style="text-align:center;font-size:12px;" width="*">抬头</td>';
            $strTable .= '<td style="text-align:center;font-size:12px;" width="*">发票内容</td>';
            $strTable .= '<td style="text-align:center;font-size:12px;" width="*">发票税率</td>';
            $strTable .= '<td style="text-align:center;font-size:12px;" width="*">纳税人识别号</td>';
            $strTable .= '<td style="text-align:center;font-size:12px;" width="*">状态</td>';
            $strTable .= '<td style="text-align:center;font-size:12px;" width="*">创建时间</td>';
            $strTable .= '<td style="text-align:center;font-size:12px;" width="*">开票时间</td>';
            $strTable .= '</tr>';
            foreach ($list as $k => $val) {
                $strTable .= '<tr>';
                $strTable .= '<td style="text-align:center;font-size:12px;">&nbsp;' . $val['invoice_id'] . '</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;mso-number-format:\'\@\';">' . $val['order_sn'] . '</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">' . $val['nickname'] . '</td>';
                if ($val['invoice_type'] == 1) {
                    $invoice_type = "电子发票";
                } elseif ($val['invoice_type'] == 2) {
                    $invoice_type = "增值税发票";
                } else {
                    $invoice_type = "普通发票";
                }
                $strTable .= '<td style="text-align:left;font-size:12px;">' . $invoice_type . '</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">' . $val['invoice_money'] . '</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">' . $val['invoice_title'] . '</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">' . $val['invoice_desc'] . '</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">' . $val['invoice_rate'] . '</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;mso-number-format:\'\@\';">' . $val['taxpayer'] . '</td>';
                if ($val['status'] == 1) {
                    $status = "已开";
                } elseif ($val['status'] == 2) {
                    $status = "作废";
                } else {
                    $status = "待开";
                }
                $strTable .= '<td style="text-align:left;font-size:12px;">' . $status . '</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">' . date("Y-m-d H:i:s", $val['ctime']) . '</td>';
                ($val['status'] == 1) ? $atime = date("Y-m-d H:i:s", $val['atime']) : $atime = "";
                $strTable .= '<td style="text-align:left;font-size:12px;">' . $atime . '</td>';
                $strTable .= '</tr>';
            }
            $strTable .= '</table>';
            unset($list);
            downloadExcel($strTable, 'invoice');
            exit();
        }
        /*code_14发票模块逻辑代码*/
    }

}
