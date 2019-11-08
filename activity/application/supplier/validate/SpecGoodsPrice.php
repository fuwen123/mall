<?php
namespace app\supplier\validate;

use think\Validate;
use think\Db;

class SpecGoodsPrice extends Validate
{

    // 验证规则
    protected $rule = [
        'spec_cost_price' => ['require', 'regex' => '([1-9]\d*(\.\d*[1-9])?)|(0\.\d*[1-9])','checkPrice']
    ];
    //错误信息
    protected $message = [
        'spec_cost_price.require' => '商品模型中供货价必填',
        'spec_cost_price.regex' => '商品模型中供货价格式不对',
    ];

    protected function checkPrice($value, $rule, $data){
        if ($value < 0.01) {
            return  '商品模型中供货价不能小于0.01元';
        } else {
            return true;
        }
    }
}