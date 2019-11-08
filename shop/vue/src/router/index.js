import Vue from 'vue'
import Router from 'vue-router'

const _import =
  process.env.NODE_ENV === 'development'
    ? path => require('@/views/' + path).default // 全部加载（dev环境使用）
    : path => () => import('@/views/' + path) // 按需加载（build时使用）

Vue.use(Router)

/**
 * 路由配置说明：
 * - 按模块区分，如home之类的
 * - footer 目前可配置
 *   · show 表示是否显示底部导航
 * - head 目前可配置：
 *   · title(页面标题)
 *   · show(是否显示公共头)
 *   · shoowBack(是否显示返回按钮)
 *   · back(返回上一页的方法，-1就是直接回到上一页，可以传对应页面的方法名称，这样公共头部会触发对应的方法)
 */

export default new Router({
  routes: [
    { path: '', redirect: '/home/index', meta: { footer: { show: true } } },
    {
      path: '/home/',
      component: _import('home/HomeBase'),
      children: [
        {
          path: 'memberlogin',
          name: 'HomeMemberLogin',
          component: _import('home/memberlogin/Login'),
          meta: { footer: { show: false }, head: { title: '用户登录', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'memberregister',
          name: 'HomeMemberRegister',
          component: _import('home/memberregister/Register'),
          meta: { footer: { show: false }, head: { title: '用户注册', showBack: true, show: true, back: 'goBack' } }
        },
        {
          path: 'document',
          name: 'HomeDocument',
          component: _import('home/article/Document'),
          meta: { footer: { show: false } },
          head: { title: '注册协议', showBack: true, show: true, back: -1 }
        },
        // 还没用上的页面
        {
          path: 'index',
          name: 'HomeIndex',
          component: _import('home/index/Index'),
          meta: { footer: { show: true }, head: { title: '首页', showBack: false, show: true, back: -1 } }
        },
        {
          path: 'map',
          name: 'HomeMap',
          component: _import('home/map/Map'),
          meta: { footer: { show: true } },
          head: { title: '地图', showBack: true, show: true, back: -1 }
        },

        {
          path: 'article_class',
          name: 'HomeArticleclass',
          component: _import('home/article/Articleclass'),
          meta: { footer: { show: true }, head: { title: '文章分类', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'article_list',
          name: 'HomeArticlelist',
          component: _import('home/article/Articlelist'),
          meta: { footer: { show: true }, head: { title: '文章列表', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'article_detail',
          name: 'HomeArticledetail',
          component: _import('home/article/Articledetail'),
          meta: { footer: { show: true }, head: { title: '文章详情', showBack: true, show: true, back: -1 } }
        },

        {
          path: 'memberbind',
          name: 'HomeMemberBind',
          component: _import('home/memberbind/Bind'),
          meta: { footer: { show: true }, head: { title: '用户绑定', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'sellerlogin',
          name: 'HomeSellerLogin',
          component: _import('home/sellerlogin/Login'),
          meta: { footer: { show: true }, head: { title: '卖家登录', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'memberforget',
          name: 'HomeMemberForget',
          component: _import('home/memberforget/Forget'),
          meta: { footer: { show: true }, head: { title: '忘记密码', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'goodsclass',
          name: 'HomeGoodsclass',
          component: _import('home/goodsclass/Goodsclass'),
          meta: { footer: { show: true }, head: { title: '分类', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'storeclass',
          name: 'HomeStoreclass',
          component: _import('home/storeclass/Storeclass'),
          meta: { footer: { show: true }, head: { title: '店铺分类', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'brand',
          name: 'HomeBrand',
          component: _import('home/brand/Brand'),
          meta: { footer: { show: true }, head: { title: '品牌', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'cart',
          name: 'HomeCart',
          component: _import('home/cart/Cart'),
          meta: { footer: { show: true }, head: { title: '购物车', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'search',
          name: 'HomeSearch',
          component: _import('home/search/Search'),
          meta: { footer: { show: true }, head: { title: '搜索', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'storelist',
          name: 'HomeStorelist',
          component: _import('home/storelist/Storelist'),
          meta: { footer: { show: true }, head: { title: '店铺列表', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'storedetail',
          name: 'HomeStoredetail',
          component: _import('home/storedetail/Storedetail'),
          meta: { footer: { show: false }, head: { title: '店铺详情', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'storeabout',
          name: 'HomeStoreabout',
          component: _import('home/storeabout/Storeabout'),
          meta: { footer: { show: false }, head: { title: '店铺介绍', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'storevoucher',
          name: 'HomeStoreVoucher',
          component: _import('home/storedetail/StoreVoucher'),
          meta: { footer: { show: false }, head: { title: '店铺代金券', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'goodslist',
          name: 'HomeGoodslist',
          component: _import('home/goodslist/Goodslist'),
          meta: { footer: { show: true }, head: { title: '店铺列表', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'store_goodslist',
          name: 'HomeStoreGoodslist',
          component: _import('home/storegoodslist/Goodslist'),
          meta: { footer: { show: false }, head: { title: '店铺商品列表', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'store_goodsclass',
          name: 'HomeStoreGoodsclass',
          component: _import('home/storegoodsclass/Goodsclass'),
          meta: { footer: { show: false }, head: { title: '店铺搜索', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'goodsdetail',
          name: 'HomeGoodsdetail',
          component: _import('home/goodsdetail/Goodsdetail'),
          meta: { footer: { show: false }, head: { title: '商品详情', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'goodsconsult',
          name: 'HomeGoodsConsult',
          component: _import('home/goodsdetail/GoodsConsult'),
          meta: { footer: { show: false }, head: { title: '商品咨询', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'pintuan_list',
          name: 'HomePintuanList',
          component: _import('home/pintuan/PintuanList'),
          meta: { footer: { show: true }, head: { title: '拼团列表', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'pointsgoods',
          name: 'HomePointsgoods',
          component: _import('home/pointsgoods/Index'),
          meta: { footer: { show: true }, head: { title: '积分兑换商品', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'pointsgoods_detail',
          name: 'HomePointsgoodsDetail',
          component: _import('home/pointsgoods/Detail'),
          meta: { footer: { show: false }, head: { title: '积分兑换商品详情页', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'bonus_detail',
          name: 'HomeBonusDetail',
          component: _import('home/bonus/Detail'),
          meta: { footer: { show: false }, head: { title: '红包兑换商品详情页', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'marketcard',
          name: 'HomeMarketcard',
          component: _import('home/marketmanage/Marketcard'),
          meta: { footer: { show: false }, head: { title: '刮刮卡', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'marketzodiac',
          name: 'HomeMarketzodiac',
          component: _import('home/marketmanage/Marketzodiac'),
          meta: { footer: { show: false }, head: { title: '生肖翻翻看', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'marketwheel',
          name: 'HomeMarketwheel',
          component: _import('home/marketmanage/Marketwheel'),
          meta: { footer: { show: false }, head: { title: '大转盘', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'marketegg',
          name: 'HomeMarketegg',
          component: _import('home/marketmanage/Marketegg'),
          meta: { footer: { show: false }, head: { title: '砸金蛋', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'bargain_list',
          name: 'HomeBargainlist',
          component: _import('home/bargain/Bargainlist'),
          meta: { footer: { show: true }, head: { title: '砍价列表页', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'bargain_share',
          name: 'HomeBargainshare',
          component: _import('home/bargain/Bargainshare'),
          meta: { footer: { show: true }, head: { title: '砍价分享页面', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'special',
          name: 'HomeSpecial',
          component: _import('home/special/Index'),
          meta: { footer: { show: true }, head: { title: '可编辑页面', showBack: true, show: true, back: -1 } }
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
          meta: { footer: { show: true }, head: { title: '用户中心', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'predeposit_list',
          name: 'MemberPredepositList',
          component: _import('member/predeposit/PredepositList'),
          meta: { footer: { show: true }, head: { title: '资金明细', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'recharge_card_list',
          name: 'MemberRechargeCardList',
          component: _import('member/predeposit/RechargeCardList'),
          meta: { footer: { show: true }, head: { title: '充值卡明细', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'withdraw_list',
          name: 'MemberWithdrawList',
          component: _import('member/withdraw/WithdrawList'),
          meta: { footer: { show: true }, head: { title: '提现明细', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'chat_info',
          name: 'MemberChatInfo',
          component: _import('member/chat/ChatInfo'),
          meta: { footer: { show: false }, head: { title: '聊天信息', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'chat_list',
          name: 'MemberChatList',
          component: _import('member/chat/ChatList'),
          meta: { footer: { show: false }, head: { title: '聊天列表', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'friend_list',
          name: 'MemberFriendList',
          component: _import('member/friend/FriendList'),
          meta: { footer: { show: false }, head: { title: '好友列表', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'consult_list',
          name: 'MemberConsultList',
          component: _import('member/consult/ConsultList'),
          meta: { footer: { show: true }, head: { title: '咨询列表', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'recharge_list',
          name: 'MemberRechargeList',
          component: _import('member/recharge/RechargeList'),
          meta: { footer: { show: true }, head: { title: '充值明细', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'voucher_list',
          name: 'MemberVoucherList',
          component: _import('member/voucher/VoucherList'),
          meta: { footer: { show: true }, head: { title: '代金券列表', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'notice_list',
          name: 'MemberNoticeList',
          component: _import('member/notice/NoticeList'),
          meta: { footer: { show: true }, head: { title: '消息列表', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'setting',
          name: 'MemberSetting',
          component: _import('member/setting/AccountSet'),
          meta: { footer: { show: true }, head: { title: '账号设置', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'profile_set',
          name: 'MemberProfileSet',
          component: _import('member/profile/ProfileSet'),
          meta: { footer: { show: true }, head: { title: '个人信息', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'point_list',
          name: 'MemberPointList',
          component: _import('member/point/PointList'),
          meta: { footer: { show: true }, head: { title: '积分明细', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'point_signin',
          name: 'MemberPointSignin',
          component: _import('member/point/PointSignin'),
          meta: { footer: { show: true }, head: { title: '签到送积分', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'address_list',
          name: 'MemberAddressList',
          component: _import('member/address/AddressList'),
          meta: { footer: { show: true }, head: { title: '地址列表', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'address_form',
          name: 'MemberAddressForm',
          component: _import('member/address/AddressForm'),
          meta: { footer: { show: true }, head: { title: '地址编辑', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'bank_list',
          name: 'MemberBankList',
          component: _import('member/bank/BankList'),
          meta: { footer: { show: true }, head: { title: '提现账户列表', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'bank_form',
          name: 'MemberBankForm',
          component: _import('member/bank/BankForm'),
          meta: { footer: { show: true }, head: { title: '提现账户编辑', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'refund_list',
          name: 'MemberRefundList',
          component: _import('member/refund/RefundList'),
          meta: { footer: { show: true }, head: { title: '退款列表', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'refund_form',
          name: 'MemberRefundForm',
          component: _import('member/refund/RefundForm'),
          meta: { footer: { show: true }, head: { title: '退款编辑', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'refund_view',
          name: 'MemberRefundView',
          component: _import('member/refund/RefundView'),
          meta: { footer: { show: true }, head: { title: '退款详情', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'vrrefund_list',
          name: 'MemberVrRefundList',
          component: _import('member/vrrefund/VrRefundList'),
          meta: { footer: { show: true }, head: { title: '退款列表', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'vrrefund_form',
          name: 'MemberVrRefundForm',
          component: _import('member/vrrefund/VrRefundForm'),
          meta: { footer: { show: true }, head: { title: '退款编辑', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'vrrefund_view',
          name: 'MemberVrRefundView',
          component: _import('member/vrrefund/VrRefundView'),
          meta: { footer: { show: true }, head: { title: '退款详情', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'return_list',
          name: 'MemberReturnList',
          component: _import('member/return/ReturnList'),
          meta: { footer: { show: true }, head: { title: '退货列表', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'return_form',
          name: 'MemberReturnForm',
          component: _import('member/return/ReturnForm'),
          meta: { footer: { show: true }, head: { title: '退货编辑', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'return_view',
          name: 'MemberReturnView',
          component: _import('member/return/ReturnView'),
          meta: { footer: { show: true }, head: { title: '退货详情', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'return_send',
          name: 'MemberReturnSend',
          component: _import('member/return/ReturnSend'),
          meta: { footer: { show: true }, head: { title: '退货', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'invoice_list',
          name: 'MemberInvoiceList',
          component: _import('member/invoice/InvoiceList'),
          meta: { footer: { show: true }, head: { title: '发票列表', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'invoice_form',
          name: 'MemberInvoiceForm',
          component: _import('member/invoice/InvoiceForm'),
          meta: { footer: { show: true }, head: { title: '发票编辑', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'buystep1',
          name: 'MemberBuyStep1',
          component: _import('member/buy/step1'),
          meta: { footer: { show: false }, head: { title: '下单界面', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'vrbuystep1',
          name: 'MemberVrBuyStep1',
          component: _import('member/vrbuy/step1'),
          meta: { footer: { show: false }, head: { title: '下单界面', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'pointsbuystep1',
          name: 'MemberPointsBuyStep1',
          component: _import('member/pointsbuy/step1'),
          meta: { footer: { show: false }, head: { title: '积分商品下单', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'pointscart',
          name: 'MemberPointsCart',
          component: _import('member/pointscart/Cart'),
          meta: { footer: { show: true }, head: { title: '积分商品购物车', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'pointsorder_list',
          name: 'MemberPointsOrderList',
          component: _import('member/pointsorder/OrderList'),
          meta: { footer: { show: true }, head: { title: '积分订单列表', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'pointsorder_detail',
          name: 'MemberPointsOrderDetail',
          component: _import('member/pointsorder/OrderDetail'),
          meta: { footer: { show: true }, head: { title: '积分订单详情', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'inform_list',
          name: 'MemberInformList',
          component: _import('member/inform/InformList'),
          meta: { footer: { show: true }, head: { title: '商家投诉', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'inform_form',
          name: 'MemberInformForm',
          component: _import('member/inform/InformForm'),
          meta: { footer: { show: true }, head: { title: '商家投诉', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'complaint_list',
          name: 'MemberComplaintList',
          component: _import('member/complaint/ComplaintList'),
          meta: { footer: { show: true }, head: { title: '商家投诉', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'complaint_form',
          name: 'MemberComplaintForm',
          component: _import('member/complaint/ComplaintForm'),
          meta: { footer: { show: true }, head: { title: '商家投诉', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'buypay',
          name: 'MemberBuyPay',
          component: _import('member/buy/pay'),
          meta: { footer: { show: false }, head: { title: '支付界面', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'order_list',
          name: 'MemberOrderList',
          component: _import('member/order/OrderList'),
          meta: { footer: { show: true }, head: { title: '我的订单', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'favorite',
          name: 'MemberFavorite',
          component: _import('member/favorite/Favorite'),
          meta: { footer: { show: true }, head: { title: '我的收藏', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'order_detail',
          name: 'MemberOrderDetail',
          component: _import('member/order/OrderDetail'),
          meta: { footer: { show: true }, head: { title: '订单详情', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'order_deliver',
          name: 'MemberOrderDeliver',
          component: _import('member/order/OrderDeliver'),
          meta: { footer: { show: true }, head: { title: '物流跟踪', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'order_evaluate',
          name: 'MemberOrderEvaluate',
          component: _import('member/order/OrderEvaluate'),
          meta: { footer: { show: true }, head: { title: '评价订单', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'vrorder_detail',
          name: 'MemberVrOrderDetail',
          component: _import('member/vrorder/OrderDetail'),
          meta: { footer: { show: true }, head: { title: '订单详情', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'vrorder_evaluate',
          name: 'MemberVrOrderEvaluate',
          component: _import('member/vrorder/OrderEvaluate'),
          meta: { footer: { show: true }, head: { title: '评价订单', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'vrorder_list',
          name: 'MemberVrOrderList',
          component: _import('member/vrorder/OrderList'),
          meta: { footer: { show: true }, head: { title: '我的订单', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'evaluate_list',
          name: 'MemberEvaluateList',
          component: _import('member/evaluate/EvaluateList'),
          meta: { footer: { show: true }, head: { title: '用户订单评价列表', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'inviter_manage',
          name: 'MemberInviterManage',
          component: _import('member/inviter/InviterManage'),
          meta: { footer: { show: true }, head: { title: '推广海报', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'inviter_user',
          name: 'MemberInviterUser',
          component: _import('member/inviter/InviterUser'),
          meta: { footer: { show: true }, head: { title: '推广会员', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'inviter_order',
          name: 'MemberInviterOrder',
          component: _import('member/inviter/InviterOrder'),
          meta: { footer: { show: true }, head: { title: '推广佣金', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'marketmanagelog',
          name: 'MemberMarketmanagelog',
          component: _import('member/marketmanagelog/Marketmanagelog'),
          meta: { footer: { show: true }, head: { title: '活动记录', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'bonusreceive',
          name: 'MemberBonusreceive',
          component: _import('member/bonusreceive/Bonusreceive'),
          meta: { footer: { show: true }, head: { title: '红包领取记录', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'bargain_list',
          name: 'MemberBargainlist',
          component: _import('member/bargain/Bargainlist'),
          meta: { footer: { show: true }, head: { title: '砍价列表页', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'arrivalnotice',
          name: 'MemberArrivalnotice',
          component: _import('member/arrivalnotice/Arrivalnotice'),
          meta: { footer: { show: true }, head: { title: '到货通知页面', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'auth',
          name: 'MemberAuth',
          component: _import('member/auth/Auth'),
          meta: { footer: { show: true }, head: { title: '会员认证页面', showBack: true, show: true, back: -1 } }
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
          meta: { footer: { show: true }, head: { title: '卖家中心', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'consult_list',
          name: 'SellerConsultList',
          component: _import('seller/consult/ConsultList'),
          meta: { footer: { show: true }, head: { title: '咨询列表', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'chat_info',
          name: 'SellerChatInfo',
          component: _import('seller/chat/ChatInfo'),
          meta: { footer: { show: false }, head: { title: '聊天信息', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'chat_list',
          name: 'SellerChatList',
          component: _import('seller/chat/ChatList'),
          meta: { footer: { show: false }, head: { title: '聊天列表', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'address_list',
          name: 'SellerAddressList',
          component: _import('seller/address/AddressList'),
          meta: { footer: { show: true }, head: { title: '店铺发货地址列表', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'address_form',
          name: 'SellerAddressForm',
          component: _import('seller/address/AddressForm'),
          meta: { footer: { show: true }, head: { title: '编辑店铺发货地址', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'notice_list',
          name: 'SellerNoticeList',
          component: _import('seller/notice/NoticeList'),
          meta: { footer: { show: true }, head: { title: '店铺消息列表', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'voucher_list',
          name: 'SellerVoucherList',
          component: _import('seller/voucher/VoucherList'),
          meta: { footer: { show: true }, head: { title: '代金券列表', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'voucher_form',
          name: 'SellerVoucherForm',
          component: _import('seller/voucher/VoucherForm'),
          meta: { footer: { show: true }, head: { title: '编辑代金券', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'goodsonline',
          name: 'SellerGoodsonline',
          component: _import('seller/goods/Goodsonline'),
          meta: { footer: { show: true }, head: { title: '卖家商品管理', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'refund_list',
          name: 'SellerRefundList',
          component: _import('seller/refund/RefundList'),
          meta: { footer: { show: true }, head: { title: '退款列表', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'refund_form',
          name: 'SellerRefundForm',
          component: _import('seller/refund/RefundForm'),
          meta: { footer: { show: true }, head: { title: '退款编辑', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'refund_view',
          name: 'SellerRefundView',
          component: _import('seller/refund/RefundView'),
          meta: { footer: { show: true }, head: { title: '退款详情', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'refund_deliver',
          name: 'SellerRefundDeliver',
          component: _import('seller/refund/RefundDeliver'),
          meta: { footer: { show: true }, head: { title: '物流跟踪', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'evaluate_list',
          name: 'SellerEvaluateList',
          component: _import('seller/evaluate/EvaluateList'),
          meta: { footer: { show: true }, head: { title: '评价列表', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'orderlist',
          name: 'SellerOrderList',
          component: _import('seller/order/OrderList'),
          meta: { footer: { show: true }, head: { title: '卖家订单列表', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'order_detail',
          name: 'SellerOrderDetail',
          component: _import('seller/order/OrderDetail'),
          meta: { footer: { show: true }, head: { title: '订单详情', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'order_deliver',
          name: 'SellerOrderDeliver',
          component: _import('seller/order/OrderDeliver'),
          meta: { footer: { show: true }, head: { title: '物流跟踪', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'order_send',
          name: 'SellerOrderSend',
          component: _import('seller/order/OrderSend'),
          meta: { footer: { show: true }, head: { title: '订单发货', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'vrorder_detail',
          name: 'SellerVrOrderDetail',
          component: _import('seller/vrorder/OrderDetail'),
          meta: { footer: { show: true }, head: { title: '订单详情', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'vrorder_list',
          name: 'SellerVrOrderList',
          component: _import('seller/vrorder/OrderList'),
          meta: { footer: { show: true }, head: { title: '我的订单', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'billlist',
          name: 'SellerBillList',
          component: _import('seller/bill/BillList'),
          meta: { footer: { show: true }, head: { title: '卖家结算单', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'complaint_list',
          name: 'SellerComplaintList',
          component: _import('seller/complaint/ComplaintList'),
          meta: { footer: { show: true }, head: { title: '商家投诉', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'complaint_form',
          name: 'SellerComplaintForm',
          component: _import('seller/complaint/ComplaintForm'),
          meta: { footer: { show: true }, head: { title: '商家投诉', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'statistics_general',
          name: 'SellerStatisticsGeneral',
          component: _import('seller/statisticsgeneral/Index'),
          meta: { footer: { show: true }, head: { title: '店铺概况', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'setting_index',
          name: 'SellerSettingIndex',
          component: _import('seller/setting/Index'),
          meta: {
            footer: { show: true },
            head: { title: '店铺基本信息设置管理页', showBack: true, show: true, back: -1 }
          }
        },
        {
          path: 'setting_info',
          name: 'SellerSettingInfo',
          component: _import('seller/setting/Info'),
          meta: { footer: { show: true }, head: { title: '店铺基本信息设置', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'inviter',
          name: 'SellerInviter',
          component: _import('seller/inviter/Index'),
          meta: { footer: { show: true }, head: { title: '店铺分销页面', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'inviter_orderlist',
          name: 'SellerInviterOrderList',
          component: _import('seller/inviter/OrderList'),
          meta: { footer: { show: true }, head: { title: '分销订单管理', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'inviter_goodslist',
          name: 'SellerInviterGoodsList',
          component: _import('seller/inviter/GoodsList'),
          meta: { footer: { show: true }, head: { title: '分销商品管理', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'inviter_goodsform',
          name: 'SellerInviterGoodsForm',
          component: _import('seller/inviter/GoodsForm'),
          meta: { footer: { show: true }, head: { title: '分销商品添加/编辑', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'account',
          name: 'SellerAccount',
          component: _import('seller/account/Index'),
          meta: { footer: { show: true }, head: { title: '子账户管理界面', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'account_list',
          name: 'SellerAccountList',
          component: _import('seller/account/AccountList'),
          meta: { footer: { show: true }, head: { title: '子账户列表', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'account_form',
          name: 'SellerAccountForm',
          component: _import('seller/account/AccountForm'),
          meta: { footer: { show: true }, head: { title: '添加子账户', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'accountgroup_list',
          name: 'SellerAccountGroupList',
          component: _import('seller/accountgroup/AccountGroupList'),
          meta: { footer: { show: true }, head: { title: '账户组列表', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'accountgroup_form',
          name: 'SellerAccountGroupForm',
          component: _import('seller/accountgroup/AccountGroupForm'),
          meta: { footer: { show: true }, head: { title: '账户组列表', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'goodsclass_list',
          name: 'SellerGoodsClassList',
          component: _import('seller/goodsclass/GoodsClassList'),
          meta: { footer: { show: true }, head: { title: '账户组列表', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'goodsclass_form',
          name: 'SellerGoodsClassForm',
          component: _import('seller/goodsclass/GoodsClassForm'),
          meta: { footer: { show: true }, head: { title: '账户组列表', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'log_list',
          name: 'SellerLogList',
          component: _import('seller/log/LogList'),
          meta: { footer: { show: true }, head: { title: '账户日志记录', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'cost_list',
          name: 'SellerCostList',
          component: _import('seller/cost/CostList'),
          meta: { footer: { show: true }, head: { title: '账户消费记录', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'money',
          name: 'SellerMoney',
          component: _import('seller/money/Index'),
          meta: { footer: { show: true }, head: { title: '店铺资金', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'moneylog_list',
          name: 'SellerMoneyLogList',
          component: _import('seller/money/LogList'),
          meta: { footer: { show: true }, head: { title: '店铺资金记录', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'moneywithdraw_list',
          name: 'SellerMoneyWithdrawList',
          component: _import('seller/money/WithdrawList'),
          meta: { footer: { show: true }, head: { title: '店铺资金提现记录', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'deposit',
          name: 'SellerDeposit',
          component: _import('seller/deposit/Index'),
          meta: { footer: { show: true }, head: { title: '店铺保证金', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'deposit_list',
          name: 'SellerDepositList',
          component: _import('seller/deposit/DepositList'),
          meta: { footer: { show: true }, head: { title: '店铺保证金记录', showBack: true, show: true, back: -1 } }
        },
        {
          path: 'depositwithdraw_list',
          name: 'SellerDepositWithdrawList',
          component: _import('seller/deposit/WithdrawList'),
          meta: { footer: { show: true }, head: { title: '店铺保证金提现记录', showBack: true, show: true, back: -1 } }
        }
      ]
    }
  ]
})
