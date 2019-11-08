import Vue from 'vue'
import Router from 'vue-router'

const _import =
  process.env.NODE_ENV === 'development'
    ? path => require('@/views/' + path).default // 全部加载（dev环境使用）
    : path => () => import('@/views/' + path) // 按需加载（build时使用）

Vue.use(Router)

export default new Router({
  routes: [
    { path: '', redirect: '/home/index', meta: { footer: { show: true } } },
    {
      path: '/home/',
      component: _import('home/HomeBase'),
      children: [
        {
          path: 'index',
          name: 'HomeIndex',
          component: _import('home/index/Index'),
          meta: { footer: { show: true }, title: '首页' }
        },
        {
          path: 'map',
          name: 'HomeMap',
          component: _import('home/map/Map'),
          meta: { footer: { show: true } },
          title: '地图'
        },
        {
          path: 'document',
          name: 'HomeDocument',
          component: _import('home/article/Document'),
          meta: { footer: { show: true } },
          title: '注册协议'
        },
        {
          path: 'article_class',
          name: 'HomeArticleclass',
          component: _import('home/article/Articleclass'),
          meta: { footer: { show: true }, title: '文章分类' }
        },
        {
          path: 'article_list',
          name: 'HomeArticlelist',
          component: _import('home/article/Articlelist'),
          meta: { footer: { show: true }, title: '文章列表' }
        },
        {
          path: 'article_detail',
          name: 'HomeArticledetail',
          component: _import('home/article/Articledetail'),
          meta: { footer: { show: true }, title: '文章详情' }
        },
        {
          path: 'memberregister',
          name: 'HomeMemberRegister',
          component: _import('home/memberregister/Register'),
          meta: { footer: { show: true }, title: '用户注册' }
        },
        {
          path: 'memberbind',
          name: 'HomeMemberBind',
          component: _import('home/memberbind/Bind'),
          meta: { footer: { show: true }, title: '用户绑定' }
        },
        {
          path: 'memberlogin',
          name: 'HomeMemberLogin',
          component: _import('home/memberlogin/Login'),
          meta: { footer: { show: true }, title: '用户登录' }
        },
        {
          path: 'sellerlogin',
          name: 'HomeSellerLogin',
          component: _import('home/sellerlogin/Login'),
          meta: { footer: { show: true }, title: '卖家登录' }
        },
        {
          path: 'memberforget',
          name: 'HomeMemberForget',
          component: _import('home/memberforget/Forget'),
          meta: { footer: { show: true }, title: '忘记密码' }
        },
        {
          path: 'goodsclass',
          name: 'HomeGoodsclass',
          component: _import('home/goodsclass/Goodsclass'),
          meta: { footer: { show: true }, title: '分类' }
        },
        {
          path: 'storeclass',
          name: 'HomeStoreclass',
          component: _import('home/storeclass/Storeclass'),
          meta: { footer: { show: true }, title: '店铺分类' }
        },
        {
          path: 'brand',
          name: 'HomeBrand',
          component: _import('home/brand/Brand'),
          meta: { footer: { show: true }, title: '品牌' }
        },
        {
          path: 'cart',
          name: 'HomeCart',
          component: _import('home/cart/Cart'),
          meta: { footer: { show: true }, title: '购物车' }
        },
        {
          path: 'search',
          name: 'HomeSearch',
          component: _import('home/search/Search'),
          meta: { footer: { show: true }, title: '搜索' }
        },
        {
          path: 'storelist',
          name: 'HomeStorelist',
          component: _import('home/storelist/Storelist'),
          meta: { footer: { show: true }, title: '店铺列表' }
        },
        {
          path: 'storedetail',
          name: 'HomeStoredetail',
          component: _import('home/storedetail/Storedetail'),
          meta: { footer: { show: false }, title: '店铺详情' }
        },
        {
          path: 'storeabout',
          name: 'HomeStoreabout',
          component: _import('home/storeabout/Storeabout'),
          meta: { footer: { show: false }, title: '店铺介绍' }
        },
        {
          path: 'storevoucher',
          name: 'HomeStoreVoucher',
          component: _import('home/storedetail/StoreVoucher'),
          meta: { footer: { show: false }, title: '店铺代金券' }
        },
        {
          path: 'goodslist',
          name: 'HomeGoodslist',
          component: _import('home/goodslist/Goodslist'),
          meta: { footer: { show: true }, title: '店铺列表' }
        },
        {
          path: 'store_goodslist',
          name: 'HomeStoreGoodslist',
          component: _import('home/storegoodslist/Goodslist'),
          meta: { footer: { show: false }, title: '店铺商品列表' }
        },
        {
          path: 'store_goodsclass',
          name: 'HomeStoreGoodsclass',
          component: _import('home/storegoodsclass/Goodsclass'),
          meta: { footer: { show: false }, title: '店铺搜索' }
        },
        {
          path: 'goodsdetail',
          name: 'HomeGoodsdetail',
          component: _import('home/goodsdetail/Goodsdetail'),
          meta: { footer: { show: false }, title: '商品详情' }
        },
        {
          path: 'goodsconsult',
          name: 'HomeGoodsConsult',
          component: _import('home/goodsdetail/GoodsConsult'),
          meta: { footer: { show: false }, title: '商品咨询' }
        },
        {
          path: 'pintuan_list',
          name: 'HomePintuanList',
          component: _import('home/pintuan/PintuanList'),
          meta: { footer: { show: true }, title: '拼团列表' }
        },
        {
          path: 'pointsgoods',
          name: 'HomePointsgoods',
          component: _import('home/pointsgoods/Index'),
          meta: { footer: { show: true }, title: '积分兑换商品' }
        },
        {
          path: 'pointsgoods_detail',
          name: 'HomePointsgoodsDetail',
          component: _import('home/pointsgoods/Detail'),
          meta: { footer: { show: false }, title: '积分兑换商品详情页' }
        },
        {
          path: 'bonus_detail',
          name: 'HomeBonusDetail',
          component: _import('home/bonus/Detail'),
          meta: { footer: { show: false }, title: '红包兑换商品详情页' }
        },
        {
          path: 'marketcard',
          name: 'HomeMarketcard',
          component: _import('home/marketmanage/Marketcard'),
          meta: { footer: { show: false }, title: '刮刮卡' }
        },
        {
          path: 'marketzodiac',
          name: 'HomeMarketzodiac',
          component: _import('home/marketmanage/Marketzodiac'),
          meta: { footer: { show: false }, title: '生肖翻翻看' }
        },
        {
          path: 'marketwheel',
          name: 'HomeMarketwheel',
          component: _import('home/marketmanage/Marketwheel'),
          meta: { footer: { show: false }, title: '大转盘' }
        },
        {
          path: 'marketegg',
          name: 'HomeMarketegg',
          component: _import('home/marketmanage/Marketegg'),
          meta: { footer: { show: false }, title: '砸金蛋' }
        },
        {
          path: 'bargain_list',
          name: 'HomeBargainlist',
          component: _import('home/bargain/Bargainlist'),
          meta: { footer: { show: true }, title: '砍价列表页' }
        },
        {
          path: 'bargain_share',
          name: 'HomeBargainshare',
          component: _import('home/bargain/Bargainshare'),
          meta: { footer: { show: true }, title: '砍价分享页面' }
        },
        {
          path: 'special',
          name: 'HomeSpecial',
          component: _import('home/special/Index'),
          meta: { footer: { show: true }, title: '可编辑页面' }
        }
      ]
    },
    {
      path: '/member/',
      component: _import('member/MemberBase'),
      children: [
        {
          path: 'index',
          name: 'MemberIndex',
          component: _import('member/index/Index'),
          meta: { footer: { show: true }, title: '用户中心' }
        },
        {
          path: 'predeposit_list',
          name: 'MemberPredepositList',
          component: _import('member/predeposit/PredepositList'),
          meta: { footer: { show: true }, title: '资金明细' }
        },
        {
          path: 'recharge_card_list',
          name: 'MemberRechargeCardList',
          component: _import('member/predeposit/RechargeCardList'),
          meta: { footer: { show: true }, title: '充值卡明细' }
        },
        {
          path: 'withdraw_list',
          name: 'MemberWithdrawList',
          component: _import('member/withdraw/WithdrawList'),
          meta: { footer: { show: true }, title: '提现明细' }
        },
        {
          path: 'chat_info',
          name: 'MemberChatInfo',
          component: _import('member/chat/ChatInfo'),
          meta: { footer: { show: false }, title: '聊天信息' }
        },
        {
          path: 'chat_list',
          name: 'MemberChatList',
          component: _import('member/chat/ChatList'),
          meta: { footer: { show: false }, title: '聊天列表' }
        },
        {
          path: 'friend_list',
          name: 'MemberFriendList',
          component: _import('member/friend/FriendList'),
          meta: { footer: { show: false }, title: '好友列表' }
        },
        {
          path: 'consult_list',
          name: 'MemberConsultList',
          component: _import('member/consult/ConsultList'),
          meta: { footer: { show: true }, title: '咨询列表' }
        },
        {
          path: 'recharge_list',
          name: 'MemberRechargeList',
          component: _import('member/recharge/RechargeList'),
          meta: { footer: { show: true }, title: '充值明细' }
        },
        {
          path: 'voucher_list',
          name: 'MemberVoucherList',
          component: _import('member/voucher/VoucherList'),
          meta: { footer: { show: true }, title: '代金券列表' }
        },
        {
          path: 'notice_list',
          name: 'MemberNoticeList',
          component: _import('member/notice/NoticeList'),
          meta: { footer: { show: true }, title: '消息列表' }
        },
        {
          path: 'setting',
          name: 'MemberSetting',
          component: _import('member/setting/AccountSet'),
          meta: { footer: { show: true }, title: '账号设置' }
        },
        {
          path: 'profile_set',
          name: 'MemberProfileSet',
          component: _import('member/profile/ProfileSet'),
          meta: { footer: { show: true }, title: '个人信息' }
        },
        {
          path: 'point_list',
          name: 'MemberPointList',
          component: _import('member/point/PointList'),
          meta: { footer: { show: true }, title: '积分明细' }
        },
        {
          path: 'point_signin',
          name: 'MemberPointSignin',
          component: _import('member/point/PointSignin'),
          meta: { footer: { show: true }, title: '签到送积分' }
        },
        {
          path: 'address_list',
          name: 'MemberAddressList',
          component: _import('member/address/AddressList'),
          meta: { footer: { show: true }, title: '地址列表' }
        },
        {
          path: 'address_form',
          name: 'MemberAddressForm',
          component: _import('member/address/AddressForm'),
          meta: { footer: { show: true }, title: '地址编辑' }
        },
        {
          path: 'bank_list',
          name: 'MemberBankList',
          component: _import('member/bank/BankList'),
          meta: { footer: { show: true }, title: '提现账户列表' }
        },
        {
          path: 'bank_form',
          name: 'MemberBankForm',
          component: _import('member/bank/BankForm'),
          meta: { footer: { show: true }, title: '提现账户编辑' }
        },
        {
          path: 'refund_list',
          name: 'MemberRefundList',
          component: _import('member/refund/RefundList'),
          meta: { footer: { show: true }, title: '退款列表' }
        },
        {
          path: 'refund_form',
          name: 'MemberRefundForm',
          component: _import('member/refund/RefundForm'),
          meta: { footer: { show: true }, title: '退款编辑' }
        },
        {
          path: 'refund_view',
          name: 'MemberRefundView',
          component: _import('member/refund/RefundView'),
          meta: { footer: { show: true }, title: '退款详情' }
        },
        {
          path: 'vrrefund_list',
          name: 'MemberVrRefundList',
          component: _import('member/vrrefund/VrRefundList'),
          meta: { footer: { show: true }, title: '退款列表' }
        },
        {
          path: 'vrrefund_form',
          name: 'MemberVrRefundForm',
          component: _import('member/vrrefund/VrRefundForm'),
          meta: { footer: { show: true }, title: '退款编辑' }
        },
        {
          path: 'vrrefund_view',
          name: 'MemberVrRefundView',
          component: _import('member/vrrefund/VrRefundView'),
          meta: { footer: { show: true }, title: '退款详情' }
        },
        {
          path: 'return_list',
          name: 'MemberReturnList',
          component: _import('member/return/ReturnList'),
          meta: { footer: { show: true }, title: '退货列表' }
        },
        {
          path: 'return_form',
          name: 'MemberReturnForm',
          component: _import('member/return/ReturnForm'),
          meta: { footer: { show: true }, title: '退货编辑' }
        },
        {
          path: 'return_view',
          name: 'MemberReturnView',
          component: _import('member/return/ReturnView'),
          meta: { footer: { show: true }, title: '退货详情' }
        },
        {
          path: 'return_send',
          name: 'MemberReturnSend',
          component: _import('member/return/ReturnSend'),
          meta: { footer: { show: true }, title: '退货' }
        },
        {
          path: 'invoice_list',
          name: 'MemberInvoiceList',
          component: _import('member/invoice/InvoiceList'),
          meta: { footer: { show: true }, title: '发票列表' }
        },
        {
          path: 'invoice_form',
          name: 'MemberInvoiceForm',
          component: _import('member/invoice/InvoiceForm'),
          meta: { footer: { show: true }, title: '发票编辑' }
        },
        {
          path: 'buystep1',
          name: 'MemberBuyStep1',
          component: _import('member/buy/step1'),
          meta: { footer: { show: false }, title: '下单界面' }
        },
        {
          path: 'vrbuystep1',
          name: 'MemberVrBuyStep1',
          component: _import('member/vrbuy/step1'),
          meta: { footer: { show: false }, title: '下单界面' }
        },
        {
          path: 'pointsbuystep1',
          name: 'MemberPointsBuyStep1',
          component: _import('member/pointsbuy/step1'),
          meta: { footer: { show: false }, title: '积分商品下单' }
        },
        {
          path: 'pointscart',
          name: 'MemberPointsCart',
          component: _import('member/pointscart/Cart'),
          meta: { footer: { show: true }, title: '积分商品购物车' }
        },
        {
          path: 'pointsorder_list',
          name: 'MemberPointsOrderList',
          component: _import('member/pointsorder/OrderList'),
          meta: { footer: { show: true }, title: '积分订单列表' }
        },
        {
          path: 'pointsorder_detail',
          name: 'MemberPointsOrderDetail',
          component: _import('member/pointsorder/OrderDetail'),
          meta: { footer: { show: true }, title: '积分订单详情' }
        },
        {
          path: 'inform_list',
          name: 'MemberInformList',
          component: _import('member/inform/InformList'),
          meta: { footer: { show: true }, title: '商家投诉' }
        },
        {
          path: 'inform_form',
          name: 'MemberInformForm',
          component: _import('member/inform/InformForm'),
          meta: { footer: { show: true }, title: '商家投诉' }
        },
        {
          path: 'complaint_list',
          name: 'MemberComplaintList',
          component: _import('member/complaint/ComplaintList'),
          meta: { footer: { show: true }, title: '商家投诉' }
        },
        {
          path: 'complaint_form',
          name: 'MemberComplaintForm',
          component: _import('member/complaint/ComplaintForm'),
          meta: { footer: { show: true }, title: '商家投诉' }
        },
        {
          path: 'buypay',
          name: 'MemberBuyPay',
          component: _import('member/buy/pay'),
          meta: { footer: { show: false }, title: '支付界面' }
        },
        {
          path: 'order_list',
          name: 'MemberOrderList',
          component: _import('member/order/OrderList'),
          meta: { footer: { show: true }, title: '我的订单' }
        },
        {
          path: 'favorite',
          name: 'MemberFavorite',
          component: _import('member/favorite/Favorite'),
          meta: { footer: { show: true }, title: '我的收藏' }
        },
        {
          path: 'order_detail',
          name: 'MemberOrderDetail',
          component: _import('member/order/OrderDetail'),
          meta: { footer: { show: true }, title: '订单详情' }
        },
        {
          path: 'order_deliver',
          name: 'MemberOrderDeliver',
          component: _import('member/order/OrderDeliver'),
          meta: { footer: { show: true }, title: '物流跟踪' }
        },
        {
          path: 'order_evaluate',
          name: 'MemberOrderEvaluate',
          component: _import('member/order/OrderEvaluate'),
          meta: { footer: { show: true }, title: '评价订单' }
        },
        {
          path: 'vrorder_detail',
          name: 'MemberVrOrderDetail',
          component: _import('member/vrorder/OrderDetail'),
          meta: { footer: { show: true }, title: '订单详情' }
        },
        {
          path: 'vrorder_evaluate',
          name: 'MemberVrOrderEvaluate',
          component: _import('member/vrorder/OrderEvaluate'),
          meta: { footer: { show: true }, title: '评价订单' }
        },
        {
          path: 'vrorder_list',
          name: 'MemberVrOrderList',
          component: _import('member/vrorder/OrderList'),
          meta: { footer: { show: true }, title: '我的订单' }
        },
        {
          path: 'evaluate_list',
          name: 'MemberEvaluateList',
          component: _import('member/evaluate/EvaluateList'),
          meta: { footer: { show: true }, title: '用户订单评价列表' }
        },
        {
          path: 'inviter_manage',
          name: 'MemberInviterManage',
          component: _import('member/inviter/InviterManage'),
          meta: { footer: { show: true }, title: '推广海报' }
        },
        {
          path: 'inviter_user',
          name: 'MemberInviterUser',
          component: _import('member/inviter/InviterUser'),
          meta: { footer: { show: true }, title: '推广会员' }
        },
        {
          path: 'inviter_order',
          name: 'MemberInviterOrder',
          component: _import('member/inviter/InviterOrder'),
          meta: { footer: { show: true }, title: '推广佣金' }
        },
        {
          path: 'marketmanagelog',
          name: 'MemberMarketmanagelog',
          component: _import('member/marketmanagelog/Marketmanagelog'),
          meta: { footer: { show: true }, title: '活动记录' }
        },
        {
          path: 'bonusreceive',
          name: 'MemberBonusreceive',
          component: _import('member/bonusreceive/Bonusreceive'),
          meta: { footer: { show: true }, title: '红包领取记录' }
        },
        {
          path: 'bargain_list',
          name: 'MemberBargainlist',
          component: _import('member/bargain/Bargainlist'),
          meta: { footer: { show: true }, title: '砍价列表页' }
        },
        {
          path: 'arrivalnotice',
          name: 'MemberArrivalnotice',
          component: _import('member/arrivalnotice/Arrivalnotice'),
          meta: { footer: { show: true }, title: '到货通知页面' }
        },
        {
          path: 'auth',
          name: 'MemberAuth',
          component: _import('member/auth/Auth'),
          meta: { footer: { show: true }, title: '会员认证页面' }
        }
      ]
    },
    {
      path: '/seller/',
      component: _import('seller/SellerBase'),
      children: [
        {
          path: 'index',
          name: 'SellerIndex',
          component: _import('seller/index/Index'),
          meta: { footer: { show: true }, title: '卖家中心' }
        },
        {
          path: 'consult_list',
          name: 'SellerConsultList',
          component: _import('seller/consult/ConsultList'),
          meta: { footer: { show: true }, title: '咨询列表' }
        },
        {
          path: 'chat_info',
          name: 'SellerChatInfo',
          component: _import('seller/chat/ChatInfo'),
          meta: { footer: { show: false }, title: '聊天信息' }
        },
        {
          path: 'chat_list',
          name: 'SellerChatList',
          component: _import('seller/chat/ChatList'),
          meta: { footer: { show: false }, title: '聊天列表' }
        },
        {
          path: 'address_list',
          name: 'SellerAddressList',
          component: _import('seller/address/AddressList'),
          meta: { footer: { show: true }, title: '店铺发货地址列表' }
        },
        {
          path: 'address_form',
          name: 'SellerAddressForm',
          component: _import('seller/address/AddressForm'),
          meta: { footer: { show: true }, title: '编辑店铺发货地址' }
        },
        {
          path: 'notice_list',
          name: 'SellerNoticeList',
          component: _import('seller/notice/NoticeList'),
          meta: { footer: { show: true }, title: '店铺消息列表' }
        },
        {
          path: 'voucher_list',
          name: 'SellerVoucherList',
          component: _import('seller/voucher/VoucherList'),
          meta: { footer: { show: true }, title: '代金券列表' }
        },
        {
          path: 'voucher_form',
          name: 'SellerVoucherForm',
          component: _import('seller/voucher/VoucherForm'),
          meta: { footer: { show: true }, title: '编辑代金券' }
        },
        {
          path: 'goodsonline',
          name: 'SellerGoodsonline',
          component: _import('seller/goods/Goodsonline'),
          meta: { footer: { show: true }, title: '卖家商品管理' }
        },
        {
          path: 'refund_list',
          name: 'SellerRefundList',
          component: _import('seller/refund/RefundList'),
          meta: { footer: { show: true }, title: '退款列表' }
        },
        {
          path: 'refund_form',
          name: 'SellerRefundForm',
          component: _import('seller/refund/RefundForm'),
          meta: { footer: { show: true }, title: '退款编辑' }
        },
        {
          path: 'refund_view',
          name: 'SellerRefundView',
          component: _import('seller/refund/RefundView'),
          meta: { footer: { show: true }, title: '退款详情' }
        },
        {
          path: 'refund_deliver',
          name: 'SellerRefundDeliver',
          component: _import('seller/refund/RefundDeliver'),
          meta: { footer: { show: true }, title: '物流跟踪' }
        },
        {
          path: 'evaluate_list',
          name: 'SellerEvaluateList',
          component: _import('seller/evaluate/EvaluateList'),
          meta: { footer: { show: true }, title: '评价列表' }
        },
        {
          path: 'orderlist',
          name: 'SellerOrderList',
          component: _import('seller/order/OrderList'),
          meta: { footer: { show: true }, title: '卖家订单列表' }
        },
        {
          path: 'order_detail',
          name: 'SellerOrderDetail',
          component: _import('seller/order/OrderDetail'),
          meta: { footer: { show: true }, title: '订单详情' }
        },
        {
          path: 'order_deliver',
          name: 'SellerOrderDeliver',
          component: _import('seller/order/OrderDeliver'),
          meta: { footer: { show: true }, title: '物流跟踪' }
        },
        {
          path: 'order_send',
          name: 'SellerOrderSend',
          component: _import('seller/order/OrderSend'),
          meta: { footer: { show: true }, title: '订单发货' }
        },
        {
          path: 'vrorder_detail',
          name: 'SellerVrOrderDetail',
          component: _import('seller/vrorder/OrderDetail'),
          meta: { footer: { show: true }, title: '订单详情' }
        },
        {
          path: 'vrorder_list',
          name: 'SellerVrOrderList',
          component: _import('seller/vrorder/OrderList'),
          meta: { footer: { show: true }, title: '我的订单' }
        },
        {
          path: 'billlist',
          name: 'SellerBillList',
          component: _import('seller/bill/BillList'),
          meta: { footer: { show: true }, title: '卖家结算单' }
        },
        {
          path: 'complaint_list',
          name: 'SellerComplaintList',
          component: _import('seller/complaint/ComplaintList'),
          meta: { footer: { show: true }, title: '商家投诉' }
        },
        {
          path: 'complaint_form',
          name: 'SellerComplaintForm',
          component: _import('seller/complaint/ComplaintForm'),
          meta: { footer: { show: true }, title: '商家投诉' }
        },
        {
          path: 'statistics_general',
          name: 'SellerStatisticsGeneral',
          component: _import('seller/statisticsgeneral/Index'),
          meta: { footer: { show: true }, title: '店铺概况' }
        },
        {
          path: 'setting_index',
          name: 'SellerSettingIndex',
          component: _import('seller/setting/Index'),
          meta: { footer: { show: true }, title: '店铺基本信息设置管理页' }
        },
        {
          path: 'setting_info',
          name: 'SellerSettingInfo',
          component: _import('seller/setting/Info'),
          meta: { footer: { show: true }, title: '店铺基本信息设置' }
        },
        {
          path: 'inviter',
          name: 'SellerInviter',
          component: _import('seller/inviter/Index'),
          meta: { footer: { show: true }, title: '店铺分销页面' }
        },
        {
          path: 'inviter_orderlist',
          name: 'SellerInviterOrderList',
          component: _import('seller/inviter/OrderList'),
          meta: { footer: { show: true }, title: '分销订单管理' }
        },
        {
          path: 'inviter_goodslist',
          name: 'SellerInviterGoodsList',
          component: _import('seller/inviter/GoodsList'),
          meta: { footer: { show: true }, title: '分销商品管理' }
        },
        {
          path: 'inviter_goodsform',
          name: 'SellerInviterGoodsForm',
          component: _import('seller/inviter/GoodsForm'),
          meta: { footer: { show: true }, title: '分销商品添加/编辑' }
        },
        {
          path: 'account',
          name: 'SellerAccount',
          component: _import('seller/account/Index'),
          meta: { footer: { show: true }, title: '子账户管理界面' }
        },
        {
          path: 'account_list',
          name: 'SellerAccountList',
          component: _import('seller/account/AccountList'),
          meta: { footer: { show: true }, title: '子账户列表' }
        },
        {
          path: 'account_form',
          name: 'SellerAccountForm',
          component: _import('seller/account/AccountForm'),
          meta: { footer: { show: true }, title: '添加子账户' }
        },
        {
          path: 'accountgroup_list',
          name: 'SellerAccountGroupList',
          component: _import('seller/accountgroup/AccountGroupList'),
          meta: { footer: { show: true }, title: '账户组列表' }
        },
        {
          path: 'accountgroup_form',
          name: 'SellerAccountGroupForm',
          component: _import('seller/accountgroup/AccountGroupForm'),
          meta: { footer: { show: true }, title: '账户组列表' }
        },
        {
          path: 'goodsclass_list',
          name: 'SellerGoodsClassList',
          component: _import('seller/goodsclass/GoodsClassList'),
          meta: { footer: { show: true }, title: '账户组列表' }
        },
        {
          path: 'goodsclass_form',
          name: 'SellerGoodsClassForm',
          component: _import('seller/goodsclass/GoodsClassForm'),
          meta: { footer: { show: true }, title: '账户组列表' }
        },
        {
          path: 'log_list',
          name: 'SellerLogList',
          component: _import('seller/log/LogList'),
          meta: { footer: { show: true }, title: '账户日志记录' }
        },
        {
          path: 'cost_list',
          name: 'SellerCostList',
          component: _import('seller/cost/CostList'),
          meta: { footer: { show: true }, title: '账户消费记录' }
        },
        {
          path: 'money',
          name: 'SellerMoney',
          component: _import('seller/money/Index'),
          meta: { footer: { show: true }, title: '店铺资金' }
        },
        {
          path: 'moneylog_list',
          name: 'SellerMoneyLogList',
          component: _import('seller/money/LogList'),
          meta: { footer: { show: true }, title: '店铺资金记录' }
        },
        {
          path: 'moneywithdraw_list',
          name: 'SellerMoneyWithdrawList',
          component: _import('seller/money/WithdrawList'),
          meta: { footer: { show: true }, title: '店铺资金提现记录' }
        },
        {
          path: 'deposit',
          name: 'SellerDeposit',
          component: _import('seller/deposit/Index'),
          meta: { footer: { show: true }, title: '店铺保证金' }
        },
        {
          path: 'deposit_list',
          name: 'SellerDepositList',
          component: _import('seller/deposit/DepositList'),
          meta: { footer: { show: true }, title: '店铺保证金记录' }
        },
        {
          path: 'depositwithdraw_list',
          name: 'SellerDepositWithdrawList',
          component: _import('seller/deposit/WithdrawList'),
          meta: { footer: { show: true }, title: '店铺保证金提现记录' }
        }
      ]
    }
  ]
})
