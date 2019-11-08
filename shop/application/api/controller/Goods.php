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
 * 商品控制器
 */
class Goods extends MobileMall {

    private $PI = 3.14159265358979324;
    private $x_pi = 0;

    public function _initialize() {
        parent::_initialize();
        Lang::load(APP_PATH . 'home/lang/'.config('default_lang').'/goods.lang.php');
        $this->x_pi = 3.14159265358979324 * 3000.0 / 180.0;
    }

    /**
     * @api {POST} api/Goods/goods_list 商品列表
     * @apiVersion 1.0.0
     * @apiGroup Goods
     *
     * @apiParam {Int} cate_id 分类ID
     * @apiParam {String} keyword 关键词
     * @apiParam {String} b_id 品牌id
     * @apiParam {Float} price_from 价格从
     * @apiParam {Float} price_to 价格到
     * @apiParam {Int} sort_key 排序键 goods_salenum销量 goods_click浏览量 goods_price价格
     * @apiParam {Int} sort_order 排序值 1升序 2降序
     * @apiParam {Int} gift 是否有赠品 1有
     * @apiParam {Int} own_shop 自营 1是
     * @apiParam {Int} area_id 地区id
     * @apiParam {Int} xianshi 是否限时折扣 1是
     * @apiParam {Int} page 页码
     * @apiParam {Int} per_page 每页显示数量
     *
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     * @apiSuccess {Object[]} result.goods_list  商品列表
     * @apiSuccess {Int} result.goods_list.evaluation_count  评论数
     * @apiSuccess {Float} result.goods_list.evaluation_good_star  评分
     * @apiSuccess {String} result.goods_list.goods_advword  广告词
     * @apiSuccess {Int} result.goods_list.goods_id  商品ID
     * @apiSuccess {String} result.goods_list.goods_image  商品图片名称
     * @apiSuccess {String} result.goods_list.goods_image_url  商品图片完整路径
     * @apiSuccess {Float} result.goods_list.goods_marketprice  商品市场价
     * @apiSuccess {String} result.goods_list.goods_name  商品名称
     * @apiSuccess {Float} result.goods_list.goods_price  商品价格
     * @apiSuccess {Float} result.goods_list.goods_promotion_price  商品促销价
     * @apiSuccess {String} result.goods_list.goods_promotion_type  促销类型
     * @apiSuccess {Int} result.goods_list.goods_salenum  商品销售量
     * @apiSuccess {Boolean} result.goods_list.group_flag  是否抢购 true是false否
     * @apiSuccess {Int} result.goods_list.is_goodsfcode  是否F码 1是0否
     * @apiSuccess {Int} result.goods_list.is_have_gift  是否含赠品 1是0否
     * @apiSuccess {Int} result.goods_list.is_platform_store  是否平台自营 1是0否
     * @apiSuccess {Int} result.goods_list.is_presell  是否预售 1是0否
     * @apiSuccess {Int} result.goods_list.is_virtual  是否虚拟商品 1是0否
     * @apiSuccess {Int} result.goods_list.store_id  店铺ID
     * @apiSuccess {String} result.goods_list.store_name  店铺名称
     * @apiSuccess {Boolean} result.goods_list.xianshi_flag  是否限时 true是false否
     * @apiSuccess {Int} result.page_total  总页数
     * @apiSuccess {Boolean} result.hasmore  是否有更多 true是false否
     */
    public function goods_list() {
        $goods_model = model('goods');
        $search_model = model('search');

        //查询条件
        $condition = array();
        $cate_id = intval(input('param.cate_id'));
        $keyword = $default_classid = input('param.keyword');
        $b_id = intval(input('param.b_id'));
        
        //获得经过属性过滤的商品信息 
        $this->_model_search = model('search');
        list($goods_param, $brand_array, $initial_array, $attr_array, $checked_brand, $checked_attr) = $this->_model_search->getAttribute(input('param.'), $default_classid);
        if (isset($goods_param['class']['depth'])) {
            $condition['goodscommon.gc_id_' . $goods_param['class']['depth']] = $goods_param['class']['gc_id'];
        }
        if (isset($goods_param['goodsid_array'])) {
            $condition['goods.goods_id'] = array('in', $goods_param['goodsid_array']);
        }
        if ($cate_id > 0) {
            $condition['goodscommon.gc_id'] = $cate_id;
        }
        if (!empty($keyword)) {
            $condition['goodscommon.goods_name|goodscommon.goods_advword'] = array('like', '%' . $keyword . '%');
            if (cookie('hisSearch') == '') {
                $his_sh_list = array();
            } else {
                $his_sh_list = explode('~', cookie('hisSearch'));
            }
            if (strlen($keyword) <= 20 && !in_array($keyword, $his_sh_list)) {
                if (array_unshift($his_sh_list, $keyword) > 8) {
                    array_pop($his_sh_list);
                }
            }
            cookie('hisSearch', implode('~', $his_sh_list), 2592000);
        }
        if ($b_id > 0) {
            $condition['goodscommon.brand_id'] = $b_id;
        }
        
        $price_from = input('param.price_from');
        $price_to = input('param.price_to');
        $price_from = preg_match('/^[\d.]{1,20}$/', $price_from) ? $price_from : null;
        $price_to = preg_match('/^[\d.]{1,20}$/', $price_to) ? $price_to : null;

        //所需字段
        $fieldstr = "goods.goods_id,goodscommon.goods_commonid,goodscommon.store_id,goodscommon.goods_name,goodscommon.goods_advword,goodscommon.goods_price,goods.goods_promotion_price,goods.goods_promotion_type,goodscommon.goods_marketprice,goodscommon.goods_image,goods.goods_salenum,goods.evaluation_good_star,goods.evaluation_count";

        $fieldstr .= ',goodscommon.is_virtual,goodscommon.is_presell,goodscommon.is_goodsfcode,goods.is_have_gift,goodscommon.store_name,goodscommon.is_platform_store';

        //排序方式
        $order = $this->_goods_list_order(input('param.sort_key'), input('param.sort_order'));

        //全文搜索搜索参数
        $indexer_searcharr = input('param.');
        //搜索消费者保障服务
        $search_ci_arr = array();
        $ci = trim(input('param.ci'), '_');
        if ($ci && $ci != 0 && is_string($ci)) {
            //处理参数
            $search_ci = $ci;
            $search_ci_arr = explode('_', $search_ci);
            $indexer_searcharr['search_ci_arr'] = $search_ci_arr;
        }
        if (input('param.own_shop') == 1) {
            $indexer_searcharr['type'] = 1;
        }
        $indexer_searcharr['price_from'] = $price_from;
        $indexer_searcharr['price_to'] = $price_to;

        //优先从全文索引库里查找
//        list($goods_list, $indexer_count) = $search_model->indexerSearch($indexer_searcharr, $this->pagesize);
//        if (!is_null($goods_list)) {
//            $goods_list = array_values($goods_list);
//        } else {

            if ($price_from && $price_to) {
                $condition['goods.goods_promotion_price'] = array('between', "{$price_from},{$price_to}");
            } elseif ($price_from) {
                $condition['goods.goods_promotion_price'] = array('egt', $price_from);
            } elseif ($price_to) {
                $condition['goods.goods_promotion_price'] = array('elt', $price_to);
            }
            if (input('param.gift') == 1) {
                $condition['goods.is_have_gift'] = 1;
            }
            if (input('param.own_shop') == 1) {
                $condition['goodscommon.store_id'] = 1;
            }
            if (intval(input('param.area_id')) > 0) {
                $condition['goodscommon.areaid_1'] = intval(input('param.area_id'));
            }

            //抢购和限时折扣搜索
            $_tmp = array();
            if (input('param.groupbuy') == 1) {
                $_tmp[] = 1;
            }
            if (input('param.xianshi') == 1) {
                $_tmp[] = 2;
            }
            if ($_tmp) {
                $condition['goods.goods_promotion_type'] = array('in', $_tmp);
            }
            unset($_tmp);

            //虚拟商品
            if (input('param.virtual') == 1) {
                $condition['goodscommon.is_virtual'] = 1;
            }
            $goods_list = $goods_model->getGoodsUnionList($condition, $fieldstr, $order,'goodscommon.goods_commonid', $this->pagesize);
//        }
        //处理商品列表(抢购、限时折扣、商品图片)
        $goods_list = $this->_goods_list_extend($goods_list);
        $result = array_merge(array('goods_list' => $goods_list), mobile_page(is_object($goods_model->page_info)?$goods_model->page_info:''));
        ds_json_encode(10000, '',$result);
    }
    
    /**
     * @api {POST} api/Goods/get_attribute 获取分类下的属性
     * @apiVersion 1.0.0
     * @apiGroup Goods
     *
     * @apiParam {Int} cate_id 分类ID
     * @apiParam {String} a_id 已选择的属性
     * @apiParam {Int} b_id 已选择的品牌
     * 
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     * @apiSuccess {Object} result.goods_param 商品数据集
     * @apiSuccess {Object} result.brand_array 品牌列表
     * @apiSuccess {Object} result.attr_array 属性列表
     * @apiSuccess {Object} result.checked_brand 已选择的品牌
     * @apiSuccess {Object} result.checked_attr 已选择的属性
     */
    public function get_attribute()
    {
        $this->_model_search = model('search');
        $default_classid = intval(input('param.cate_id'));
        //获得经过属性过滤的商品信息 
        list($goods_param, $brand_array, $initial_array, $attr_array, $checked_brand, $checked_attr) = $this->_model_search->getAttribute(input('param.'), $default_classid);
        $result = array(
            'goods_param'=>$goods_param,
            'brand_array'=>$brand_array,
            'initial_array'=>$initial_array,
            'attr_array'=>$attr_array,
            'checked_brand'=>$checked_brand,
            'checked_attr'=>$checked_attr,
        );
        ds_json_encode(10000, '',$result);
    }

    /**
     * @api {POST} api/Goods/get_bundling 优惠套装
     * @apiVersion 1.0.0
     * @apiGroup Goods
     *
     * @apiParam {Int} goods_id 商品ID
     *
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     * @apiSuccess {Object} result.bundling_array 优惠套餐分类列表，键为分类ID
     * @apiSuccess {Float} result.bundling_array.freight 邮费
     * @apiSuccess {Int} result.bundling_array.id 优惠套餐分类ID
     * @apiSuccess {String} result.bundling_array.name 优惠套餐分类名称
     * @apiSuccess {Float} result.bundling_array.price 优惠套餐价
     * @apiSuccess {Float} result.bundling_array.storecost_price 原价
     * @apiSuccess {Object} result.b_goods_array  优惠套餐商品列表，键为分类ID
     * @apiSuccess {Int} result.b_goods_array.id 优惠套餐分类ID
     * @apiSuccess {String} result.b_goods_array.image 商品图片
     * @apiSuccess {String} result.b_goods_array.name 商品名称
     * @apiSuccess {Float} result.b_goods_array.price 优惠后价格
     * @apiSuccess {Float} result.b_goods_array.shop_price 原价
     */
    public function get_bundling() {
        $goods_id = intval(input('param.goods_id'));
        if ($goods_id <= 0) {
            ds_json_encode(10001,lang('param_error'));
        }


        // 优惠套装
        $array = model('pbundling')->getBundlingCacheByGoodsId($goods_id);
        if (!empty($array)) {
            $bundling_array=unserialize($array['bundling_array']);
            $b_goods_array=unserialize($array['b_goods_array']);
            ds_json_encode(10000, '',array('bundling_array'=>!empty($bundling_array)?$bundling_array:false,'b_goods_array'=>!empty($b_goods_array)?$b_goods_array:false));
        }else{
            ds_json_encode(10001,lang('bundling_not_exist'));
        }

    }
    /**
     * 商品列表排序方式
     */
    private function _goods_list_order($sort_key, $sort_order) {
        $result = 'goodscommon.mall_goods_commend desc,goodscommon.mall_goods_sort asc';
        if (!empty($sort_key)) {

            $sequence = 'desc';
            if ($sort_order == 'asc') {
                $sequence = 'asc';
            }

            switch ($sort_key) {
                //销量
                case 'goods_salenum' :
                    $result = 'goods.goods_salenum' . ' ' . $sequence;
                    break;
                //浏览量
                case 'goods_click' :
                    $result = 'goods.goods_click' . ' ' . $sequence;
                    break;
                //价格
                case 'goods_price' :
                    $result = 'goodscommon.goods_price' . ' ' . $sequence;
                    break;
                //新品
                case 'goods_addtime' :
                    $result = 'goodscommon.goods_addtime' . ' ' . $sequence;
                    break;
            }
        }
        return $result;
    }

    private function _goods_list_extend($goods_list) {
        //获取商品列表编号数组
        $commonid_array = array();
        $goodsid_array = array();
        foreach ($goods_list as $key => $value) {
            $commonid_array[] = $value['goods_commonid'];
            $goodsid_array[] = $value['goods_id'];
        }

        //促销
        $store_model = model('store');
        $groupbuy_list = model('groupbuy')->getGroupbuyListByGoodsCommonIDString(implode(',', $commonid_array));
        $xianshi_list = model('pxianshigoods')->getXianshigoodsListByGoodsString(implode(',', $goodsid_array));
        foreach ($goods_list as $key => $value) {
            //抢购
            if (isset($groupbuy_list[$value['goods_commonid']])) {
                $goods_list[$key]['goods_price'] = $groupbuy_list[$value['goods_commonid']]['groupbuy_price'];
                $goods_list[$key]['group_flag'] = true;
            } else {
                $goods_list[$key]['group_flag'] = false;
            }

            //限时折扣
            if (isset($xianshi_list[$value['goods_id']]) && !$goods_list[$key]['group_flag']) {
                $goods_list[$key]['goods_price'] = $xianshi_list[$value['goods_id']]['xianshigoods_price'];
                $goods_list[$key]['xianshi_flag'] = true;
            } else {
                $goods_list[$key]['xianshi_flag'] = false;
            }

            //商品图片url
            $goods_list[$key]['goods_image_url'] = goods_cthumb($value['goods_image'], 480, $value['store_id']);
            $store_info = $store_model->getStoreInfoByID($value['store_id']);
            $goods_list[$key]['store_name'] = $store_info['store_name'];

            unset($goods_list[$key]['goods_commonid']);
            unset($goods_list[$key]['nc_distinct']);
        }

        return $goods_list;
    }


    /**
     * @api {POST} api/Goods/goods_detail 商品详细页
     * @apiVersion 1.0.0
     * @apiGroup Goods
     *
     * @apiParam {Int} goods_id 商品ID
     *
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     * @apiSuccess {Object} result.consult_type 咨询类型列表，键为咨询类型ID
     * @apiSuccess {Int} result.consult_type.consulttype_id 咨询类型ID
     * @apiSuccess {Object} result.consult_type.consulttype_introduce 咨询介绍
     * @apiSuccess {Object} result.consult_type.consulttype_name 咨询类型标题
     * @apiSuccess {Int} result.consult_type.consulttype_sort 咨询类型排序
     * @apiSuccess {Object[]} result.gift_array 赠品列表
     * @apiSuccess {Int} result.gift_array.gift_id 赠品ID
     * @apiSuccess {Int} result.gift_array.gift_goodsid 赠品商品ID
     * @apiSuccess {Object} result.gift_array.gift_goodsname 主商品名称
     * @apiSuccess {Object} result.gift_array.gift_goodsimage 主商品图片名称
     * @apiSuccess {Object} result.gift_array.gift_goodsimage_url  主商品图片完整路径
     * @apiSuccess {Object} result.gift_array.gift_amount 赠品数量
     * @apiSuccess {Int} result.gift_array.goods_id 主商品ID
     * @apiSuccess {Int} result.gift_array.goods_commonid 主商品公共ID
     * @apiSuccess {Object[]} result.goods_commend_list 推荐商品列表
     * @apiSuccess {Int} result.goods_commend_list.goods_id 商品ID
     * @apiSuccess {Object} result.goods_commend_list.goods_image_url 商品图片
     * @apiSuccess {Object} result.goods_commend_list.goods_name 商品名称
     * @apiSuccess {Float} result.goods_commend_list.goods_price 商品价格
     * @apiSuccess {Float} result.goods_commend_list.goods_promotion_price 商品促销价
     * @apiSuccess {Object[]} result.goods_eval_list 商品评论列表
     * @apiSuccess {Int} result.goods_eval_list.geval_addtime 评论时间
     * @apiSuccess {String} result.goods_eval_list.geval_content 评论内容
     * @apiSuccess {String} result.goods_eval_list.geval_explain 店家解释
     * @apiSuccess {Int} result.goods_eval_list.geval_frommemberid 评论用户ID
     * @apiSuccess {String} result.goods_eval_list.geval_frommembername 评论用户名
     * @apiSuccess {Int} result.goods_eval_list.geval_goodsid 评论商品ID
     * @apiSuccess {String} result.goods_eval_list.geval_goodsimage 评论商品图片
     * @apiSuccess {String} result.goods_eval_list.geval_goodsname 评论商品名称
     * @apiSuccess {Float} result.goods_eval_list.geval_goodsprice 评论商品价格
     * @apiSuccess {Int} result.goods_eval_list.geval_id 评论ID
     * @apiSuccess {String} result.goods_eval_list.geval_image 评论图片
     * @apiSuccess {Int} result.goods_eval_list.geval_isanonymous 是否匿名 0:不是 1:匿名评价
     * @apiSuccess {Int} result.goods_eval_list.geval_ordergoodsid 订单商品ID
     * @apiSuccess {Int} result.goods_eval_list.geval_orderid 订单ID
     * @apiSuccess {String} result.goods_eval_list.geval_orderno 订单编号
     * @apiSuccess {String} result.goods_eval_list.geval_remark 管理员对评价的处理备注
     * @apiSuccess {Int} result.goods_eval_list.geval_scores 评分
     * @apiSuccess {Int} result.goods_eval_list.geval_state 评论状态 0为正常 1为禁止显示
     * @apiSuccess {Int} result.goods_eval_list.geval_storeid 商品店铺ID
     * @apiSuccess {String} result.goods_eval_list.geval_storename 商品店铺名称
     * @apiSuccess {Object} result.goods_evaluate_info 商品评论综合信息
     * @apiSuccess {Int} result.goods_evaluate_info.all 商品评论总数
     * @apiSuccess {Int} result.goods_evaluate_info.bad 差评数
     * @apiSuccess {Int} result.goods_evaluate_info.bad_percent 差评率
     * @apiSuccess {Int} result.goods_evaluate_info.good 好评数
     * @apiSuccess {Int} result.goods_evaluate_info.good_percent 好评率
     * @apiSuccess {Int} result.goods_evaluate_info.good_star 好评评分
     * @apiSuccess {Int} result.goods_evaluate_info.normal 中评数
     * @apiSuccess {Int} result.goods_evaluate_info.normal_percent 中评率
     * @apiSuccess {Int} result.goods_evaluate_info.star_average 平均评分
     * @apiSuccess {Object} result.goods_hair_info 商品运费信息
     * @apiSuccess {String} result.goods_hair_info.area_name 地区名称
     * @apiSuccess {String} result.goods_hair_info.content 运费描述
     * @apiSuccess {Boolean} result.goods_hair_info.if_store 是否有货 true是false否
     * @apiSuccess {String} result.goods_hair_info.if_store_cn 库存描述
     * @apiSuccess {String} result.goods_image 商品图片，用逗号分隔
     * @apiSuccess {Object} result.goods_info 商品信息
     * @apiSuccess {Int} result.goods_info.appoint_satedate 预约商品出售时间
     * @apiSuccess {Int} result.goods_info.areaid_1 一级地区id
     * @apiSuccess {Int} result.goods_info.areaid_2 二级地区id
     * @apiSuccess {Boolean} result.goods_info.cart 是否可以加入购物车
     * @apiSuccess {Int} result.goods_info.color_id 颜色规格值ID
     * @apiSuccess {Int} result.goods_info.evaluation_count 评论数
     * @apiSuccess {Int} result.goods_info.evaluation_good_star 好评评分
     * @apiSuccess {Int} result.goods_info.gc_id_1 一级分类ID
     * @apiSuccess {Int} result.goods_info.gc_id_2 二级分类ID
     * @apiSuccess {Int} result.goods_info.gc_id_3 三级分类ID
     * @apiSuccess {String} result.goods_info.goods_advword 广告词
     * @apiSuccess {String} result.goods_info.goods_attr 商品属性
     * @apiSuccess {Int} result.goods_info.goods_click 商品点击次数
     * @apiSuccess {Int} result.goods_info.goods_collect 收藏数
     * @apiSuccess {Int} result.goods_info.goods_commonid 商品公共ID
     * @apiSuccess {Float} result.goods_info.goods_costprice 商品成本价
     * @apiSuccess {Int} result.goods_info.goods_discount 商品折扣
     * @apiSuccess {Float} result.goods_info.goods_freight 运费
     * @apiSuccess {Int} result.goods_info.goods_id 商品ID
     * @apiSuccess {Float} result.goods_info.goods_marketprice 商品市场价
     * @apiSuccess {String} result.goods_info.goods_mgdiscount 会员等级折扣
     * @apiSuccess {String} result.goods_info.goods_name 商品名称
     * @apiSuccess {Float} result.goods_info.goods_price 商品价格
     * @apiSuccess {Float} result.goods_info.goods_promotion_price 商品促销价
     * @apiSuccess {String} result.goods_info.goods_promotion_type 商品促销类型
     * @apiSuccess {Int} result.goods_info.goods_salenum 销售量
     * @apiSuccess {String} result.goods_info.goods_serial 货号
     * @apiSuccess {String} result.goods_info.goods_spec 商品规格序列化
     * @apiSuccess {String} result.goods_info.goods_specname 规格名称序列化
     * @apiSuccess {String} result.goods_info.goods_stcids 店铺分类id 首尾用,隔开
     * @apiSuccess {Int} result.goods_info.goods_storage 库存
     * @apiSuccess {Int} result.goods_info.goods_storage_alarm 预警库存
     * @apiSuccess {Int} result.goods_info.goods_url 商品PC端连接
     * @apiSuccess {Int} result.goods_info.goods_vat 是否开具增值税发票 1:是 0:否
     * @apiSuccess {Object} result.goods_info.groupbuy_info 抢购信息
     * @apiSuccess {Float} result.goods_info.inviter_amount 商品已结算的分销佣金
     * @apiSuccess {Int} result.goods_info.inviter_open 是否开启分销 1是0否
     * @apiSuccess {Float} result.goods_info.inviter_ratio_1 一级分销比例
     * @apiSuccess {Float} result.goods_info.inviter_ratio_2 二级分销比例
     * @apiSuccess {Float} result.goods_info.inviter_ratio_3 三级分销比例
     * @apiSuccess {Float} result.goods_info.inviter_total_amount 总分销金额
     * @apiSuccess {Int} result.goods_info.inviter_total_quantity 总分销量
     * @apiSuccess {Int} result.goods_info.is_appoint 是否是预约商品 1:是 0:否
     * @apiSuccess {Int} result.goods_info.is_goodsfcode 是否是F码商品 1:是 0:否
     * @apiSuccess {Int} result.goods_info.is_have_gift 是否含赠品 1:是 0:否
     * @apiSuccess {Int} result.goods_info.is_platform_store 是否自营商品 1:是 0:否
     * @apiSuccess {Int} result.goods_info.is_presell 是否是预售商品 1:是 0:否
     * @apiSuccess {Int} result.goods_info.is_virtual 是否是虚拟商品 1:是 0:否
     * @apiSuccess {Object} result.goods_info.mgdiscount_info 会员等级折扣信息
     * @apiSuccess {Object} result.goods_info.pintuan_info 拼团信息
     * @apiSuccess {Int} result.goods_info.pintuangroup_share_id 拼团ID
     * @apiSuccess {Int} result.goods_info.plateid_bottom 底部关联板式
     * @apiSuccess {Int} result.goods_info.plateid_top 顶部关联板式
     * @apiSuccess {Int} result.goods_info.presell_deliverdate 预售商品发货时间
     * @apiSuccess {Int} result.goods_info.region_id 地区ID
     * @apiSuccess {String} result.goods_info.spec_name 规格名称
     * @apiSuccess {Int} result.goods_info.spec_value 规格值
     * @apiSuccess {Int} result.goods_info.transport_id 商品售卖区域
     * @apiSuccess {String} result.goods_info.transport_title 商品售卖区域名称
     * @apiSuccess {Int} result.goods_info.virtual_indate 虚拟商品有效期
     * @apiSuccess {Int} result.goods_info.virtual_invalid_refund 是否允许过期退款 1:是 0:否
     * @apiSuccess {Int} result.goods_info.virtual_limit 虚拟商品购买上限
     * @apiSuccess {Object} result.goods_info.xianshi_info 限时信息
     * @apiSuccess {Object[]} result.mb_body 商品详情列表
     * @apiSuccess {String} result.mb_body.type 详情类型 text文字image图片
     * @apiSuccess {String} result.mb_body.value 详情值
     * @apiSuccess {Float} result.inviter_amount 分销佣金
     * @apiSuccess {Boolean} result.is_favorate 是否已收藏 true是false否
     * @apiSuccess {Object} result.spec_image 规格图片列表，键为规格ID，值为规格图片完整路径
     * @apiSuccess {Object} result.spec_list 规格商品ID列表，键为规格ID，值为商品ID
     * @apiSuccess {Object} result.store_info 店铺信息
     * @apiSuccess {Int} result.store_info.goods_count 商品数
     * @apiSuccess {Int} result.store_info.is_platform_store 是否平台自营 1是0否
     * @apiSuccess {Int} result.store_info.member_id 用户ID
     * @apiSuccess {String} result.store_info.member_name 用户名称
     * @apiSuccess {String} result.store_info.store_address 店铺地址
     * @apiSuccess {Object} result.store_info.store_credit 店铺信用信息
     * @apiSuccess {Object} result.store_info.store_credit.store_deliverycredit 发货信用信息
     * @apiSuccess {Int} result.store_info.store_credit.store_deliverycredit.credit 发货信用值
     * @apiSuccess {String} result.store_info.store_credit.store_deliverycredit.text 发货信用描述
     * @apiSuccess {Object} result.store_info.store_credit.store_desccredit 描述相符信用信息
     * @apiSuccess {Int} result.store_info.store_credit.store_desccredit.credit 描述相符信用值
     * @apiSuccess {String} result.store_info.store_credit.store_desccredit.text 描述相符信用描述
     * @apiSuccess {Object} result.store_info.store_credit.store_servicecredit 服务态度信用信息
     * @apiSuccess {Int} result.store_info.store_credit.store_servicecredit.credit 服务态度信用值
     * @apiSuccess {String} result.store_info.store_credit.store_servicecredit.text 服务态度信用描述
     * @apiSuccess {Int} result.store_info.store_id 店铺ID
     * @apiSuccess {String} result.store_info.store_logo 店铺logo
     * @apiSuccess {String} result.store_info.store_name 店铺名称
     * @apiSuccess {Object[]} result.voucher 店铺优惠券列表
     * @apiSuccess {String} result.voucher.vouchertemplate_enddate 优惠券过期时间描述
     * @apiSuccess {Int} result.voucher.vouchertemplate_id 优惠券模板ID
     * @apiSuccess {Float} result.voucher.vouchertemplate_limit 优惠券最低消费金额
     * @apiSuccess {Int} result.voucher.vouchertemplate_price 优惠金额
     */
    public function goods_detail() {
        $goods_id = intval(input('param.goods_id'));
        $area_id = intval(input('param.area_id'));
        $bargain_id = intval(input('param.bargain_id'));
        // 商品详细信息
        $goods_model = model('goods');
        $goods_detail = $goods_model->getGoodsDetail($goods_id);
        //halt($goods_detail);
        if (empty($goods_detail)) {
            ds_json_encode(10001,lang('goods_goods_not_exist'));
        }
        foreach($goods_detail['gift_array'] as $k => $v){
            $goods_detail['gift_array'][$k]['gift_goodsimage_url']=goods_cthumb($v['gift_goodsimage'], '240', $goods_detail['goods_info']['store_id']);
        }
        //$goods_list = $goods_model->getGoodsContract(array(0=>$goods_detail['goods_info']));
        //$goods_detail['goods_info'] = $goods_list[0];
        //推荐商品
        $hot_sales = $goods_model->getGoodsCommendList($goods_detail['goods_info']['store_id'], 6);
        $goodsid_array = array();
        foreach ($hot_sales as $value) {
            $goodsid_array[] = $value['goods_id'];
        }
        $goods_commend_list = array();
        foreach ($hot_sales as $value) {
            $goods_commend = array();
            $goods_commend['goods_id'] = $value['goods_id'];
            $goods_commend['goods_name'] = $value['goods_name'];
            $goods_commend['goods_price'] = $value['goods_price'];
            $goods_commend['goods_promotion_price'] = $value['goods_promotion_price'];
            $goods_commend['goods_image_url'] = goods_cthumb($value['goods_image'], 240);
            $goods_commend_list[] = $goods_commend;
        }

        $goods_detail['goods_commend_list'] = $goods_commend_list;
        $store_info = model('store')->getStoreInfoByID($goods_detail['goods_info']['store_id']);
        $goods_detail['store_info']['store_logo'] = get_store_logo($store_info['store_logo'], 'store_logo');
        $goods_detail['store_info']['store_id'] = $store_info['store_id'];
        $goods_detail['store_info']['store_name'] = $store_info['store_name'];
        $goods_detail['store_info']['member_id'] = $store_info['member_id'];
        $goods_detail['store_info']['member_name'] = $store_info['member_name'];
        $goods_detail['store_info']['is_platform_store'] = $store_info['is_platform_store'];
        $goods_detail['store_info']['store_address'] = $store_info['area_info'].$store_info['store_address'];
        $goods_detail['store_info']['goods_count'] = $store_info['goods_count'];

        if ($store_info['is_platform_store']) {
            $goods_detail['store_info']['store_credit'] = array(
                'store_desccredit' => array(
                    'text' => lang('store_desccredit'),
                    'credit' => 5,
                    'percent' => '----',
                    'percent_class' => 'equal',
                    'percent_text' => lang('percent_text_equal'),
                ),
                'store_servicecredit' => array(
                    'text' => lang('store_servicecredit'),
                    'credit' => 5,
                    'percent' => '----',
                    'percent_class' => 'equal',
                    'percent_text' => lang('percent_text_equal'),
                ),
                'store_deliverycredit' => array(
                    'text' => lang('store_deliverycredit'),
                    'credit' => 5,
                    'percent' => '----',
                    'percent_class' => 'equal',
                    'percent_text' => lang('percent_text_equal'),
                ),
            );
        } else {
            $storeCredit = array();
            $percentClassTextMap = array(
                'equal' => lang('percent_text_equal'),
                'high' => lang('percent_text_high'),
                'low' => lang('percent_text_low'),
            );
            foreach ((array) $store_info['store_credit'] as $k => $v) {
                isset($v['percent_class']) && $v['percent_text'] = $percentClassTextMap[$v['percent_class']];
                $storeCredit[$k] = $v;
            }
            $goods_detail['store_info']['store_credit'] = $storeCredit;
        }

        //商品详细信息处理
        $goods_detail = $this->_goods_detail_extend($goods_detail);
        
        $goods_common_info = $goods_model->getGoodeCommonInfoByID($goods_detail['goods_info']['goods_commonid']);
        $goods_detail['mb_body']=array();
        if ($goods_common_info['mobile_body'] != '') {
            $goods_detail['mb_body'] = unserialize($goods_common_info['mobile_body']);
        }
        //如果没有砍价id就不显示砍价
        if(!$bargain_id){
            $goods_detail['goods_info']['bargain_info']='';
        }
        
        // 如果已登录 判断该商品是否已被收藏&&添加浏览记录
        if ($member_id = $this->getMemberIdIfExists()) {
            $c = (int) model('favorites')->getGoodsFavoritesCountByGoodsId($goods_id, $member_id);
            $goods_detail['is_favorate'] = $c > 0;


            if (!$goods_detail['goods_info']['is_virtual']) {
                // 店铺优惠券
                $condition = array();
                $condition['vouchertemplate_state'] = 1;
                $condition['vouchertemplate_enddate'] = array('gt', time());
                $condition['vouchertemplate_store_id'] = array('in', $store_info['store_id']);
                $voucher_template = model('voucher')->getVouchertemplateList($condition);
                if (!empty($voucher_template)) {
                    foreach ($voucher_template as $val) {
                        $param = array();
                        $param['vouchertemplate_id'] = $val['vouchertemplate_id'];
                        $param['vouchertemplate_price'] = $val['vouchertemplate_price'];
                        $param['vouchertemplate_limit'] = $val['vouchertemplate_limit'];
                        $param['vouchertemplate_enddate'] = $val['vouchertemplate_enddate'];
                        $goods_detail['voucher'][] = $param;
                    }
                }
            }
            //如果有砍价，看当前用户是否已发起过砍价
            if($goods_detail['goods_info']['bargain_info']!=''){
                $pbargainorder_model=model('pbargainorder');
                //是否有正在进行的砍价
                $bargainorder_info=$pbargainorder_model->getOnePbargainorder(array('bargainorder_initiator_id' => $member_id, 'bargain_id' => $goods_detail['goods_info']['bargain_info']['bargain_id']));
                if($bargainorder_info){
                    if(!$bargainorder_info['order_id']){
                        $goods_detail['goods_info']['bargainorder_info']=$bargainorder_info;
                    }else{//砍价成功就不能再下单了
                        $goods_detail['goods_info']['bargain_info']='';
                    }
                }
            }
            if (isset($goods_detail['goods_info']['pintuan_type']) && $goods_detail['goods_info']['pintuan_type']) {
                //不可以重复参加
                $order_id_list = db('ppintuanorder')->where(array('pintuan_id' => $goods_detail['goods_info']['pintuan_id'], 'pintuanorder_state' => array('<>', 0)))->column('order_id');
                if ($order_id_list) {
                    if (!$goods_detail['goods_info']['is_virtual']) {
                        if (db('order')->where(array('buyer_id' => $member_id, 'order_id' => array('in', $order_id_list)))->value('order_id')) {
                            unset($goods_detail['goods_info']['pintuan_type']);
                        }
                    } else {
                        if (db('vrorder')->where(array('buyer_id' => $member_id, 'order_id' => array('in', $order_id_list)))->value('order_id')) {
                            unset($goods_detail['goods_info']['pintuan_type']);
                        }
                    }
                }
            }
        }

        // 评价列表
        $goods_eval_list = model('evaluategoods')->getEvaluategoodsList(array('geval_goodsid' => $goods_id),'3');
        //$goods_eval_list = model('memberevaluate','logic')->evaluateListDity($goods_eval_list);
        $goods_detail['goods_eval_list'] = $goods_eval_list;

        //评价信息
        $goods_evaluate_info = model('evaluategoods')->getEvaluategoodsInfoByGoodsID($goods_id);
        $goods_detail['goods_evaluate_info'] = $goods_evaluate_info;

        $goods_detail['goods_hair_info'] = $this->_calc(0, $goods_id);
        
        $goods_detail['goods_info']['pintuangroup_share_id'] = intval(input('param.pintuangroup_share_id'));#获取分享拼团的用户ID
        $inviter_model=model('inviter');
        $goods_detail['inviter_amount']=0;
        if(config('inviter_show') && config('inviter_open') && $goods_detail['goods_info']['inviter_open'] && $member_id && $inviter_model->getInviterInfo('i.inviter_id='.$member_id.' AND i.inviter_state=1')){
            $inviter_amount=round($goods_detail['goods_info']['inviter_ratio_1'] / 100 * $goods_detail['goods_info']['goods_price'], 2);
            if($inviter_amount>0){
                $goods_detail['inviter_amount']=$inviter_amount;
            }
        }
        if(empty($goods_detail['mansong_info'])){
            $goods_detail['mansong_info']=false;
        }
        
        // 咨询类型
        $consult_type = rkcache('consulttype', true);
        
        $goods_detail['consult_type']=$consult_type;
        ds_json_encode(10000, '',$goods_detail);
    }


    /**
     * @api {POST} api/Goods/consulting_list 产品咨询列表
     * @apiVersion 1.0.0
     * @apiGroup Goods
     *
     * @apiParam {Int} goods_id 商品ID
     * @apiParam {Int} page 页码
     * @apiParam {Int} per_page 每页显示数量
     * 
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     * @apiSuccess {Object[]} result.consult_list  产品咨询列表
     * @apiSuccess {Int} result.consult_list.consult_id  产品咨询ID
     * @apiSuccess {Int} result.consult_list.goods_id  商品ID
     * @apiSuccess {String} result.consult_list.goods_name  商品名称
     * @apiSuccess {Int} result.consult_list.member_id  用户ID
     * @apiSuccess {String} result.consult_list.member_name  用户名称
     * @apiSuccess {Int} result.consult_list.store_id  店铺ID
     * @apiSuccess {String} result.consult_list.store_name  店铺名称
     * @apiSuccess {Int} result.consult_list.consulttype_id  咨询类型ID
     * @apiSuccess {String} result.consult_list.consult_content  咨询内容
     * @apiSuccess {Int} result.consult_list.consult_addtime  咨询时间，Unix时间戳
     * @apiSuccess {String} result.consult_list.consult_reply  回复内容
     * @apiSuccess {Int} result.consult_list.consult_replytime  回复时间，Unix时间戳
     * @apiSuccess {Int} result.consult_list.consult_isanonymous  是否匿名
     * @apiSuccess {Int} result.page_total  总页数
     * @apiSuccess {Boolean} result.hasmore  是否有更多 true是false否
     */
    public function consulting_list() {

        $goods_id = intval(input('param.goods_id'));
        if ($goods_id <= 0) {
            ds_json_encode(10001,lang('param_error'));
        }

        //得到商品咨询信息
        $consult_model = model('consult');
        $where = array();
        $where['goods_id'] = $goods_id;
        if (intval(input('param.ctid')) > 0) {
            $where['consulttype_id'] = intval(input('param.ctid'));
        }
        $consult_list = $consult_model->getConsultList($where, '*');

        $result = array_merge(array('consult_list'=> $consult_list), mobile_page($consult_model->page_info));
        ds_json_encode(10000, '',$result);
    }


    /**
     * @api {POST} api/Goods/save_consult 商品咨询添加
     * @apiVersion 1.0.0
     * @apiGroup Goods
     *
     * @apiParam {Int} goods_id 商品ID
     * @apiParam {String} goods_content 咨询内容
     * @apiParam {Int} consult_type_id 咨询类型ID
     * @apiParam {String} key 用户授权token
     * 
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     */
    public function save_consult() {
        $member_id = $this->getMemberIdIfExists();
        //检查是否可以评论
        if (!config('guest_comment') && !$member_id) {
            ds_json_encode(10001,lang('goods_index_goods_noallow'));
        }
        $goods_id = intval(input('post.goods_id'));
        if ($goods_id <= 0) {
            ds_json_encode(10001,lang('param_error'));
        }
        //咨询内容的非空验证
        if (trim(input('post.goods_content')) == "") {
            ds_json_encode(10001,lang('goods_index_input_consult'));
        }
        //表单验证
        $data = [
            'goods_content' => input('post.goods_content')
        ];
        $goods_validate = validate('goods');
        if (!$goods_validate->scene('save_consult')->check($data)) {
            ds_json_encode(10001,$goods_validate->getError());
        }


        //判断商品编号的存在性和合法性
        $goods = model('goods');
        $goods_info = $goods->getGoodsInfoByID($goods_id);
        if (empty($goods_info)) {
            ds_json_encode(10001,lang('goods_index_goods_not_exists'));
        }
        
        if ($member_id) {
            //查询会员信息
            $member_model = model('member');
            $member_info = $member_model->getMemberInfo(array('member_id' => $member_id));
            if (empty($member_info) || $member_info['is_allowtalk'] == 0) {
                ds_json_encode(10001,lang('goods_index_goods_noallow'));
            }
            $seller_model=model('seller');
            $seller_info=$seller_model->getSellerInfo(array('member_id' => $member_id));
            //判断是否是店主本人
            if ($seller_info && $goods_info['store_id'] == $seller_info['store_id']) {
                ds_json_encode(10001,lang('goods_index_consult_store_error'));
            }
        }
        
        //检查店铺状态
        $store_model = model('store');
        $store_info = $store_model->getStoreInfoByID($goods_info['store_id']);
        if ($store_info['store_state'] == '0' || intval($store_info['store_state']) == '2' || (intval($store_info['store_endtime']) != 0 && $store_info['store_endtime'] <= time())) {
            ds_json_encode(10001,lang('goods_index_goods_store_closed'));
        }
        //接收数据并保存
        $input = array();
        $input['goods_id'] = $goods_id;
        $input['goods_name'] = $goods_info['goods_name'];
        $input['member_id'] = intval($member_id) > 0 ? $member_id : 0;
        $input['member_name'] = isset($member_info) ? $member_info['member_name'] : '';
        $input['store_id'] = $store_info['store_id'];
        $input['store_name'] = $store_info['store_name'];
        $input['consulttype_id'] = intval(input('post.consult_type_id',1));
        $input['consult_addtime'] = TIMESTAMP;
        $input['consult_content'] = input('post.goods_content');
        $input['consult_isanonymous'] = input('post.hide_name')=='hide'?1:0;
        $consult_model = model('consult');
        if ($consult_model->addConsult($input)) {
            ds_json_encode(10000,lang('goods_index_consult_success'));
        } else {
            ds_json_encode(10001,lang('goods_index_consult_fail'));
        }
    }
    
    /**
     * 记录浏览历史
     */
    public function addbrowse() {
    	$goods_id = intval(input('param.gid'));
    	model('goodsbrowse')->addViewedGoods($goods_id, $this->member_info['member_id'], $this->store_info['store_id']);
    	exit();
    }
    /**
     * 商品详细信息处理
     */
    private function _goods_detail_extend($goods_detail) {
        //整理商品规格
        unset($goods_detail['spec_list']);
        $goods_detail['spec_list'] = $goods_detail['spec_list_mobile'];
        unset($goods_detail['spec_list_mobile']);

        //整理商品图片
        unset($goods_detail['goods_image']);
        $goods_detail['goods_image'] = implode(',', $goods_detail['goods_image_mobile']);
        unset($goods_detail['goods_image_mobile']);

        //商品链接
        $goods_detail['goods_info']['goods_url'] = url('Goods/index', array('goods_id' => $goods_detail['goods_info']['goods_id']));
        
        //商品PC端详情信息
        $goods_detail['goods_info']['goods_body'] = htmlspecialchars_decode($goods_detail['goods_info']['goods_body']);

        //整理数据
//        unset($goods_detail['goods_info']['goods_commonid']);
        unset($goods_detail['goods_info']['gc_id']);
        unset($goods_detail['goods_info']['gc_name']);
        unset($goods_detail['goods_info']['store_id']);
        unset($goods_detail['goods_info']['store_name']);
        unset($goods_detail['goods_info']['brand_id']);
        unset($goods_detail['goods_info']['brand_name']);
        unset($goods_detail['goods_info']['type_id']);
        unset($goods_detail['goods_info']['goods_image']);
        unset($goods_detail['goods_info']['goods_state']);
        unset($goods_detail['goods_info']['goods_stateremark']);
        unset($goods_detail['goods_info']['goods_verify']);
        unset($goods_detail['goods_info']['goods_verifyremark']);
        unset($goods_detail['goods_info']['goods_lock']);
        unset($goods_detail['goods_info']['goods_addtime']);
        unset($goods_detail['goods_info']['goods_edittime']);
        unset($goods_detail['goods_info']['goods_shelftime']);
        unset($goods_detail['goods_info']['goods_show']);
        unset($goods_detail['goods_info']['goods_commend']);
        unset($goods_detail['goods_info']['explain']);
        unset($goods_detail['goods_info']['buynow_text']);
        unset($goods_detail['groupbuy_info']);
        unset($goods_detail['xianshi_info']);

        return $goods_detail;
    }

    /**
     * @api {POST} api/Goods/goods_evaluate 商品评论
     * @apiVersion 1.0.0
     * @apiGroup Goods
     *
     * @apiParam {Int} goods_id 商品ID
     * @apiParam {Int} type 类型 1好评 2中评 3差评
     * @apiParam {Int} page 页码
     * @apiParam {Int} per_page 每页显示数量
     *
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     * @apiSuccess {Object[]} result.goods_eval_list  评论列表
     * @apiSuccess {Int} result.goods_eval_list.geval_addtime  评论时间
     * @apiSuccess {Int} result.goods_eval_list.geval_content  评论内容
     * @apiSuccess {Int} result.goods_eval_list.geval_explain  店主解释
     * @apiSuccess {Int} result.goods_eval_list.geval_frommemberid  评论用户ID
     * @apiSuccess {Int} result.goods_eval_list.geval_frommembername  评论用户名
     * @apiSuccess {Int} result.goods_eval_list.geval_goodsid  评论商品ID
     * @apiSuccess {Int} result.goods_eval_list.geval_goodsimage  评论商品图片
     * @apiSuccess {Int} result.goods_eval_list.geval_goodsname  评论商品名称
     * @apiSuccess {Int} result.goods_eval_list.geval_goodsprice  评论价格
     * @apiSuccess {Int} result.goods_eval_list.geval_id  评论ID
     * @apiSuccess {Int} result.goods_eval_list.geval_image  评论晒图
     * @apiSuccess {Int} result.goods_eval_list.geval_isanonymous  是否匿名
     * @apiSuccess {Int} result.goods_eval_list.geval_ordergoodsid  订单商品ID
     * @apiSuccess {Int} result.goods_eval_list.geval_orderid  订单ID
     * @apiSuccess {Int} result.goods_eval_list.geval_orderno  订单编号
     * @apiSuccess {Int} result.goods_eval_list.geval_remark  管理员对评价的处理备注
     * @apiSuccess {Int} result.goods_eval_list.geval_scores  评分
     * @apiSuccess {Int} result.goods_eval_list.geval_state  评价信息的状态 0为正常 1为禁止显示
     * @apiSuccess {Int} result.goods_eval_list.geval_storeid  店铺ID
     * @apiSuccess {Int} result.goods_eval_list.geval_storename  店铺名称
     * @apiSuccess {Int} result.goods_eval_list.member_avatar  用户头像
     * @apiSuccess {Int} result.page_total  总页数
     * @apiSuccess {Boolean} result.hasmore  是否有更多 true是false否
     */
    public function goods_evaluate() {
        $goods_id = intval(input('param.goods_id'));
        $type = intval(input('param.type'));

        $condition = array();
        $condition['geval_goodsid'] = $goods_id;
        switch ($type) {
            case '1':
                $condition['geval_scores'] = array('in', '5,4');
                break;
            case '2':
                $condition['geval_scores'] = array('in', '3,2');
                break;
            case '3':
                $condition['geval_scores'] = array('in', '1');
                break;
            case '4':
                //$condition['geval_image|geval_image_again'] = array('neq', '');  //追加评价带后续处理
                $condition['geval_image'] = array('neq', '');
                break;
            case '5':
                $condition['geval_content_again'] = array('neq', '');
                break;
        }

        //查询商品评分信息
        $evaluategoods_model = model('evaluategoods');
        $goods_eval_list = $evaluategoods_model->getEvaluategoodsList($condition, 10);
        foreach ($goods_eval_list as $k=>$val){
			if($val['geval_isanonymous']){
                $goods_eval_list[$k]['member_avatar']=get_member_avatar_for_id(0);
                $goods_eval_list[$k]['geval_frommembername']=str_cut($val['geval_frommembername'],2).'***';
            }
            if(!empty($goods_eval_list[$k]['geval_image'])) {
            $goods_eval_list[$k]['geval_image']=explode(',',$goods_eval_list[$k]['geval_image']);
                foreach ($goods_eval_list[$k]['geval_image'] as $kk => $vv) {
                    $store_id = substr($vv, 0, strpos($vv, '_'));
                    $goods_eval_list[$k]['geval_image'][$kk] = UPLOAD_SITE_URL . '/' . ATTACH_MALBUM . '/' . $store_id . '/' . $vv;
                }
            }
        }
        $goods_eval_list = model('memberevaluate','logic')->evaluateListDity($goods_eval_list);
        $result = array_merge(array('goods_eval_list' => $goods_eval_list), mobile_page( is_object($evaluategoods_model->page_info)?$evaluategoods_model->page_info:0));
        ds_json_encode(10000, '',$result);
    }

    /**
     * 商品详细页运费显示
     *
     * @return unknown
     */
    public function calc() {
        $area_id = intval(input('param.area_id'));
        $goods_id = intval(input('param.goods_id'));
        ds_json_encode(10000, '',$this->_calc($area_id, $goods_id));
    }

    public function _calc($area_id, $goods_id) {
        $goods_info = model('goods')->getGoodsInfo(array('goods_id' => $goods_id), 'transport_id,store_id,goods_freight');
        $store_info = model('store')->getStoreInfoByID($goods_info['store_id']);
		$if_store=true;
        $area_name='';
        if ($area_id <= 0) {
            if (strpos($store_info['deliver_region'], '|')) {
                $store_info['deliver_region'] = explode('|', $store_info['deliver_region']);
                $store_info['deliver_region_ids'] = explode(' ', $store_info['deliver_region'][0]);
            }
            if(isset($store_info['deliver_region_ids'])){
                $area_id = intval($store_info['deliver_region_ids'][0]);
                $area_name = $store_info['deliver_region'][1];
            }
        }
        if ($goods_info['transport_id']) {
            $freight_total = model('transport')->calcTransport(intval($goods_info['transport_id']), $area_id);
            if ($freight_total > 0) {
                if ($store_info['store_free_price'] > 0) {
                    if ($freight_total >= $store_info['store_free_price']) {
                        $freight_total = lang('free_shipping');
                    } else {
                        $freight_total = lang('freight').'：' . $freight_total . lang('shop_with') . $store_info['store_free_price'] . lang('goods_index_yuan').lang('free_shipping');
                    }
                } else {
                    $freight_total = lang('freight').'：' . $freight_total . lang('goods_index_yuan');
                }
            } else {
                if ($freight_total === false) {
                    $if_store = false;
                }
                $freight_total = lang('free_shipping');
            }
        } else {
            $freight_total = $goods_info['goods_freight'] > 0 ? lang('freight').'：' . $goods_info['goods_freight'] . lang('goods_index_yuan') : lang('free_shipping');
        }

        return array('content' => $freight_total, 'if_store_cn' => $if_store === false ? lang('goods_out_stock') : lang('goods_stock'), 'if_store' => $if_store === false ? false : true, 'area_name' => $area_name ? $area_name : lang('goods_index_trans_country'));
    }


    public function auto_complete() {
        if (!config('fullindexer.open')){
            ds_json_encode(10001, lang('param_error'));
        }
        try {
            require(EXTEND_PATH . 'xs/lib/XS.php');
            $obj_doc = new \XSDocument();
            $obj_xs = new \XS(config('fullindexer.appname'));
            $obj_index = $obj_xs->index;
            $obj_search = $obj_xs->search;
            $obj_search->setCharset(CHARSET);
            $corrected = $obj_search->getExpandedQuery(input('param.term'));
            if (count($corrected) !== 0) {
                $data = array();
                foreach ($corrected as $word) {
                    $row['id'] = $word;
                    $row['label'] = $word;
                    $row['value'] = $word;
                    $data[] = $row;
                }
                ds_json_encode(10000, '',array('list' => $data));
            }
        } catch (XSException $e) {
            if (is_object($obj_index)) {
                $obj_index->flushIndex();
            }
        }
    }

    /**
     * 经纬度转换
     * @param unknown $bdLat
     * @param unknown $bdLon
     * @return multitype:number
     */
    public function bd_decrypt($bdLat, $bdLon) {
        $x = $bdLon - 0.0065;
        $y = $bdLat - 0.006;
        $z = sqrt($x * $x + $y * $y) - 0.00002 * sin($y * $this->x_pi);
        $theta = atan2($y, $x) - 0.000003 * cos($x * $this->x_pi);
        $gcjLon = $z * cos($theta);
        $gcjLat = $z * sin($theta);
        return array('lat' => $gcjLat, 'lon' => $gcjLon);
    }

    /**
     *  @desc 根据两点间的经纬度计算距离
     *  @param float $lat 纬度值
     *  @param float $lng 经度值
     */
    private function getDistance($lat1, $lng1, $lat2, $lng2) {
        $earthRadius = 6367000; //approximate radius of earth in meters

        /*
          Convert these degrees to radians
          to work with the formula
         */

        $lat1 = ($lat1 * pi() ) / 180;
        $lng1 = ($lng1 * pi() ) / 180;

        $lat2 = ($lat2 * pi() ) / 180;
        $lng2 = ($lng2 * pi() ) / 180;

        /*
          Using the
          Haversine formula

          http://en.wikipedia.org/wiki/Haversine_formula

          calculate the distance
         */

        $calcLongitude = $lng2 - $lng1;
        $calcLatitude = $lat2 - $lat1;
        $stepOne = pow(sin($calcLatitude / 2), 2) + cos($lat1) * cos($lat2) * pow(sin($calcLongitude / 2), 2);
        $stepTwo = 2 * asin(min(1, sqrt($stepOne)));
        $calculatedDistance = $earthRadius * $stepTwo;

        return round($calculatedDistance);
    }

    private function parseDistance($num = 0) {
        $num = floatval($num);
        if ($num >= 1000) {
            $num = $num / 1000;
            return str_replace('.0', '', number_format($num, 1, '.', '')) . 'km';
        } else {
            return $num . 'm';
        }
    }

}
