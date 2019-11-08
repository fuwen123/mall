<?php
return	array(
	'goods'=>array('name'=>'商品','child'=>array(
		array('name' => '商品管理','child' => array(
			array('name'=>'商品列表','act'=>'index','op'=>'Goods'),
			array('name'=>'库存管理','act'=>'stockList','op'=>'Goods'),
			array('name'=>'发布商品','act'=>'addEditGoods','op'=>'Goods'),
			array('name'=>'待审核商品','act'=>'auditGoodsList','op'=>'Goods'),
		)),
	)),
	'order'=>array('name'=>'订单','child'=>array(
		array('name' => '订单管理','child'=>array(
			array('name' => '订单列表', 'act'=>'index', 'op'=>'Order'),
			array('name' => '发货单', 'act'=>'deliveryList', 'op'=>'Order'),
			array('name' => '退换货申请', 'act'=>'returnList', 'op'=>'Order'),
			array('name' => '发票管理', 'act'=>'index', 'op'=>'Invoice'),
		)),
	)),
	'finance'=>array('name'=>'结算','child'=>array(
		array('name' => '结算管理','child' => array(
			array('name'=>'订单结算记录','act'=>'index','op'=>'Finance'),
			array('name'=>'未结算订单','act'=>'orderList','op'=>'Finance'),
			array('name'=>'提现申请','act'=>'withdrawalsList','op'=>'Finance'),
			array('name'=>'转账记录','act'=>'remittance','op'=>'Finance'),
		)),
	)),
	'system'=>array('name'=>'系统','child'=>array(
		array('name' => '基本设置','child' => array(
			array('name'=>'基本资料','act'=>'index','op'=>'System'),
			array('name'=>'密码管理','act'=>'password','op'=>'System'),
		)),
		array('name' => '物流配送','child' => array(
			array('name'=>'运费模板','act'=>'freight','op'=>'System'),
		)),
	)),
);