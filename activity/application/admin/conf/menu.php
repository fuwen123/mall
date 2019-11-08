<?php
$menu = (IS_SAAS == '1') ? array(
        array('name' => '首页装修', 'act'=>'templateList', 'op'=>'Block'),
        array('name' => '模板分类管理', 'act'=>'template_class', 'op'=>'Block', 'admin_saas'=>1),
		array('name' => '行业模板设置', 'act'=>'templateList2', 'op'=>'Block',  'admin_saas'=>1),
		array('name' => '自定义页面', 'act'=>'pageList', 'op'=>'Block'),
		array('name' => '会员中心自定义', 'act'=>'user_center_menu', 'op'=>'System'),
		array('name' => '模板切换', 'act'=>'change', 'op'=>'Template'),
		array('name'=>'PC端导航栏','act'=>'navigationList','op'=>'System'),
) : array(
        array('name' => '首页装修', 'act'=>'templateList', 'op'=>'Block'),
        array('name' => '模板分类管理', 'act'=>'template_class', 'op'=>'Block', 'admin_saas'=>1),
		array('name' => '自定义页面', 'act'=>'pageList', 'op'=>'Block'),
		array('name' => '会员中心自定义', 'act'=>'user_center_menu', 'op'=>'System'),
		array('name' => '模板切换', 'act'=>'change', 'op'=>'Template'),
		array('name'=>'PC端导航栏','act'=>'navigationList','op'=>'System'),
);
return	array(	
	'index'=>array('name'=>'首页','child'=>array(
			array('name' => '首页','child' => array(
				array('name' => '后台首页', 'act'=>'index', 'op'=>'Index'),
			)),
	)),

	'system'=>array('name'=>'设置','child'=>array(
				array('name' => '系统设置','child' => array(
						array('name'=>'商城配置','act'=>'index','op'=>'System'),
                        array('name' => '积分兑换','act'=>'index', 'op'=>'IntegralMall'),
                        array('name'=>'提现设置','act'=>'cash','op'=>'System'),
                        array('name'=>'签到规则','act'=>'signRule','op'=>'User'),
						array('name'=>'地区&配送','act'=>'region','op'=>'Tools'),
						array('name'=>'短信模板','act'=>'index','op'=>'SmsTemplate'),
						array('name'=>'消息通知','act'=>'index','op'=>'MessageTemplate'),
						array('name'=>'清除缓存','act'=>'cleanCache','op'=>'System'),
						array('name' => '运费模板', 'act'=>'index', 'op'=>'Freight'),
						array('name' => '快递公司', 'act'=>'index', 'op'=>'Shipping'),
				)),

				array('name' => '插件设置','child'=>array(
					array('name' => '插件配置', 'act'=>'index', 'op'=>'Plugin'),
				)),

				array('name' => '权限设置','child'=>array(
						array('name' => '管理员列表', 'act'=>'index', 'op'=>'Admin'),
						array('name' => '角色管理', 'act'=>'role', 'op'=>'Admin'),
						array('name'=>'权限资源列表','act'=>'right_list','op'=>'System'),
						//array('name' => '管理员日志', 'act'=>'log', 'op'=>'Admin'),
				)),
			
				array('name' => '数据设置','child'=>array(
						array('name' => '数据备份', 'act'=>'index', 'op'=>'Tools'),
						array('name' => '数据还原', 'act'=>'restore', 'op'=>'Tools'),
                        array('name' => '清空演示数据', 'act'=>'clear_demo_data', 'op'=>'Tools'),						
						//array('name' => 'ecshop数据导入', 'act'=>'ecshop', 'op'=>'Tools'),
						//array('name' => '淘宝csv导入', 'act'=>'taobao', 'op'=>'Tools'),
						//array('name' => 'SQL查询', 'act'=>'log', 'op'=>'Admin'),
				)),
	)),

	'decorate'=>array('name'=>'页面','child'=>array(
		array('name' => '页面管理','child'=>$menu),
		array('name' => '内容管理','child'=>array(
                array('name' => '文章列表', 'act'=>'articleList', 'op'=>'Article'),
                array('name' => '帮助分类', 'act'=>'categoryList', 'op'=>'Article'),
                array('name' => '会员协议', 'act'=>'agreement', 'op'=>'Article'),
                array('name' => '新闻列表', 'act'=>'newsList', 'op'=>'News'),
                array('name' => '新闻分类', 'act'=>'categoryList', 'op'=>'News'),
                array('name' => '智能表单', 'act'=>'form_list', 'op'=>'Block',  'admin_saas'=>1),

				array('name'=>'友情链接','act'=>'linkList','op'=>'Article'),
		)),
        array('name' => '广告管理','child' => array(
                array('name'=>'广告列表','act'=>'adList','op'=>'Ad'),
        )),
	)),


	'goods'=>array('name'=>'商品','child'=>array(
				array('name' => '商品管理','child' => array(
				    array('name' => '商品列表', 'act'=>'goodsList', 'op'=>'Goods'),
					array('name' => '商品标签', 'act'=>'goods_label', 'op'=>'Goods'),
                    array('name' => '品牌列表', 'act'=>'brandList', 'op'=>'Goods'),
                    array('name' => '库存日志', 'act'=>'stockList', 'op'=>'Goods'),
                    array('name' => '库存预警', 'act'=>'lowStockWarn', 'op'=>'Goods'),
                    array('name' => '库存盘点', 'act'=>'alterStock', 'op'=>'Goods'),
                    array('name' => '淘宝导入', 'act'=>'index', 'op'=>'Import'),
                )),
            array('name' => '商品属性','child' => array(
                array('name' => '商品分类', 'act'=>'categoryList', 'op'=>'Goods'),
                array('name' => '商品模型', 'act'=>'type_list', 'op'=>'Goods'),
            )),
            array('name' => '评论管理','child' => array(
                array('name' => '评论列表', 'act'=>'index', 'op'=>'Comment'),
                array('name' => '商品咨询', 'act'=>'ask_list', 'op'=>'Comment'),
            )),
	)),


    'order'=>array('name'=>'订单','child'=>array(
			array('name' => '订单管理','child'=>array(
				array('name' => '订单列表', 'act'=>'index', 'op'=>'Order'),
				array('name' => '虚拟订单', 'act'=>'virtual_list', 'op'=>'Order'),
                array('name' => '拼团列表','act'=>'team_list','op'=>'Team'),
                array('name' => '拼团订单','act'=>'order_list','op'=>'Team'),
                array('name' => '订单日志','act'=>'order_log','op'=>'Order'),
                array('name' => '添加订单', 'act'=>'add_order', 'op'=>'Order'),
            )),
            array('name' => '订单处理','child'=>array(
                array('name' => '退款单', 'act'=>'refund_order_list', 'op'=>'Order'),
                array('name' => '退换货', 'act'=>'return_list', 'op'=>'Order'),
                array('name' => '发货单', 'act'=>'delivery_list', 'op'=>'Order'),
                array('name' => '批量发货', 'act'=>'delivery_excel', 'op'=>'Order'),
                array('name' => '发票管理','act'=>'index', 'op'=>'Invoice'),
            )),
            array('name' => '配送服务','child'=>array(
                array('name' => '预约管理','act'=>'index','op'=>'BeSpeakOrder'),
                array('name' => '上门自提','act'=>'index','op'=>'ShopOrder'),
            )),
    )),
    
	'member'=>array('name'=>'会员','child'=>array(
		array('name' => '会员管理','child'=>array(
			array('name'=>'会员列表','act'=>'index','op'=>'User'),
			array('name'=>'会员等级','act'=>'levelList','op'=>'User'),
		)),
		array('name' => '充值提现','child'=>array(
			array('name'=>'充值记录','act'=>'recharge','op'=>'User'),
			array('name'=>'提现申请','act'=>'withdrawals','op'=>'User'),
			array('name'=>'汇款记录','act'=>'remittance','op'=>'User'),
			
		)),
		array('name' => '签到管理','child'=>array(
			array('name'=>'签到记录','act'=>'signList','op'=>'User'),
		)),
	)),
	
	'store'=>array('name'=>'门店','child'=>array(
		array('name' => '门店管理','child'=>array(
			array('name'=>'门店列表','act'=>'index','op'=>'Shop'),
			array('name'=>'门店订单','act'=>'index','op'=>'ShopOrder'),
		)),
	)),
	
	'supplier'=>array('name'=>'供应商','child'=>array(
		array('name' => '供应商管理','child'=>array(
			array('name'=>'供应商列表','act'=>'index','op'=>'Supplier'),
		)),
		array('name' => '商品管理','child'=>array(
			array('name'=>'待审核商品','act'=>'auditGoodsList','op'=>'Goods'),
			array('name'=>'商品列表','act'=>'supplierGoodsList','op'=>'Goods'),
		)),
		array('name' => '订单管理','child'=>array(
			array('name'=>'订单列表','act'=>'supplierOrderList','op'=>'Order'),
			array('name'=>'退款单','act'=>'supplierRefundList','op'=>'Order'),
			array('name'=>'退换货','act'=>'supplierReturnList','op'=>'Order'),
			array('name'=>'发票列表','act'=>'supplierInvoice','op'=>'Invoice'),
		)),
		array('name' => '财务管理','child'=>array(
			array('name'=>'提现申请','act'=>'withdrawalsList','op'=>'Supplier'),
			array('name'=>'转款列表','act'=>'remittance','op'=>'Supplier'),
			array('name'=>'结算记录','act'=>'orderStatis','op'=>'Supplier'),
		)),
	)),
		
	'marketing'=>array('name'=>'营销','child'=>array(
			array('name' => '常用促销','child' => array(
					array('name' => '营销菜单', 'act'=>'index_list', 'op'=>'Promotion'),
					array('name' => '抢购管理', 'act'=>'flash_sale', 'op'=>'Promotion'),
					array('name' => '团购管理', 'act'=>'group_buy_list', 'op'=>'Promotion'),
					array('name' => '优惠促销', 'act'=>'prom_goods_list', 'op'=>'Promotion'),
					array('name' => '订单促销', 'act'=>'prom_order_list', 'op'=>'Promotion'),
					array('name' => '预售管理','act'=>'index', 'op'=>'PreSell'),
					array('name' => '搭配购管理','act'=>'index', 'op'=>'Combination'),
					array('name' => '砍价管理','act'=>'index', 'op'=>'PromotionBargain'),
			)),
			array('name' => '拼团购','child' => array(
                    array('name' => '分享团','act'=>'index', 'op'=>'Team'),
                    array('name' => '佣金团','act'=>'index', 'op'=>'Team'),
                    array('name' => '抽奖团','act'=>'index', 'op'=>'Team'),
            )),
			array('name' => '优惠券','child' => array(
				array('name' => '优惠券','act'=>'index', 'op'=>'Coupon'),
//                array('name' => '新人好礼','act'=>'index', 'op'=>'Coupon'),
				array('name' => '发放记录','act'=>'send_list', 'op'=>'Coupon'),
			)),
//			array('name' => '互动营销','child' => array(
//                array('name' => '砸金蛋','act'=>'', 'op'=>''),
//                array('name' => '猜单双','act'=>'', 'op'=>''),
//                array('name' => '爱心助力','act'=>'', 'op'=>''),
//                array('name' => '拆红包','act'=>'', 'op'=>''),
//                array('name' => '刮刮奖','act'=>'', 'op'=>''),
//                array('name' => '大转盘抽奖','act'=>'', 'op'=>''),
//            )),
	)),
		
	'distribution'=>array('name'=>'分销','child'=>array(
			array('name' => '分销管理','child' => array(
					array('name' => '分销商品', 'act'=>'goods_list', 'op'=>'Distribut'),
					array('name' => '分销商列表', 'act'=>'distributor_list', 'op'=>'Distribut'),
					array('name' => '分销关系', 'act'=>'tree', 'op'=>'Distribut'),
					array('name' => '分销商等级', 'act'=>'grade_list', 'op'=>'Distribut'),
                    array('name' => '分销设置', 'act'=>'distribut', 'op'=>'System'),
			)),
        array('name' => '佣金管理','child' => array(
            array('name' => '分成日志', 'act'=>'rebate_log', 'op'=>'Distribut'),
        )),
	)),
	
	'data'=>array('name'=>'数据','child'=>array(
			array('name' => '交易数据','child' => array(
					array('name' => '销售概况', 'act'=>'index', 'op'=>'Report'),
					array('name' => '销售排行', 'act'=>'saleTop', 'op'=>'Report'),
					array('name' => '销售明细', 'act'=>'saleList', 'op'=>'Report'),
			)),
            array('name' => '会员数据','child' => array(
                array('name' => '会员排行', 'act'=>'userTop', 'op'=>'Report'),
                array('name' => '会员统计', 'act'=>'user', 'op'=>'Report'),
                array('name' => '登录分析', 'act'=>'login', 'op'=>'Report'),
            )),
            array('name' => '运营数据','child' => array(
                array('name' => '运营概览', 'act'=>'finance', 'op'=>'Report'),
                array('name' => '平台支出记录','act'=>'expense_log','op'=>'Report'),
            )),

	)),
    'minapp'=>array('name'=>'APP&小程序','child'=>array(
        array('name' => '小程序', 'child' => array(
            array('name' => '首页设置', 'act' => 'editAd&request_url=api/ad/ad_home', 'op' => 'Ad'),
            array('name' => '安卓设置', 'act'=>'index', 'op'=>'MobileApp'),
            array('name' => '苹果设置', 'act'=>'ios_audit', 'op'=>'MobileApp'),
            array('name' => '小程序管理', 'act'=>'mini_app', 'op'=>'MobileApp'),
             
        )),
    )),
	
	'weixin'=>array('name'=>'微商城','child'=>array(
    	    array('name' => '微信接入','child' => array(
    	        array('name' => '公众号配置', 'act'=>'index', 'op'=>'Wechat'),
    	        array('name' => '微信菜单管理', 'act'=>'menu', 'op'=>'Wechat'),
    	        array('name' => '自动回复', 'act'=>'auto_reply', 'op'=>'Wechat'),
                array('name' => '粉丝列表', 'act'=>'fans_list', 'op'=>'Wechat'),
                array('name' => '模板消息', 'act'=>'template_msg', 'op'=>'Wechat'),
                array('name' => '素材管理', 'act'=>'materials', 'op'=>'Wechat'),
    	    )),
	)),
	
	'notice'=>array('name'=>'公告','child'=>array(
        array('name' => '版本通知','child' => array(
            array('name' => '版本迭代', 'act'=>'Message', 'op'=>'Notice'),   
        ))
       
    )),
	
);