<?php

return array(
    'code'=> 'newalipay',
    'name' => '新PC&APP端支付宝',
    'version' => '1.0',
    'author' => 'jy_pwn',
    'desc' => '新PC&APP支付宝插件',
    'scene' =>2,  // 使用场景 0 PC+手机 1 手机 2 PC
    'icon' => 'logo.jpg',
    'config' => array(
        array('name' => 'app_id','label'=>'支付宝appid'  , 'description' => '' ,  'type' => 'text',   'value' => '' ),
        array('name' => 'merchant_private_key','label'=>'商户私钥' , 'description' => '商户私钥', 'type' => 'textarea',   'value' => '' ),
        array('name' => 'alipay_public_key','label'=>'支付宝公钥' , 'description' => '支付宝公钥', 'type' => 'textarea',   'value' => '' ),
        array('name' => 'is_bank','label'=>'是否开通网银'  , 'description' => '' ,        'type' => 'select', 'option' => array(
            '0' => '否',
            '1' =>  '是',
        ))   
    ),
    'bank_code'=>array(
            '招商银行'=>'CMB-DEBIT',
            '中国工商银行'=>'ICBC-DEBIT',
            '交通银行'=>'COMM-DEBIT',
            '中国建设银行'=>'CCB-DEBIT',
            '中国民生银行'=>'CMBC',
            '中国银行'=>'BOC-DEBIT',
            '中国农业银行'=>'ABC',        
            '上海银行'=>'SHBANK',                                           
    )
);