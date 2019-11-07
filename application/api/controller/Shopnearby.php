<?php

namespace app\api\controller;

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
 * 附件店铺控制器
 */
class Shopnearby extends MobileMall {

    public function _initialize() {
        parent::_initialize();
    }

    /**
     * @api {POST} api/Shopnearby/index 首页显示
     * @apiVersion 1.0.0
     * @apiGroup Shopnearby
     *
     * @apiHeader {String} X-DS-KEY 用户授权token
     *
     * @apiParam {Int} brand 所属品牌id
     * @apiParam {Int} category 所属分类id
     * @apiParam {String} keyword 关键字
     * @apiParam {String} longitude 经度
     * @apiParam {String} latitude 纬度
     * @apiParam {String} sort_key 键
     * @apiParam {String} sort_value 值
     * @apiParam {Int} page 当前第几页
     * @apiParam {Int} per_page 每页多少
     *
     * @apiSuccess {String} code 返回码,10000为成功
     * @apiSuccess {String} message  返回消息
     * @apiSuccess {Object} result  返回数据
     * @apiSuccess {Object[]} result.store_list  店铺列表 （返回字段参考store）
     * @apiSuccess {Float} result.store_list.distance  距离
     * @apiSuccess {Float} result.store_list.store_credit_percent 店铺信用评分
     * @apiSuccess {Int} result.page_total  总页数
     * @apiSuccess {Boolean} result.hasmore  是否有更多 true是false否
     */
    public function index() {
        $this->_get_Own_Store_List();
    }

    private function _get_Own_Store_List() {
        //查询条件
        $condition = array('store_state'=>1);
        if (!empty(input('post.keyword'))) {
            $condition['store_name'] = array('like', '%' . input('post.keyword') . '%');
        }
        $storeclass_id = intval(input('param.storeclass_id'));
        if ($storeclass_id>0) {
            $condition['storeclass_id'] = $storeclass_id;
        }
        $lat = input('post.latitude', 0);
        $lng = input('post.longitude', 0);
        $sort_key = input('post.sort_key');
        switch ($sort_key) {
            case 'sale':
                $order = 'store_sales desc';
                break;
            case 'distance':
                $order = 'distance asc';
                break;

            case 'score':
                $order = 'store_credit desc';
                break;
    
            default:
                $order = 'is_platform_store desc,store_recommend desc,store_sort asc,store_baozh desc,store_credit desc,store_desccredit desc';
        }
        $evaluatestore_model = model('evaluatestore');
        $store_list_object = db('store')->where($condition)
                ->where('(2 * 6378.137* ASIN(SQRT(POW(SIN(PI()*(' . $lat . '-store_latitude)/360),2)+COS(PI()*' . $lat . '/180)* COS(store_latitude * PI()/180)*POW(SIN(PI()*(' . $lng . '-store_longitude)/360),2)))) < 100000')
                ->fieldRaw('store_id,is_platform_store,store_name,area_info,store_address,storeclass_id,store_collect,store_logo,store_avatar,(2 * 6378.137* ASIN(SQRT(POW(SIN(PI()*(' . $lat . '-store_latitude)/360),2)+COS(PI()*' . $lat . '/180)* COS(store_latitude * PI()/180)*POW(SIN(PI()*(' . $lng . '-store_longitude)/360),2)))) as distance')
                ->order($order)
                ->paginate($this->pagesize, false, ['query' => request()->param()]);
        $store_list = $store_list_object->items();
        $memberId = $this->getMemberIdIfExists();
        foreach ($store_list as $key => $value) {
            $store_list[$key]['distance'] = round($value['distance'], 2);
            $store_list[$key]['store_avatar'] = get_store_logo($value['store_avatar'], 'store_avatar');
            $store_list[$key]['store_logo'] = get_store_logo($value['store_logo']);
            $store_evaluate_info = $evaluatestore_model->getEvaluatestoreInfoByStoreID($value['store_id'], $value['storeclass_id']);
            $store_list[$key]['store_credit_percent'] = $store_evaluate_info['store_credit_percent'];
            $store_list[$key]['goods_list']=model('goods')->getGoodsListByColorDistinct(array('store_id'=>$value['store_id'],'goods_commend'=>1), 'goods_image,goods_id,goods_price', 'goods_id desc', 0, 4);
            foreach($store_list[$key]['goods_list'] as $k => $v){
                $store_list[$key]['goods_list'][$k]['goods_image_url']=goods_cthumb($v['goods_image'], 480, $value['store_id']);
            }
            // 如果已登录 判断该店铺是否已被收藏
            if ($memberId) {
                $c = (int) model('favorites')->getStoreFavoritesCountByStoreId($value['store_id'], $memberId);
                $store_list[$key]['is_favorate'] = $c > 0;
            } else {
                $store_list[$key]['is_favorate'] = false;
            }
        }
        $result = array_merge(array('store_list' => $store_list), mobile_page($store_list_object));
        ds_json_encode(10000, '', $result);
    }

}
