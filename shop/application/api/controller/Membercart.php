<?php

namespace app\api\controller;
use think\Lang;
/**
 * ============================================================================
 * DSMall多用户商城
 * ============================================================================
 * 版权所有 2014-2028 长沙德尚网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.csdeshang.com
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用 .
 * 不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * 购物车控制器
 */
class Membercart extends MobileMember {

    public function _initialize() {
        parent::_initialize();
        Lang::load(APP_PATH . 'home/lang/'.config('default_lang').'/cart.lang.php');
    }

    /**
     * @api {POST} api/Membercart/cart_list 购物车列表
     * @apiVersion 1.0.0
     * @apiGroup Membercart
     *
     * @apiHeader {String} X-DS-KEY 用户授权token
     *
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     * @apiSuccess {Int} result.cart_count  物品种类数量
     * @apiSuccess {Object} result.cart_list  购物车列表，键为店铺ID
     * @apiSuccess {Int} result.cart_list.store_id  店铺ID
     * @apiSuccess {String} result.cart_list.store_name  店铺名称
     * @apiSuccess {String} result.cart_list.store_name  店铺优惠券
     * @apiSuccess {String} result.cart_list.goods  商品列表
     * @apiSuccess {Int} result.cart_list.goods.bl_id  优惠套餐ID
     * @apiSuccess {Int} result.cart_list.goods.buyer_id  买家ID
     * @apiSuccess {Int} result.cart_list.goods.cart_id  购物车ID
     * @apiSuccess {Int} result.cart_list.goods.gc_id  分类ID
     * @apiSuccess {Object} result.cart_list.goods.gift_list  赠品列表
     * @apiSuccess {Int} result.cart_list.goods.goods_commonid  商品公共ID
     * @apiSuccess {Float} result.cart_list.goods.goods_freight  运费
     * @apiSuccess {Int} result.cart_list.goods.goods_id  商品ID
     * @apiSuccess {String} result.cart_list.goods.goods_image  商品图片名称
     * @apiSuccess {String} result.cart_list.goods.goods_image_url  商品图片完整路径
     * @apiSuccess {String} result.cart_list.goods.goods_name  商品名称
     * @apiSuccess {Int} result.cart_list.goods.goods_num  购买数量
     * @apiSuccess {Float} result.cart_list.goods.goods_price  商品价格
     * @apiSuccess {Int} result.cart_list.goods.goods_storage  商品库存
     * @apiSuccess {Int} result.cart_list.goods.goods_storage_alarm  预警库存
     * @apiSuccess {Int} result.cart_list.goods.goods_vat  是否支持发票 0否1是
     * @apiSuccess {Object} result.cart_list.goods.groupbuy_info  抢购信息
     * @apiSuccess {Int} result.cart_list.goods.is_goodsfcode  是否F码商品 0否1是
     * @apiSuccess {Int} result.cart_list.goods.is_have_gift  是否含赠品 0否1是
     * @apiSuccess {Object} result.cart_list.goods.mgdiscount_info  会员折扣信息
     * @apiSuccess {Boolean} result.cart_list.goods.state  商品状态 true上架false下架
     * @apiSuccess {Boolean} result.cart_list.goods.storage_state  商品库存状态 true充足false不足
     * @apiSuccess {Int} result.cart_list.goods.store_id  店铺ID
     * @apiSuccess {String} result.cart_list.goods.store_name  店铺名称
     * @apiSuccess {Int} result.cart_list.goods.transport_id  售卖区域id
     * @apiSuccess {Object} result.cart_list.goods.xianshi_info  限时折扣信息
     * @apiSuccess {Int} result.cart_list.store_id  店铺ID
     * @apiSuccess {String} result.cart_list.store_name  店铺名称
     * @apiSuccess {Object[]} result.cart_val  去除result.cart_list键的数组
     * @apiSuccess {Float} result.sum  总价
     */
    public function cart_list() {
        $cart_model = model('cart');

        $condition = array('buyer_id' => $this->member_info['member_id']);
        $cart_list = $cart_model->getCartList('db', $condition);

        // 购物车列表 [得到最新商品属性及促销信息]
        $cart_list = model('buy_1','logic')->getGoodsCartList($cart_list);
        $goods_model = model('goods');

        $sum = 0;
        $cart_a = array();
        $k=0;
        $voucher_model = model('voucher');
        foreach ($cart_list as $key => $val) {
            $cart_a[$val['store_id']]['store_id'] = $val['store_id'];
            $cart_a[$val['store_id']]['store_name'] = $val['store_name'];
            //获取店铺代金券
            $cart_a[$val['store_id']]['voucher_list'] = $voucher_model->getVouchertemplateList(array('vouchertemplate_store_id'=>$val['store_id'],'vouchertemplate_state'=>1,'vouchertemplate_enddate'=>array('gt', time())));
//            $goods_data = $goods_model->getGoodsOnlineInfoForShare($val['goods_id']);
            $cart_a[$val['store_id']]['goods'][$key] = $val;

            $cart_a[$val['store_id']]['goods'][$key]['cart_id'] = $val['cart_id'];
            $cart_a[$val['store_id']]['goods'][$key]['goods_num'] = $val['goods_num'];
            $cart_a[$val['store_id']]['goods'][$key]['goods_image_url'] = goods_cthumb($val['goods_image'], $val['store_id']);
//            if (isset($goods_data['goods_promotion_type'])) {
//                $cart_a[$val['store_id']]['goods'][$key]['goods_price'] = $goods_data['goods_promotion_price'];
//            }
            $cart_a[$val['store_id']]['goods'][$key]['gift_list'] = isset($val['gift_list'])?$val['gift_list']:'';
            $cart_list[$key]['goods_sum'] = ds_price_format($val['goods_price'] * $val['goods_num']);
            $sum += $cart_list[$key]['goods_sum'];
            $k++;
        }
        
        
        foreach ($cart_a as $key => $value) {
            $value['goods'] = array_values($value['goods']);
            $cart_l[] = $value;
        }
        if(isset($cart_l)){
            $cart_b=array_values($cart_l);
        }else{
            $cart_b=array();
        }
        ds_json_encode(10000,'',array('cart_list' => $cart_a, 'sum' => ds_price_format($sum), 'cart_count' => count($cart_list),'cart_val'=>$cart_b));
    }

    /**
     * @api {POST} api/membercart/cart_add 购物车添加
     * @apiVersion 1.0.0
     * @apiGroup MemberCart
     * 
     * @apiHeader {String} X-DS-KEY 用户授权token
     * 
     * @apiParam {Int} goods_id 商品ID  
     * @apiParam {Int} quantity 购买数量
     * @apiParam {Int} bl_id 组合购买ID  
     * 
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据(购物车主键ID)
     */
    public function cart_add() {
        if(isset($this->member_info) && !$this->member_info['is_buylimit']){
            ds_json_encode(10001,lang('cart_buy_noallow'));
        }
        if(config('member_auth') && $this->member_info['member_auth_state']!=3){
            ds_json_encode(10001,lang('cart_buy_noauth'));
        }
        $goods_id = intval(input('post.goods_id'));
        $quantity = intval(input('post.quantity'));
        $bl_id = intval(input('post.bl_id'));


        $goods_model = model('goods');
        $cart_model = model('cart');
        $logic_buy_1 = model('buy_1','logic');

        if(!$bl_id){
            if ($goods_id <= 0 || $quantity <= 0) {
                ds_json_encode(10001,lang('param_error'));
            }
            $goods_info = $goods_model->getGoodsOnlineInfoAndPromotionById($goods_id);

            //验证是否可以购买
            if (empty($goods_info)) {
                ds_json_encode(10001,lang('cart_add_goods_not_exists'));
            }

            //抢购
            $logic_buy_1->getGroupbuyInfo($goods_info, $quantity);

            //限时折扣
            $logic_buy_1->getXianshiInfo($goods_info, $quantity);

            if (isset($this->member_info) && $goods_info['store_id'] == $this->member_info['store_id']) {
                ds_json_encode(10001,lang('cannot_buy_self_goods'));
            }
            if (intval($goods_info['goods_storage']) < 1 || intval($goods_info['goods_storage']) < $quantity) {
                ds_json_encode(10001,lang('cart_add_stock_shortage'));
            }
        }else{
            //优惠套装加入购物车(单套)
            $pbundling_model = model('pbundling');
            $bl_info = $pbundling_model->getBundlingInfo(array('bl_id' => $bl_id));
            if (empty($bl_info) || $bl_info['bl_state'] == '0') {
                ds_json_encode(10001,lang('recommendations_buy_separately'));
            }

            //检查每个商品是否符合条件,并重新计算套装总价
            $bl_goods_list = $pbundling_model->getBundlingGoodsList(array('bl_id' => $bl_id));
            $goods_id_array = array();
            $bl_amount = 0;
            foreach ($bl_goods_list as $goods) {
                $goods_id_array[] = $goods['goods_id'];
                $bl_amount += $goods['blgoods_price'];
            }
            $goods_model = model('goods');
            $goods_list = $goods_model->getGoodsOnlineListAndPromotionByIdArray($goods_id_array);
            foreach ($goods_list as $goods_info) {
                if (isset($this->member_info) && $goods_info['store_id'] == $this->member_info['store_id']) {
                    ds_json_encode(10001,lang('cannot_buy_self_goods'));
                }
                if (intval($goods_info['goods_storage']) < 1) {
                    ds_json_encode(10001,lang('cart_add_stock_shortage'));
                }
            }

            //优惠套装作为一条记录插入购物车，图片取套装内的第一个商品图
            $goods_info = array();
            $goods_info['store_id'] = $bl_info['store_id'];
            $goods_info['goods_id'] = $goods_list[0]['goods_id'];
            $goods_info['goods_storage'] = $goods_list[0]['goods_storage'];
            $goods_info['goods_name'] = $bl_info['bl_name'];
            $goods_info['goods_price'] = $bl_amount;
            $goods_info['goods_num'] = 1;
            $goods_info['goods_image'] = $goods_list[0]['goods_image'];
            $goods_info['store_name'] = $bl_info['store_name'];
            $goods_info['bl_id'] = $bl_id;
            $quantity = 1;
        }

        $param = array();
        $param['buyer_id'] = isset($this->member_info)?$this->member_info['member_id']:0;
        $param['store_id'] = $goods_info['store_id'];
        $param['goods_id'] = $goods_info['goods_id'];
        $param['goods_name'] = $goods_info['goods_name'];
        $param['goods_price'] = $goods_info['goods_price'];
        $param['goods_image'] = $goods_info['goods_image'];
        $param['store_name'] = $goods_info['store_name'];
        $param['goods_storage'] = $goods_info['goods_storage'];
        $param['bl_id'] = $bl_id;
        
        $result = $cart_model->addCart($param, isset($this->member_info)?'db':'cookie', $quantity);
        if ($result) {
            ds_json_encode(10000, lang('ds_common_op_succ'), $result);
        } else {
            ds_json_encode(10001, $cart_model->error_message);
        }
    }

    /**
     * @api {POST} api/membercart/cart_del 购物车删除
     * @apiVersion 1.0.0
     * @apiGroup MemberCart
     * 
     * @apiHeader {String} X-DS-KEY 用户授权token
     * 
     * @apiParam {String} cart_id 购物车主键ID  例3,5,8
     * 
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     */
    public function cart_del() {
        $cart_id = input('param.cart_id');
        $cart_id_array = ds_delete_param($cart_id);
        if ($cart_id_array == FALSE) {
            ds_json_encode('10001', lang('param_error'));
        }
        $cart_model = model('cart');

        if ($cart_id > 0) {
            $condition = array();
            $condition['buyer_id'] = $this->member_info['member_id'];
            $condition['cart_id'] = array('in', $cart_id_array);
            $cart_model->delCart('db', $condition);
        }
        ds_json_encode(10000, '', 1);
    }

    /**
     * @api {POST} api/membercart/cart_edit_quantity 更新购物车购买数量
     * @apiVersion 1.0.0
     * @apiGroup MemberCart
     * 
     * @apiHeader {String} X-DS-KEY 用户授权token
     * 
     * @apiParam {Int} cart_id 购物车主键ID  
     * @apiParam {Int} quantity 修改数量
     * 
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     * @apiSuccess {Object} result.quantity  数量
     * @apiSuccess {Object} result.goods_price  单价
     * @apiSuccess {Object} result.total_price  总价
     * 
     */
    public function cart_edit_quantity() {
        $cart_id = intval(abs(input('post.cart_id')));
        $quantity = intval(abs(input('post.quantity')));
        if (empty($cart_id) || empty($quantity)) {
            ds_json_encode(10001,lang('param_error'));
        }

        $cart_model = model('cart');
        $cart_info = $cart_model->getCartInfo(array('cart_id' => $cart_id, 'buyer_id' => $this->member_info['member_id']));
        //检查是否为本人购物车
        if ($cart_info['buyer_id'] != $this->member_info['member_id']) {
            ds_json_encode(10001,lang('param_error'));
        }
        
        //检查库存是否充足
        if (!$this->_check_goods_storage($cart_info, $quantity, $this->member_info['member_id'])) {
            ds_json_encode(10001,lang('goods_quantity_limit'));
        }

        $data = array();
        $data['goods_num'] = $quantity;
        $data['goods_price'] = $cart_info['goods_price'];

        $where['cart_id']= $cart_id;
        $where['buyer_id']=$cart_info['buyer_id'];

        $update = $cart_model->editCart($data, $where);
        if ($update) {
            $return = array();
            $return['quantity'] = $quantity;
            $return['goods_price'] = ds_price_format($cart_info['goods_price']);
            $return['total_price'] = ds_price_format($cart_info['goods_price'] * $quantity);
            ds_json_encode(10000,'',$return);
        } else {
            ds_json_encode(10001,lang('ds_common_op_fail'));
        }
    }

    /**
     * 检查库存是否充足
     */
    private function _check_goods_storage(& $cart_info, $quantity, $member_id) {
        $goods_model = model('goods');
        $pbundling_model = model('pbundling');
        $logic_buy_1 = model('buy_1','logic');

        if ($cart_info['bl_id'] == '0') {
            //普通商品
            $goods_info = $goods_model->getGoodsOnlineInfoAndPromotionById($cart_info['goods_id']);

            //抢购
            $logic_buy_1->getGroupbuyInfo($goods_info, $quantity);
            if (isset($goods_info['ifgroupbuy'])) {
                if ($goods_info['upper_limit'] && $quantity > $goods_info['upper_limit']) {
                    return false;
                }
            }
            //限时折扣
            $logic_buy_1->getXianshiInfo($goods_info, $quantity);
            if (intval($goods_info['goods_storage']) < $quantity) {
                return false;
            }
            //会员等级折扣
            $logic_buy_1->getMgdiscountInfo($goods_info);
            
            $goods_info['cart_id'] = $cart_info['cart_id'];
            $goods_info['buyer_id'] = $cart_info['buyer_id'];
            $cart_info = $goods_info;
        } else {
            //优惠套装商品
            $bl_goods_list = $pbundling_model->getBundlingGoodsList(array('bl_id' => $cart_info['bl_id']));
            $goods_id_array = array();
            foreach ($bl_goods_list as $goods) {
                $goods_id_array[] = $goods['goods_id'];
            }
            $bl_goods_list = $goods_model->getGoodsOnlineListAndPromotionByIdArray($goods_id_array);

            //如果有商品库存不足，更新购买数量到目前最大库存
            foreach ($bl_goods_list as $goods_info) {
                if (intval($goods_info['goods_storage']) < $quantity) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * @api {POST} api/Membercart/cart_count 检查购物车数量
     * @apiVersion 1.0.0
     * @apiGroup Membercart
     *
     * @apiHeader {String} X-DS-KEY 用户授权token
     *
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     * @apiSuccess {Int} result.cart_count  购物车数量
     */
    public function cart_count() {
        $cart_model = model('cart');
        $count = $cart_model->getCartCountByMemberId($this->member_info['member_id']);
        $data['cart_count'] = $count;
        ds_json_encode(10000,'',$data);
    }

}

?>
