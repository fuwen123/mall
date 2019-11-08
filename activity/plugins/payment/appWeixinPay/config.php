<?php

return array(
    'code'=> 'appWeixinPay',
    'name' => 'App微信支付',
    'version' => '1.0',
    'author' => 'wangqh',
    'desc' => 'APP(安卓和苹果)微信支付',
    'icon' => 'logo.jpg',
    'scene' => 3,  // 使用场景 0 PC+手机 1 手机 2 PC ,3 APP
    'config' => array(
        array('name' => 'appid','label'=>'绑定支付的APPID','type' => 'text',   'value' => '','hint'=>''), // * APPID：绑定支付的APPID（必须配置，开户邮件中可查看）
        array('name' => 'mchid',   'label'=>'商户号', 'type' => 'text',   'value' => ''), // * MCHID：商户号（必须配置，开户邮件中可查看）
        array('name' => 'key',  'label'=>'商户支付密钥', 'type' => 'text',   'value' => ''), // KEY：商户支付密钥，参考开户邮件设置（必须配置，登录商户平台自行设置）
        array('name' => 'apiclient_cert',  'label'=>'商户支付证书文件apiclient_cert.pem', 'type' => 'file',   'value' => ''),// 小程序支付密钥文件 apiclient_pem
        array('name' => 'apiclient_key',  'label'=>'商户支付证书密钥apiclient_key.pem', 'type' => 'file',   'value' => ''),// 小程序支付密钥文件 apiclient_key
        
    ),
);