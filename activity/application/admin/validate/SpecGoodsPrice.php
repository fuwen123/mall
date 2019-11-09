<?php
namespace app\admin\validate;

use think\Validate;
use think\Db;

class SpecGoodsPrice extends Validate
{

    // 验证规则
    protected $rule = [
        'price' => ['require', 'regex' => '([1-9]\d*(\.\d*[1-9])?)|(0\.\d*[1-9])','checkPrice'],
        'commission' => 'checkCommission'
    ];
    //错误信息
    protected $message = [
        'price.require' => '商品模型中购买价必填',
        'price.regex' => '商品模型中购买价格式不对',
    ];

    protected function checkPrice($value, $rule, $data){
        if ($value < 0.01) {
            return  '商品模型中购买价不能小于0.01元';
        } elseif ($value < $data['cost_price']) {
			return  '商品模型中购买价不能小于成本价';
		}else {
            return true;
        }
    }
    
    protected function checkCommission($value, $rule, $data)
    {
        if ($value > $data['price']) {
            return '商品模型中购买价，商品分销的分成金额不能大于等于购买价金额';
        } else {
            return true;
        }
    }
}