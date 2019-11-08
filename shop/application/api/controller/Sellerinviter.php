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
 * 卖家分销控制器
 */
class Sellerinviter extends MobileSeller {

    public function _initialize() {
        parent::_initialize();
        Lang::load(APP_PATH . 'home/lang/' . config('default_lang') . '/sellerinviter.lang.php');
        if (intval(config('promotion_allow')) !== 1) {
            ds_json_encode(10001, lang('promotion_unavailable'));
        }
    }
    /**
     * @api {POST} api/Sellerinviter/order_list 分销业绩
     * @apiVersion 1.0.0
     * @apiGroup Sellerinviter
     *
     * @apiHeader {String} X-DS-KEY 用户授权token
     * 
     * @apiParam {String} orderinviter_order_sn 订单号
     * @apiParam {String} page 页码
     * @apiParam {String} pagesize 每页显示数量
     *
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     * @apiSuccess {Object[]} result.order_list  分销订单列表 （返回字段参考orderinviter表）
     * @apiSuccess {Int} result.page_total  总页数
     * @apiSuccess {Boolean} result.hasmore  是否有更多 true是false否
     */
    public function order_list() {
        $conditions = array('orderinviter_store_id' => $this->store_info['store_id']);
        if (input('param.orderinviter_order_sn')) {
            $conditions['orderinviter_order_sn'] = array('like', '%' . input('param.orderinviter_order_sn') . '%');
        }
        $orderinviter_list = db('orderinviter')->where($conditions)->order('orderinviter_id desc')->paginate(10);
        
        $result = array_merge(array('order_list' => $orderinviter_list->items()), mobile_page($orderinviter_list->render()));
        ds_json_encode(10000, lang('ds_common_op_succ'), $result);
    }
    
    /**
     * @api {POST} api/Sellerinviter/goods_list 分销商品
     * @apiVersion 1.0.0
     * @apiGroup Sellerinviter
     *
     * @apiHeader {String} X-DS-KEY 用户授权token
     * 
     * @apiParam {String} goods_name 商品名
     * @apiParam {String} page 页码
     * @apiParam {String} pagesize 每页显示数量
     *
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     * @apiSuccess {Object[]} result.goods_list  分销商品列表 （返回字段参考goodscommon表）
     * @apiSuccess {Object} result.storage_array  库存列表，键为商品公共ID
     * @apiSuccess {Int} result.storage_array.sum  库存
     * @apiSuccess {Int} result.storage_array.goods_id  商品ID
     */
    public function goods_list() {
        $goods_model = model('goods');

        if (check_platform_store()) {
            $this->assign('isPlatformStore', true);
        }

        $condition = array();
        $condition['store_id'] = $this->store_info['store_id'];
        $condition['inviter_open'] = 1;
        if ((input('param.goods_name'))) {
            $condition['goods_name'] = array('like', '%' . input('param.goods_name') . '%');
        }

        $goods_list = $goods_model->getGoodsCommonList($condition, '*');
        $this->assign('goods_list', $goods_list);

        $storage_array = $goods_model->calculateStorage($goods_list);
        $this->assign('storage_array', $storage_array);
        ds_json_encode(10000,'',array('goods_list'=>$goods_list,'storage_array'=>$storage_array));
    }

    /**
     * @api {POST} api/Sellerinviter/goods_add 添加分销活动
     * @apiVersion 1.0.0
     * @apiGroup Sellerinviter
     *
     * @apiHeader {String} X-DS-KEY 用户授权token
     * 
     * @apiParam {Int} inviter_goods_commonid 商品公共ID
     * @apiParam {Float} inviter_ratio_1 一级分销佣金比例
     * @apiParam {Float} inviter_ratio_2 二级分销佣金比例
     * @apiParam {Float} inviter_ratio_3 三级分销佣金比例
     *
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     */
    public function goods_add() {
        $goods_model = model('goods');
        //验证输入
        $inviter_goods_commonid = intval(input('post.inviter_goods_commonid'));
        $inviter_ratio_1 = floatval(input('post.inviter_ratio_1'));
        $inviter_ratio_2 = floatval(input('post.inviter_ratio_2'));
        $inviter_ratio_3 = floatval(input('post.inviter_ratio_3'));

        if (!($inviter_goods_commonid)) {
            ds_json_encode(10001, lang('inviter_goods_commonid_required'));
        }
        $goods_info = $goods_model->getGoodeCommonInfo('goods_commonid=' . $inviter_goods_commonid . ' AND store_id=' . $this->store_info['store_id']);
        if (!$goods_info) {
            ds_json_encode(10001, lang('sellerinviter_goods_empty'));
        }
        if ($inviter_ratio_1 > config('inviter_ratio_1')) {
            ds_json_encode(10001, lang('inviter_ratio_1_max') . config('inviter_ratio_1') . lang('ds_percent'));
        }
        if ($inviter_ratio_2 > config('inviter_ratio_2')) {
            ds_json_encode(10001, lang('inviter_ratio_2_max') . config('inviter_ratio_2') . lang('ds_percent'));
        }
        if ($inviter_ratio_3 > config('inviter_ratio_3')) {
            ds_json_encode(10001, lang('inviter_ratio_3_max') . config('inviter_ratio_3') . lang('ds_percent'));
        }
        $result = $goods_model->editGoodsCommonById(array(
            'inviter_open' => 1,
            'inviter_ratio_1' => $inviter_ratio_1,
            'inviter_ratio_2' => $inviter_ratio_2,
            'inviter_ratio_3' => $inviter_ratio_3,
                ), array($inviter_goods_commonid));
        if ($result) {
            $this->recordSellerlog('添加分销商品，商品编号：' . $inviter_goods_commonid);
            ds_json_encode(10000, lang('goods_add_success'));
        } else {
            ds_json_encode(10001, lang('goods_add_fail'));
        }
    }

    /**
     * @api {POST} api/Sellerinviter/goods_info 获取分销商品信息
     * @apiVersion 1.0.0
     * @apiGroup Sellerinviter
     *
     * @apiHeader {String} X-DS-KEY 用户授权token
     * 
     * @apiParam {Int} goods_commonid 商品公共ID
     *
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     * @apiSuccess {Object} result.info  商品信息 （返回字段参考goodscommon表）
     */
    public function goods_info() {
        $goods_model = model('goods');
        $goods_commonid = intval(input('param.goods_commonid'));
        $goods_info = $goods_model->getGoodeCommonInfo('goods_commonid=' . $goods_commonid . ' AND inviter_open=1 AND store_id=' . $this->store_info['store_id']);
        if (!$goods_info) {
            ds_json_encode(10001,lang('sellerinviter_goods_empty'));
        }
        ds_json_encode(10000,'',array('info'=>$goods_info));
    }

    /**
     * @api {POST} api/Sellerinviter/goods_edit 编辑分销活动
     * @apiVersion 1.0.0
     * @apiGroup Sellerinviter
     *
     * @apiHeader {String} X-DS-KEY 用户授权token
     * 
     * @apiParam {Int} inviter_goods_commonid 商品公共ID
     * @apiParam {Float} inviter_ratio_1 一级分销佣金比例
     * @apiParam {Float} inviter_ratio_2 二级分销佣金比例
     * @apiParam {Float} inviter_ratio_3 三级分销佣金比例
     *
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     */
    public function goods_edit() {
        $goods_model = model('goods');
        //验证输入
        $inviter_goods_commonid = intval(input('post.inviter_goods_commonid'));
        $inviter_ratio_1 = floatval(input('post.inviter_ratio_1'));
        $inviter_ratio_2 = floatval(input('post.inviter_ratio_2'));
        $inviter_ratio_3 = floatval(input('post.inviter_ratio_3'));

        if (!($inviter_goods_commonid)) {
            ds_json_encode(10001, lang('inviter_goods_commonid_required'));
        }
        $goods_info = $goods_model->getGoodeCommonInfo('goods_commonid=' . $inviter_goods_commonid . ' AND inviter_open=1 AND store_id=' . $this->store_info['store_id']);
        if (!$goods_info) {
            ds_json_encode(10001, lang('sellerinviter_goods_empty'));
        }
        if ($inviter_ratio_1 > config('inviter_ratio_1')) {
            ds_json_encode(10001, lang('inviter_ratio_1_max') . config('inviter_ratio_1') . lang('ds_percent'));
        }
        if ($inviter_ratio_2 > config('inviter_ratio_2')) {
            ds_json_encode(10001, lang('inviter_ratio_2_max') . config('inviter_ratio_2') . lang('ds_percent'));
        }
        if ($inviter_ratio_3 > config('inviter_ratio_3')) {
            ds_json_encode(10001, lang('inviter_ratio_3_max') . config('inviter_ratio_3') . lang('ds_percent'));
        }
        $result = $goods_model->editGoodsCommonById(array(
            'inviter_ratio_1' => $inviter_ratio_1,
            'inviter_ratio_2' => $inviter_ratio_2,
            'inviter_ratio_3' => $inviter_ratio_3,
                ), array($inviter_goods_commonid));
        if ($result) {
            $this->recordSellerlog('编辑分销商品，商品编号：' . $inviter_goods_commonid);
            ds_json_encode(10000, lang('goods_edit_success'));
        } else {
            ds_json_encode(10001, lang('goods_edit_fail'));
        }
    }

    /**
     * @api {POST} api/Sellerinviter/goods_del 删除分销商品
     * @apiVersion 1.0.0
     * @apiGroup Sellerinviter
     *
     * @apiHeader {String} X-DS-KEY 用户授权token
     * 
     * @apiParam {Int} goods_commonid 商品公共ID
     *
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     */
    public function goods_del() {
        $goods_model = model('goods');
        $goods_commonid = intval(input('param.goods_commonid'));
        $goods_info = $goods_model->getGoodeCommonInfo('goods_commonid=' . $goods_commonid . ' AND inviter_open=1 AND store_id=' . $this->store_info['store_id']);
        if (!$goods_info) {
            ds_json_encode(10001, lang('sellerinviter_goods_empty'));
        }
        $result = $goods_model->editGoodsCommonById(array(
            'inviter_open' => 0,
                ), array($goods_commonid));
        if ($result) {
            $this->recordSellerlog('删除分销商品，商品编号：' . $goods_commonid);
            ds_json_encode(10000, lang('goods_del_success'));
        } else {
            ds_json_encode(10001, lang('goods_del_fail'));
        }
    }

    /**
     * @api {POST} api/Sellerinviter/search_goods 选择活动商品
     * @apiVersion 1.0.0
     * @apiGroup Sellerinviter
     *
     * @apiHeader {String} X-DS-KEY 用户授权token
     * 
     * @apiParam {String} goods_name 商品名称
     * @apiParam {String} page 页码
     * @apiParam {String} pagesize 每页显示数量
     *
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     */
    public function search_goods() {
        $goods_model = model('goods');
        $condition = array();
        $condition['store_id'] = $this->store_info['store_id'];
        $condition['inviter_open'] = 0; //未选择为分销的商品
        $condition['goods_name'] = array('like', '%' . input('param.goods_name') . '%');
        $goods_list = $goods_model->getGoodsCommonList($condition, 'goods_commonid,goods_name,goods_price,goods_image,inviter_open');
        foreach ($goods_list as $key => $value) {
            $goods_list[$key]['goods_image'] = goods_cthumb($value['goods_image'], '240');
        }
        ds_json_encode(10000, '', array('goods_list' => $goods_list));
    }

    public function inviter_goods_info() {
        $goods_commonid = intval(input('param.goods_commonid'));

        $data = array();
        $data['result'] = true;


        //获取商品具体信息用于显示
        $goods_model = model('goods');
        $condition = array();
        $condition['goods_commonid'] = $goods_commonid;
        $goods_list = $goods_model->getGoodsOnlineList($condition);

        if (empty($goods_list)) {
            $data['result'] = false;
            $data['message'] = lang('param_error');
            echo json_encode($data);
            die;
        }


        $goods_info = $goods_list[0];
        $data['goods_id'] = $goods_info['goods_id'];
        $data['goods_commonid'] = $goods_info['goods_commonid'];
        $data['goods_name'] = $goods_info['goods_name'];
        $data['goods_price'] = $goods_info['goods_price'];
        $data['goods_image'] = goods_thumb($goods_info, 240);
        $data['goods_href'] = url('Goods/index', array('goods_id' => $goods_info['goods_id']));

        echo json_encode($data);
        die;
    }

}
