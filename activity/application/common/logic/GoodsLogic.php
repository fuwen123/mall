<?php
/**
 * tpshop
 * ============================================================================
 * 版权所有 2015-2027 深圳搜豹网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.tp-shop.cn
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用 .
 * 不允许对程序代码以任何形式任何目的的再发布。
 * 采用最新Thinkphp5助手函数特性实现单字母函数M D U等简写方式
 * ============================================================================
 */

namespace app\common\logic;

use app\common\model\Goods;
use think\Model;
use think\Db;
/**
 * 分类逻辑定义
 * Class CatsLogic
 * @package common\Logic
 */
class GoodsLogic extends Model
{
    /**
     * @param $goods_id_arr
     * @param $filter_param
     * @param $action
     * @return array|mixed 这里状态一般都为1 result 不是返回数据 就是空
     * 获取 商品列表页帅选品牌
     */
    public function get_filter_brand($goods_id_arr, $filter_param, $action)
    {
        if (!empty($filter_param['brand_id'])) {
            return array();
        }

        $map['goods_id'] = ['in', $goods_id_arr ];
        $map['brand_id'] = ['>', 0];
        $brand_id_arr = M('goods')->where($map)->column('brand_id');
        $list_brand = M('brand')->where(['id'=>['in',$brand_id_arr]])->limit('30')->select();

        foreach ($list_brand as $k => $v) {
            // 筛选参数
            $filter_param['brand_id'] = $v['id'];
            $list_brand[$k]['href'] = urldecode(U("Goods/$action", $filter_param, ''));
        }
        return $list_brand;
    }

    /**
     * @param $goods_id_arr
     * @param $filter_param
     * @param $action
     * @param int $mode  0  返回数组形式  1 直接返回result
     * @return array 这里状态一般都为1 result 不是返回数据 就是空
     * 获取 商品列表页帅选规格
     */
    public function get_filter_spec($goods_id_arr, $filter_param, $action, $mode = 0)
    {
        $goods_id_str = implode(',', $goods_id_arr);
        $goods_id_str = $goods_id_str ? $goods_id_str : '0';
        $spec_key = DB::query("select group_concat(`key` separator  '_') as `key` from __PREFIX__spec_goods_price where goods_id in($goods_id_str)");  //where("goods_id in($goods_id_str)")->select();
        $spec_key = explode('_', $spec_key[0]['key']);
        $spec_key = array_unique($spec_key);
        $spec_key = array_filter($spec_key);

        if (empty($spec_key)) {
            if ($mode == 1) return array();
            return array('status' => 1, 'msg' => '', 'result' => array());
        }
        $spec = M('spec')->where(array('search_index'=>1))->getField('id,name');
        $spec_item = M('spec_item')->where(array('spec_id'=>array('in',array_keys($spec))))->getField('id,spec_id,item');

        $list_spec = array();
        $old_spec = $filter_param['spec'];
        foreach ($spec_key as $k => $v) {
            if (strpos($old_spec, $spec_item[$v]['spec_id'] . '_') === 0 || strpos($old_spec, '@' . $spec_item[$v]['spec_id'] . '_') || $spec_item[$v]['spec_id'] == '')
                continue;
            $list_spec[$spec_item[$v]['spec_id']]['spec_id'] = $spec_item[$v]['spec_id'];
            $list_spec[$spec_item[$v]['spec_id']]['name'] = $spec[$spec_item[$v]['spec_id']];
            //$list_spec[$spec_item[$v]['spec_id']]['item'][$v] = $spec_item[$v]['item'];

            // 帅选参数
            if (!empty($old_spec))
                $filter_param['spec'] = $old_spec . '@' . $spec_item[$v]['spec_id'] . '_' . $v;
            else
                $filter_param['spec'] = $spec_item[$v]['spec_id'] . '_' . $v;
            $list_spec[$spec_item[$v]['spec_id']]['item'][] = array('key' => $spec_item[$v]['spec_id'], 'val' => $v, 'item' => $spec_item[$v]['item'], 'href' => urldecode(U("Goods/$action", $filter_param, '')));
        }

        if ($mode == 1) return $list_spec;
        return array('status' => 1, 'msg' => '', 'result' => $list_spec);
    }
    /**
     * @param array $goods_id_arr
     * @param $filter_param
     * @param $action
     * @param int $mode 0  返回数组形式  1 直接返回result
     * @return array
     * 获取商品列表页帅选属性
     */
    public function get_filter_attr($goods_id_arr = array(), $filter_param, $action, $mode = 0)
    {
        $goods_id_str = implode(',', $goods_id_arr);
        $goods_id_str = $goods_id_str ? $goods_id_str : '0';
        $goods_attr = M('goods_attr')->where(['goods_id'=>['in',$goods_id_str],'attr_value'=>['<>','']])->select();
        // $goods_attr = M('goods_attr')->where("attr_value != ''")->select();
        $goods_attribute = M('goods_attribute')->where("attr_index = 1")->getField('attr_id,attr_name,attr_index');
        if (empty($goods_attr)) {
            if ($mode == 1) return array();
            return array('status' => 1, 'msg' => '', 'result' => array());
        }
        $list_attr = $attr_value_arr = array();
        $old_attr = $filter_param['attr'];
        foreach ($goods_attr as $k => $v) {
            // 存在的帅选不再显示
            if (strpos($old_attr, $v['attr_id'] . '_') === 0 || strpos($old_attr, '@' . $v['attr_id'] . '_'))
                continue;
            if ($goods_attribute[$v['attr_id']]['attr_index'] == 0)
                continue;
            $v['attr_value'] = trim($v['attr_value']);
            // 如果同一个属性id 的属性值存储过了 就不再存贮
            if (in_array($v['attr_id'] . '_' . $v['attr_value'], (array)$attr_value_arr[$v['attr_id']]))
                continue;
            $attr_value_arr[$v['attr_id']][] = $v['attr_id'] . '_' . $v['attr_value'];

            $list_attr[$v['attr_id']]['attr_id'] = $v['attr_id'];
            $list_attr[$v['attr_id']]['attr_name'] = $goods_attribute[$v['attr_id']]['attr_name'];

            // 帅选参数
            if (!empty($old_attr))
                $filter_param['attr'] = $old_attr . '@' . $v['attr_id'] . '_' . $v['attr_value'];
            else
                $filter_param['attr'] = $v['attr_id'] . '_' . $v['attr_value'];

            $list_attr[$v['attr_id']]['attr_value'][] = array('key' => $v['attr_id'], 'val' => $v['attr_value'], 'attr_value' => $v['attr_value'], 'href' => U("Goods/$action", $filter_param, ''));
            //unset($filter_param['attr_id_'.$v['attr_id']]);
        }
        if ($mode == 1) return $list_attr;
        return array('status' => 1, 'msg' => '', 'result' => $list_attr);
    }

    /**
     * 商品收藏
     * @param $user_id|用户id
     * @param $goods_id|商品id
     * @return array
     */
    public function collect_goods($user_id, $goods_id)
    {
        if(!is_numeric($user_id) || $user_id <= 0){
            return array('status'=>-1,'msg'=>'必须登录后才能收藏','result'=>array());
        }
        $collect_id = Db::name('goods_collect')->where("user_id", $user_id)->where("goods_id", $goods_id)->value('collect_id');
        if($collect_id){
            Db::name('goods')->where('goods_id', $goods_id)->setDec('collect_sum');
            Db::name('goods_collect')->where('collect_id',$collect_id)->delete();
            return array('status'=>2,'msg'=>'已取消收藏!','result'=>array());
        }else{
            Db::name('goods')->where('goods_id', $goods_id)->setInc('collect_sum');
            Db::name('goods_collect')->add(array('goods_id'=>$goods_id,'user_id'=>$user_id, 'add_time'=>time()));
            return array('status'=>1,'msg'=>'收藏成功!请到个人中心查看','result'=>array());
        }


    }
    /**
     * 这个方法已经废弃
     * 获取商品规格
     * @param $goods_id|商品id
     * @return array
     */
    public function get_spec($goods_id,$terminal="pc")
    {
        //商品规格 价钱 库存表 找出 所有 规格项id
        $keys = M('SpecGoodsPrice')->where("goods_id", $goods_id)->getField("GROUP_CONCAT(`key` ORDER BY store_count desc SEPARATOR '_') ");
        $filter_spec = array();
        if ($keys) {
            $specImage = M('SpecImage')->where(['goods_id'=>$goods_id,'src'=>['<>','']])->getField("spec_image_id,src");// 规格对应的 图片表， 例如颜色
            $keys = str_replace('_', ',', $keys);
            $sql = "SELECT a.name,a.order,b.* FROM __PREFIX__spec AS a INNER JOIN __PREFIX__spec_item AS b ON a.id = b.spec_id WHERE b.id IN($keys) ORDER BY a.order desc,b.order_index asc";
            $filter_spec2 = \think\Db::query($sql);
            foreach ($filter_spec2 as $key => $val) {
                if($terminal=="pc") {
                    $filter_spec[$val['name']][] = array(
                        'item_id' => $val['id'],
                        'item' => $val['item'],
                        'src' => $specImage[$val['id']],
                    );
                }else{
                    $filter_spec[$val['name']]["spec_name"] = $val['name'];
                    $filter_spec[$val['name']]["spec_value"][] = array(
                        'item_id' => $val['id'],
                        'item' => $val['item'],
                        'src' => $specImage[$val['id']],
                    );
                }
            }
        }
        if($terminal!="pc"){
            return array_values($filter_spec);
        }else{
            return $filter_spec;
        }
    }

    /**
     * 筛选的价格期间
     * @param $goods_id_arr|帅选的分类id
     * @param $filter_param
     * @param $action
     * @param int $c 分几段 默认分5 段
     * @return array
     */
    function get_filter_price($goods_id_arr, $filter_param, $action, $c = 5)
    {

        if (!empty($filter_param['price']))
            return array();

        $goods_id_str = implode(',', $goods_id_arr);
        $goods_id_str = $goods_id_str ? $goods_id_str : '0';
        $priceList = M('goods')->where("goods_id", "in", $goods_id_str)->getField('shop_price', true);  //where("goods_id in($goods_id_str)")->select();
        rsort($priceList);
        $max_price = (int)$priceList[0];

        $psize = ceil($max_price / $c); // 每一段累积的价钱
        $parr = array();
        for ($i = 0; $i < $c; $i++) {
            $start = $i * $psize;
            $end = $start + $psize;

            // 如果没有这个价格范围的商品则不列出来
            $in = false;
            foreach ($priceList as $k => $v) {
                if ($v > $start && $v < $end)
                    $in = true;
            }
            if ($in == false)
                continue;

            $filter_param['price'] = "{$start}-{$end}";
            if ($i == 0)
                $parr[] = array('value' => "{$end}元以下", 'href' => urldecode(U("Goods/$action", $filter_param, '')),'param'=>$filter_param['price']);
            elseif($i == ($c-1) && ($max_price > $end))
                $parr[] = array('value' => "{$end}元以上", 'href' => urldecode(U("Goods/$action", $filter_param, '')),'param'=>$filter_param['price']);
            else
                $parr[] = array('value' => "{$start}-{$end}元", 'href' => urldecode(U("Goods/$action", $filter_param, '')),'param'=>$filter_param['price']);
        }
        return $parr;
    }

    /**
     * 筛选条件菜单
     * @param $filter_param
     * @param $action
     * @return array
     */
    function get_filter_menu($filter_param, $action)
    {
        $menu_list = array();
        // 品牌
        if (!empty($filter_param['brand_id'])) {
            $brand_list = M('brand')->getField('id,name');
            $brand_id = explode('_', $filter_param['brand_id']);
            $brand['text'] = "品牌:";
            foreach ($brand_id as $k => $v) {
                $brand['text'] .= $brand_list[$v] . ',';
            }
            $brand['text'] = substr($brand['text'], 0, -1);
            $tmp = $filter_param;
            unset($tmp['brand_id']); // 当前的参数不再带入
            $brand['href'] = urldecode(U("Goods/$action", $tmp, ''));
            $menu_list[] = $brand;
        }
        // 规格
        if (!empty($filter_param['spec'])) {
            $spec = M('spec')->getField('id,name');
            $spec_item = M('spec_item')->getField('id,item');
            $spec_group = explode('@', $filter_param['spec']);
            foreach ($spec_group as $k => $v) {
                $spec_group2 = explode('_', $v);
                $spec_menu['text'] = $spec[$spec_group2[0]] . ':';
                array_shift($spec_group2); // 弹出第一个规格名称
                foreach ($spec_group2 as $k2 => $v2) {
                    $spec_menu['text'] .= $spec_item[$v2] . ',';
                }
                $spec_menu['text'] = substr($spec_menu['text'], 0, -1);

                $tmp = $spec_group;
                $tmp2 = $filter_param;
                unset($tmp[$k]);
                $tmp2['spec'] = implode('@', $tmp); // 当前的参数不再带入
                $spec_menu['href'] = urldecode(U("Goods/$action", $tmp2, ''));
                $menu_list[] = $spec_menu;
            }
        }
        // 属性
        if (!empty($filter_param['attr'])) {
            $goods_attribute = M('goods_attribute')->getField('attr_id,attr_name');
            $attr_group = explode('@', $filter_param['attr']);
            foreach ($attr_group as $k => $v) {
                $attr_group2 = explode('_', $v);
                $attr_menu['text'] = $goods_attribute[$attr_group2[0]] . ':';
                array_shift($attr_group2); // 弹出第一个规格名称
                foreach ($attr_group2 as $k2 => $v2) {
                    $attr_menu['text'] .= $v2 . ',';
                }
                $attr_menu['text'] = substr($attr_menu['text'], 0, -1);

                $tmp = $attr_group;
                $tmp2 = $filter_param;
                unset($tmp[$k]);
                $tmp2['attr'] = implode('@', $tmp); // 当前的参数不再带入
                $attr_menu['href'] = urldecode(U("Goods/$action", $tmp2, ''));
                $menu_list[] = $attr_menu;
            }
        }
        // 价格
        if (!empty($filter_param['price'])) {
            $price_menu['text'] = "价格:" . $filter_param['price'];
            unset($filter_param['price']);
            $price_menu['href'] = urldecode(U("Goods/$action", $filter_param, ''));
            $menu_list[] = $price_menu;
        }

        return $menu_list;
    }

    /**
     * 传入当前分类 如果当前是 2级 找一级
     * 如果当前是 3级 找2 级 和 一级
     * @param  $goodsCate
     */
    function get_goods_cate(&$goodsCate)
    {
        if (empty($goodsCate)) return array();
        $cateAll = get_goods_category_tree();
        if ($goodsCate['level'] == 1) {
            $cateArr = $cateAll[$goodsCate['id']]['tmenu'];
            $goodsCate['parent_name'] = $goodsCate['name'];
            $goodsCate['select_id'] = 0;
        } elseif ($goodsCate['level'] == 2) {
            $cateArr = $cateAll[$goodsCate['parent_id']]['tmenu'];
            $goodsCate['parent_name'] = $cateAll[$goodsCate['parent_id']]['name'];//顶级分类名称
            $goodsCate['open_id'] = $goodsCate['id'];//默认展开分类
            $goodsCate['select_id'] = 0;
        } else {
            $parent = M('GoodsCategory')->where("id", $goodsCate['parent_id'])->order('`sort_order` desc')->find();//父类
            $cateArr = $cateAll[$parent['parent_id']]['tmenu'];
            $goodsCate['parent_name'] = $cateAll[$parent['parent_id']]['name'];//顶级分类名称
            $goodsCate['open_id'] = $parent['id'];
            $goodsCate['select_id'] = $goodsCate['id'];//默认选中分类
        }
        return $cateArr;
    }

    /**
     * @param  $brand_id|帅选品牌id
     * @param  $price|帅选价格
     * @return array|mixed
     */
    function getGoodsIdByBrandPrice($brand_id, $price)
    {
        if (empty($brand_id) && empty($price))
            return array();
        $brand_select_goods=$price_select_goods=array();
        if ($brand_id) // 品牌查询
        {
            $brand_id_arr = explode('_', $brand_id);
            $brand_select_goods = M('goods')->whereIn('brand_id',$brand_id_arr,'or')->getField('goods_id', true);
        }
        if ($price)// 价格查询
        {
            $price = explode('-', $price);
            $price[0] = intval($price[0]);
            $price[1] = intval($price[1]);
            $price_where=" shop_price >= $price[0] and shop_price <= $price[1] ";
            $price_select_goods = M('goods')->where($price_where)->getField('goods_id', true);
        }
        if($brand_select_goods && $price_select_goods)
            $arr = array_intersect($brand_select_goods,$price_select_goods);
        else
            $arr = array_merge($brand_select_goods,$price_select_goods);
        return $arr ? $arr : array();
    }

    /**
     * 根据规格 查找 商品id
     * @param $spec|规格
     * @return array|\type
     */
    function getGoodsIdBySpec($spec)
    {
        if (empty($spec))
            return array();

        $spec_group = explode('@', $spec);
        $where = " where 1=1 ";
        foreach ($spec_group as $k => $v) {
            $spec_group2 = explode('_', $v);
            array_shift($spec_group2);
            $like = array();
            foreach ($spec_group2 as $k2 => $v2) {
                $v2 = addslashes($v2);
                $like[] = " key2  like '%\_$v2\_%' ";
            }
            $where .= " and (" . implode('or', $like) . ") ";
        }
        $sql = "select * from (select *,concat('_',`key`,'_') as key2 from __PREFIX__spec_goods_price as a) b  $where";
        $result = Db::query($sql);
        $arr = get_arr_column($result, 'goods_id');  // 只获取商品id 那一列
        return ($arr ? $arr : array_unique($arr));
    }

    /**
     * @param $attr|属性
     * @return array|mixed
     * 根据属性 查找 商品id
     * 59_直板_翻盖
     * 80_BT4.0_BT4.1
     */
    function getGoodsIdByAttr($attr)
    {
        if (empty($attr))
            return array();

        $attr_group = explode('@', $attr);
        $attr_id = $attr_value = array();
        foreach ($attr_group as $k => $v) {
            $attr_group2 = explode('_', $v);
            $attr_id[] = array_shift($attr_group2);
            $attr_value = array_merge($attr_value, $attr_group2);
        }
        $c = count($attr_id) - 1;
        if ($c > 0) {
            $arr = Db::name('goods_attr')
                ->where(['attr_id'=>['in',$attr_id],'attr_value'=>['in',$attr_value]])
                ->group('goods_id')
                ->having("count(goods_id) > $c")
                ->getField("goods_id", true);
        }else{
            $arr = M('goods_attr')
                ->where(['attr_id'=>['in',$attr_id],'attr_value'=>['in',$attr_value]])
                ->getField("goods_id", true); // 如果只有一个条件不再进行分组查询
        }
        return ($arr ? $arr : array_unique($arr));
    }

    /**
     * 获取地址
     * @return array
     */
    function getRegionList()
    {
        $res = S('getRegionList');
        if(!empty($res))
            return $res;
        $parent_region = M('region')->field('id,name')->where(array('level'=>1))->cache(true)->select();
        $ip_location = array();
        $city_location = array();
        foreach($parent_region as $key=>$val){
            $c = M('region')->field('id,name')->where(array('parent_id'=>$parent_region[$key]['id']))->order('id asc')->cache(true)->select();
            $ip_location[$parent_region[$key]['name']] = array('id'=>$parent_region[$key]['id'],'root'=>0,'djd'=>1,'c'=>$c[0]['id']);
            $city_location[$parent_region[$key]['id']] = $c;
        }
        $res = array(
            'ip_location'=>$ip_location,
            'city_location'=>$city_location
        );
        S('getRegionList',$res);
        return $res;
    }

    /**
     * 寻找Region_id的父级id
     * @param $cid
     * @return array
     */
    function getParentRegionList($cid){
        //$pids = '';
        $pids = array();
        $parent_id =  M('region')->where(array('id'=>$cid))->getField('parent_id');
        if($parent_id != 0){
            //$pids .= $parent_id;
            array_push($pids,$parent_id);
            $npids = $this->getParentRegionList($parent_id);
            if(!empty($npids)){
                //$pids .= ','.$npids;
                $pids = array_merge($pids,$npids);
            }

        }
        return $pids;
    }

    /**
     * 检查多个商品是否可配送
     * @param $goodsArr
     * @param $region_id
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function checkGoodsListShipping($goodsArr, $region_id)
    {
        $Goods = new Goods();
        $freightLogic = new FreightLogic();
        $freightLogic->setRegionId($region_id);
        $goods_ids = get_arr_column($goodsArr, 'goods_id');
        $goodsList = $Goods->field('goods_name,goods_id,template_id,is_free_shipping')->where('goods_id', 'IN', $goods_ids)->cache(true)->select();
        foreach ($goodsList as $goodsKey => $goodsVal) {
            $freightLogic->setGoodsModel($goodsVal);
            $goodsList[$goodsKey]['shipping_able'] = $freightLogic->checkShipping();
        }
        return $goodsList;
    }

    /**
     * 根据配送地址获取多个商品的运费
     * @param $goodsArr
     * @param $region_id
     * @return int
     */
    public function getFreight($goodsArr, $region_id)
    {
        $Goods = new Goods();
        $freightLogic = new FreightLogic();
        $freightLogic->setRegionId($region_id);
        $goods_ids = get_arr_column($goodsArr, 'goods_id');
        $goodsList = $Goods->field('goods_id,volume,weight,template_id,is_free_shipping,suppliers_id')->where('goods_id', 'IN', $goods_ids)->select();
        $goodsList = collection($goodsList)->toArray();
        foreach ($goodsArr as $cartKey => $cartVal) {
            foreach ($goodsList as $goodsKey => $goodsVal) {
                if ($cartVal['goods_id'] == $goodsVal['goods_id']) {
                    $goodsArr[$cartKey]['volume'] = $goodsVal['volume'];
                    $goodsArr[$cartKey]['weight'] = $goodsVal['weight'];
                    $goodsArr[$cartKey]['template_id'] = $goodsVal['template_id'];
                    $goodsArr[$cartKey]['is_free_shipping'] = $goodsVal['is_free_shipping'];
					$goodsArr[$cartKey]['suppliers_id'] = $goodsVal['suppliers_id'];
                }
            }
        }
		//按不同供应商分类，邮费分开计算
		$suppliersGoods = [];
		foreach ($goodsArr as $goodsKey => $goodsVal) {
            $suppliersGoods[$goodsVal['suppliers_id']][] = $goodsVal;
        }
		$freight = 0;
		foreach ($suppliersGoods as $suppliersGoodsKey => $suppliersGoodsVal) {
			$template_list = [];
			foreach ($suppliersGoodsVal as $goodsKey => $goodsVal) {
				$template_list[$goodsVal['template_id']][] = $goodsVal;
			}
			foreach ($template_list as $templateVal => $goodsArr) {
				$temp['template_id'] = $templateVal;
				foreach ($goodsArr as $goodsKey => $goodsVal) {
					$temp['total_volume'] += $goodsVal['volume'] * $goodsVal['goods_num'];
					$temp['total_weight'] += $goodsVal['weight'] * $goodsVal['goods_num'];
					$temp['goods_num'] += $goodsVal['goods_num'];
					$temp['is_free_shipping'] = $goodsVal['is_free_shipping'];
				}
				$freightLogic->setGoodsModel($temp);
				$freightLogic->setGoodsNum($temp['goods_num']);
				$freightLogic->doCalculation();
				$freight += $freightLogic->getFreight();
				unset($temp);
			}
		}
        
        return $freight;
    }


    /**
     *网站自营,入驻商家,货到付款,仅看有货,促销商品
     * @param $sel|筛选条件
     * @param array $cat_id|分类ID
     * @return mixed
     */
    function getFilterSelected($sel ,$cat_id=[]){
        $where =[];
        if($cat_id){
            $cat_ids = implode(',', $cat_id);
            $where ['cat_id']=['in',$cat_ids];
        }
        //促销商品
        if($sel == 'prom_type'){
            $where ['prom_type']= 3;
        }
        //看有货
        if($sel == 'store_count'){
            $where['store_count'] = ['gt',0];
        }
        //看包邮
        if($sel == 'free_post'){
            $where ['is_free_shipping']= 1;
        }
        //看全部
        if($sel == 'all'){
            $arr_id = Db::name('goods')->where($where)->getField('goods_id', true);
        }else{
            $arr_id = Db::name('goods')->where($where)->getField('goods_id', true);
        }
        return $arr_id;
    }

    /**
     * 用户浏览记录
     * @author lxl
     * @time  17-4-20
     */
    public function add_visit_log($user_id,$goods){
        $record = M('goods_visit')->where(array('user_id'=>$user_id,'goods_id'=>$goods['goods_id']))->find();
        if($record){
            M('goods_visit')->where(array('user_id'=>$user_id,'goods_id'=>$goods['goods_id']))->save(array('visittime'=>time()));
        }else{
            $visit = array('user_id'=>$user_id,'goods_id'=>$goods['goods_id'],'visittime'=>time(),'cat_id'=>$goods['cat_id'],'extend_cat_id'=>$goods['extend_cat_id']);
            M('goods_visit')->add($visit);
        }
    }

    /**
     * 在有价格阶梯的情况下，根据商品数量，获取商品价格
     * @param $goods_num|购买的商品数
     * @param $goods_price|商品默认单价
     * @param $price_ladder|价格阶梯数组
     * @return mixed
     */
    public function getGoodsPriceByLadder($goods_num, $goods_price, $price_ladder)
    {
        if (empty($price_ladder)) {
            return $goods_price;
        }
        $price_ladder = array_values(array_sort($price_ladder,'amount','asc'));
        $price_ladder_count = count($price_ladder);
        for ($i = 0; $i < $price_ladder_count; $i++) {
            if($i == 0 && $goods_num < $price_ladder[$i]['amount']){
                return $goods_price;
            }
            if($goods_num >= $price_ladder[$i]['amount'] && $goods_num < $price_ladder[$i+1]['amount']){
                return $price_ladder[$i]['price'];
            }
            if($i == ($price_ladder_count - 1)){
                return $price_ladder[$i]['price'];
            }
        }
    }


    /**
     * 是否收藏商品
     * @param type $user_id
     * @param type $goods_id
     * @return type
     */
    public function isCollectGoods($user_id, $goods_id)
    {
        $collect = M('goods_collect')->where(['user_id' => $user_id, 'goods_id' => $goods_id])->find();
        return $collect ? 1 : 0;
    }
    /**
     * 获取促销商品数据
     * @return mixed
     */
    public function getPromotionGoods()
    {
        $goods_where = array('g.is_on_sale' => 1);
        $promotion_goods = M('goods')
            ->alias('g')
            ->field('g.goods_id,g.goods_name,f.price AS shop_price,f.end_time')
            ->join('__FLASH_SALE__ f','g.goods_id = f.goods_id')
            ->where($goods_where)
            ->limit(3)
            ->select();
        return $promotion_goods;
    }

    /**
     * 获取商品好评率
     * @param $goods_id
     * @return float|int
     */
    public function getGoodsFavorableRate($goods_id){
        $all = Db::name('comment')->where('goods_id',$goods_id)->count();
        if(empty($all)) return 0;
        $ok = Db::name('comment')->where('goods_id',$goods_id)->where('goods_rank','gt',3)->count();
        return round($ok / $all,2);
    }

    /**
     * 获取精品商品数据
     * @return mixed
     */
    public function getRecommendGoods($p = 1)
    {
        $goods_where = array('a.is_recommend' => 1, 'a.is_on_sale' => 1);
//        $goods_where['goods_name'] = array("exp", " NOT REGEXP '华为|荣耀|小米|合约机|三星|魅族' ");//临时屏蔽,苹果APP审核过了之后注释
        $favourite_goods = M('goods')
            ->field('a.goods_id,a.goods_name,a.shop_price,a.cat_id,a.virtual_sales_sum,a.sales_sum,b.label_name')
            ->alias('a')
            ->join('__GOODS_LABEL__ b','a.label_id = b.label_id','left')
            ->where($goods_where)
            ->order('a.sort DESC')
            ->page($p, 10)
            ->cache(true, 3600)
            ->select();
        // 获取好评率 favorable_rate
        foreach($favourite_goods as $k=>$arr){
            $favourite_goods[$k]['favorable_rate'] = $this->getGoodsFavorableRate($arr['goods_id']);
        }
        return $favourite_goods;
    }

    /**
     * 获取新品商品数据
     * @return mixed
     */
    public function getNewGoods()
    {
        $goods_where = array('is_new' => 1,  'is_on_sale' => 1);
        $orderBy = array('sort' => 'desc');
        $new_goods = M('goods')
            ->field('goods_id,goods_name,shop_price')
            ->where($goods_where)
            ->order($orderBy)
            ->limit(9)
            ->select();
        return $new_goods;
    }

    /**
     * 获取热销商品数据
     * @return mixed
     */
    public function getHotGood($is_brand = 0)
    {
        $goods_where = array('is_hot' => 1,  'is_on_sale' => 1);
        if ($is_brand) {
            $goods_where['brand_id'] = ['<>', 0];
        }
        $orderBy = array('sort' => 'desc');
        $new_goods = M('goods')
            ->field('goods_id,goods_name,shop_price,market_price,is_virtual')
            ->where($goods_where)
            ->order($orderBy)
            ->limit(9)
            ->select();
        return $new_goods;
    }

    /**
     * 获取首页轮播图片
     * @return mixed
     */
    public function getHomeAdv()
    {
        $start_time = strtotime(date('Y-m-d H:00:00'));
        $end_time = strtotime(date('Y-m-d H:00:00'));
        $adv = M("ad")->field(array('ad_link','ad_name','ad_code'))
        ->where("pid=9 and enabled=1 and start_time< $start_time and end_time > $end_time")
        ->order("orderby desc")->cache(true,3600)
        ->limit(5)->select(); 
        //广告地址转换
        foreach($adv as $k=>$v){
            if(!strstr($v['ad_link'],'http')){
                $adv[$k]['ad_link'] = SITE_URL.$v['ad_link'];
            }
            $adv[$k]['ad_code'] = SITE_URL.$v['ad_code'];
        }
        return $adv;
    }
    
    /**
     * 获取首页轮播图片
     * @return mixed
     */
    public function getAppHomeAdv($isBanner=true)
    {
        $start_time = strtotime(date('Y-m-d H:00:00'));
        $end_time = strtotime(date('Y-m-d H:00:00'));
        if($isBanner){
            $where = array("pid"=>500);
        }else{
            $where = "pid > 500 AND pid < 520";
        }
    
        $adv = M("ad")->field(array('ad_link','ad_name','ad_code','media_type,pid'))
        ->where(" enabled=1 and start_time< $start_time and end_time > $end_time")->where($where)
        ->order("orderby desc")//->fetchSql(true)//->cache(true,3600)
        ->limit(20)->select();
         
        return $adv;
    }
    
    /**
     * 获取秒杀商品
     * @return mixed
     */
    public function getFlashSaleGoods($count, $page = 1, $start_time=0, $end_time=0)
    {
        //$where['f.status'] = 1;
        $where['f.start_time'] = array('egt', $start_time ?: time());
        if ($end_time) {
            $where['f.end_time'] = array('elt',$end_time);
        }
        $where['f.is_end'] = 0;
        $flash_sale_goods = M('flash_sale')->alias('f')
            ->field('f.end_time,f.buy_num,f.goods_name,f.price,f.goods_id,f.price,g.shop_price,f.item_id,100*(FORMAT(f.buy_num/f.goods_num,2)) as percent,buy_num')
            ->join('__GOODS__ g','g.goods_id = f.goods_id')
            ->where($where)
            //->order('f.start_time', 'asc')
            ->page($page, $count)
            ->select();
        return $flash_sale_goods;
    }
    
     /**
     * 找相似
     */
    public function getSimilar($goods_id, $p, $count)
    {
        $goods = M('goods')->field('cat_id')->where('goods_id', $goods_id)->find();
        if (empty($goods)) {
            return [];
        }

        $where = ['goods_id' => ['<>', $goods_id], 'cat_id' => $goods['cat_id']];
    	$goods_list = M('goods')->field("goods_id,goods_name,shop_price,is_virtual")
                ->where($where)->page($p, $count)->select();

    	return $goods_list;
    }
    
    /**
     * 积分商城
     */
    public function integralMall($rank, $user_id, $p = 1)
    {
        $ranktype = '';
        if ($rank == 'num') {
            $ranktype = 'sales_sum';//以兑换量（购买量）排序
        } elseif ($rank == 'integral') {
            $ranktype = 'exchange_integral';//以需要积分排序
        }
        //积分规则修改后的逻辑
        $point_rate = tpCache('integral.point_rate');
        //$point_rate = tpCache('shopping.point_rate');
        $goods_where['is_on_sale'] = 1;//是否上架
        $goods_where['is_virtual'] = 0;//是否虚拟商品
        //积分兑换筛选
        $exchange_integral_where_array = array(array('gt',0));
        //我能兑换
        if ($rank == 'exchange' && !empty($user_id)) {
            //获取用户积分
            $user_pay_points = intval(M('users')->where(array('user_id' => $user_id))->getField('pay_points'));
            if ($user_pay_points !== false) {
                array_push($exchange_integral_where_array, array('lt', $user_pay_points));
            }
        }
        $goods_where['exchange_integral'] =  $exchange_integral_where_array;  //拼装条件
        $goods_list_count = M('goods')->where($goods_where)->count();   //总数
        $goods_list = M('goods')->field('goods_id,goods_name,shop_price,market_price,exchange_integral,is_virtual')
                ->where($goods_where)->order($ranktype ,'desc')->page($p, 15)->select();
        
        $result = [
            'goods_list' => $goods_list,
            'goods_list_count' => $goods_list_count,
            'point_rate' => $point_rate,
        ];
        
        return $result;
    }
    
    /**
     *  获取排好序的品牌列表
     */
    function getSortBrands()
    {
        $brandList =  M("Brand")->select();
        $brandIdArr =  M("Brand")->where("name in (select `name` from `".C('database.prefix')."brand` group by name having COUNT(id) > 1)")->getField('id,cat_id');
        $goodsCategoryArr = M('goodsCategory')->where("level = 1")->getField('id,name');
        $nameList = array();
        foreach($brandList as $k => $v)
        {

            $name = getFirstCharter($v['name']) .'  --   '. $v['name']; // 前面加上拼音首字母

            if(array_key_exists($v[id],$brandIdArr) && $v['cat_id']) { // 如果有双重品牌的 则加上分类名称
                $name .= ' ( '. $goodsCategoryArr[$v['cat_id']] . ' ) ';
            }
            $nameList[] = $v['name'] = $name;
            $brandList[$k] = $v;
        }
        array_multisort($nameList,SORT_STRING,SORT_ASC,$brandList);

        return $brandList;
    }
    
    /**
     * 获取商品促销简单信息
     * @param array $goods
     * @param FlashSaleLogic|GroupBuyLogic|PromGoodsLogic $goodsPromLogic
     * @return array
     */
    public function getGoodsPromSimpleInfo($goods, $goodsPromLogic)
    {
        //prom_type: 0默认 1抢购 2团购 3优惠促销 4预售(不考虑)
        $activity['prom_type'] = 0;
    
        $goodsPromFactory = new \app\common\logic\GoodsPromFactory;
        if (!$goodsPromFactory->checkPromType($goods['prom_type'])
            || !$goodsPromLogic || !$goodsPromLogic->checkActivityIsAble()) {
                return $activity;
            }
    
            // 1抢购 2团购
            $prom = $goodsPromLogic->getPromModel()->getData();
            if (in_array($goods['prom_type'], [1, 2])) {
                $info = $goodsPromLogic->getActivityGoodsInfo();
                $activity = [
                    'prom_type' => $goods['prom_type'],
                    'prom_price' => $prom['price'],
                    'prom_store_count' => $info['store_count'],
                    'virtual_num' => $info['virtual_num']
                ];
                if($prom['start_time']){
                    $activity['prom_start_time'] = $prom['start_time'];
                }
                if($prom['end_time']) {
                    $activity['prom_end_time'] = $prom['end_time'];
                }
                return $activity;
            }
    
            // 3优惠促销
            // type:0直接打折,1减价优惠,2固定金额出售,3买就赠优惠券
            if ($prom['type'] == 0) {
                $expression = round($prom['expression']/10,2);
                $activityData[] = ['title' => '折扣', 'content' => "指定商品立打{$expression}折"];
            } elseif ($prom['type'] == 1) {
                $activityData[] = ['title' => '直减', 'content' => "指定商品立减{$prom['expression']}元"];
            } elseif ($prom['type'] == 2) {
                $activityData[] = ['title' => '促销', 'content' => "促销价{$prom['expression']}元"];
            } elseif ($prom['type'] == 3) {
                $couponLogic = new \app\common\logic\CouponLogic;
                $money = $couponLogic->getSendValidCouponMoney($prom['expression'], $goods['goods_id'], $goods['store_id'], $goods['cat_id']);
                if ($money !== false) {
                    $activityData[] = ['title' => '送券', 'content' => "买就送代金券{$money}元"];
                }
            }
            if ($activityData) {
                $activityInfo = $goodsPromLogic->getActivityGoodsInfo();
                $activity = [
                    'prom_type' => $goods['prom_type'],
                    'prom_price' => $activityInfo['shop_price'],
                    'data' => $activityData
                ];
                if($prom['start_time']){
                    $activity['prom_start_time'] = $prom['start_time'];
                }
                if($prom['end_time']) {
                    $activity['prom_end_time'] = $prom['end_time'];
                }
            }
    
            return $activity;
    }
    
    /**
     * 获取
     * @param type $user_level
     * @param type $cur_time
     * @param type $goods
     * @return string|array
     */
    public function getOrderPromSimpleInfo($goods)
    {
        $cur_time = time();
       
        $data = [];
        $po = M('prom_order')->where(['start_time' => ['<=', $cur_time], 'end_time' => ['>', $cur_time], 'is_close' => 0])->select();
        if (!empty($po)) {
            foreach ($po as $p) {
                //type:0满额打折,1满额优惠金额,2满额送积分,3满额送优惠券
                if ($p['type'] == 0) {
                    $data[] = ['title' => '折扣', 'content' => "满{$p['money']}元打{$p['expression']}折"];
                } elseif ($p['type'] == 1) {
                    $data[] = ['title' => '优惠', 'content' => "满{$p['money']}元优惠{$p['expression']}元"];
                } elseif ($p['type'] == 2) {
                    //积分暂不支持?
                } elseif ($p['type'] == 3) {
                    $couponLogic = new \app\common\logic\CouponLogic;
                    $money = $couponLogic->getSendValidCouponMoney($p['expression'], $goods['goods_id'],  $goods['cat_id']);
                    if ($money !== false) {
                        $data[] = ['title' => '送券', 'content' => "满{$p['money']}元送{$money}元优惠券"];
                    }
                }
            }
        }
    
        return $data;
    }

    /**
     *  获取排好序的分类列表
     */
    function getSortCategory()
    {
        $categoryList =  M("GoodsCategory")->getField('id,name,parent_id,level');
        $nameList = array();
        foreach($categoryList as $k => $v)
        {

            //$str_pad = str_pad('',($v[level] * 5),'-',STR_PAD_LEFT);
            $name = getFirstCharter($v['name']) .' '. $v['name']; // 前面加上拼音首字母
            //$name = getFirstCharter($v['name']) .' '. $v['name'].' '.$v['level']; // 前面加上拼音首字母
            /*
            // 找他老爸
            $parent_id = $v['parent_id'];
            if($parent_id)
                $name .= '--'.$categoryList[$parent_id]['name'];
            // 找他 爷爷
            $parent_id = $categoryList[$v['parent_id']]['parent_id'];
            if($parent_id)
                $name .= '--'.$categoryList[$parent_id]['name'];
            */
            $nameList[] = $v['name'] = $name;
            $categoryList[$k] = $v;
        }
        array_multisort($nameList,SORT_STRING,SORT_ASC,$categoryList);

        return $categoryList;
    }

    /**
     * 手机端 分类页面数据用
     * @param int $category
     * @return array
     */
    public function ajax_category($category=2){

        $list = [];
        if($category == 2){
            $list['category1'] = $this->get_category(1);
            $list['category2'] = $this->get_category(2);
            if(empty($list['category2'])){
                $list['category2'] = [];
            }
        }elseif($category == 3){
            // 只要一级分类
            $list['category1'] = $this->get_category(1);
            $ids_arr = get_arr_column($list['category1'],'id');
            $list['goods'] = D('goods')
                ->field('goods_id,goods_name,cat_id,sales_sum,original_img,shop_price')
                ->where('cat_id', 'in',$ids_arr)
                ->where('is_on_sale', 1)
                ->order('sort desc')->select();
            if(empty($list['goods'])){
                $list['goods'] = [];
            }
            foreach($list['goods'] as $k=>$v){
                if(empty($v['original_img']) or (strpos($v['original_img'],'http') !== 0 && !file_exists('.'.$v['original_img']))){
                    $list['goods'][$k]['original_img'] = '/public/images/icon_goods_thumb_empty_300.png';
                }
            }
        }

        $list['status'] = 1;
        return $list;
    }
    private function get_category($level=1){
        $list = Db::name('goods_category')
            ->field('id,mobile_name,parent_id,level,image,is_hot,commission_rate')
            ->where('level',$level)->where('is_show',1)
            ->order('sort_order desc')
            ->select();
        if($level != 1){
            foreach($list as $k=>$v){
                if(empty($v['image']) or (strpos($v['image'],'http') !== 0 && !file_exists('.'.$v['image']))){
                    $list[$k]['image'] = '/public/images/icon_goods_thumb_empty_300.png';
                }
            }
        }
        return $list;
    }


    /**
     *商品分享海报
     */
    public function getGoodsSharePoster($data){
        //0默认1抢购2团购3优惠促销4预售5虚拟(5其实没用)6拼团7搭配购8砍价
        switch ($data['prom_type']){
            case 1:
                $res = Db::name('flash_sale')->alias('f')->join('goods g','g.goods_id=f.goods_id')
                    ->where(['f.goods_id'=>$data['goods_id'],'f.item_id'=>$data['item_id']])
                    ->field('f.goods_id,f.item_id,f.goods_name,f.price,g.shop_price as pre_price')->find();
                $res && $res['desc'] = '限时抢购价';
                break;
            case 2:
                $res = Db::name('group_buy')->where(['goods_id'=>$data['goods_id'],'item_id'=>$data['item_id']])
                    ->field('goods_id,item_id,goods_name,price,goods_price as pre_price')->find();
                $res && $res['desc'] = '限时团购价';
                break;
            case 3:
                $res =  Db::name('prom_goods_item')->alias('i')->join('prom_goods g','i.prom_id=g.id')
                    ->where(['i.prom_id'=>$data['prom_id'],'i.goods_id'=>$data['goods_id'],'i.item_id'=>$data['item_id']])
                    ->field('i.goods_id,i.item_id,i.goods_name,i.price,g.type,g.expression')->find();
                $res = $this->parsePromData($res);
                break;
            case 6:
                $res = Db::name('team_goods_item')->alias('g')->join('team_activity t','g.team_id=t.team_id')
                    ->where(['g.goods_id'=>$data['goods_id'],'g.item_id'=>$data['item_id']])
                    ->field('g.goods_id,g.item_id,t.goods_name,g.team_price as price')->find();
                $res && $res['desc'] = '限时拼团价';
                $res['path'] = 'pages/team/team_info/team_info';
                break;
            case 8:
                $res = Db::name('promotion_bargain_goods_item')->alias('b')->join('goods g','g.goods_id=b.goods_id')
                    ->where(['b.goods_id'=>$data['goods_id'],'b.item_id'=>$data['item_id']])
                    ->field('b.goods_id,b.item_id,g.goods_name,b.end_price as price,b.start_price as pre_price')->find();
                $res && $res['desc'] = '限时砍价购';
                break;
            default:
                if($data['item_id']>0){
                    $res = Db::name('spec_goods_price')->alias('s')
                        ->join('goods g','s.goods_id=g.goods_id')->where('s.item_id',$data['item_id'])
                        ->field('s.goods_id,s.item_id,concat(g.goods_name,s.key_name) as goods_name,price')->find();
                }else{
                    $res = Db::name('goods')->where('goods_id',$data['goods_id'])
                        ->field('goods_id,0 as item_id,goods_name,shop_price as price')->find();
                }
                $res['path'] = 'pages/goods/goodsInfo/goodsInfo';
                $res && $res['desc'] = '商城惊喜价';
                break;
        }

        if(!$res){
            if($data['item_id']>0){
                $res = Db::name('spec_goods_price')->alias('s')
                    ->join('goods g','s.goods_id=g.goods_id')->where('s.item_id',$data['item_id'])
                    ->field('s.goods_id,s.item_id,concat(g.goods_name,s.key_name) as goods_name,price')->find();
            }else{
                $res = Db::name('goods')->where('goods_id',$data['goods_id'])
                    ->field('goods_id,0 as item_id,goods_name,shop_price as price')->find();
            }
            $res['path'] = 'pages/goods/goodsInfo/goodsInfo';
            $res && $res['desc'] = '商城惊喜价';
        }

        if(!$res){
            ajaxReturn(array('status'=>0,'msg'=>'获取失败'));
        }
        $res['first_leader'] = $data['first_leader'];
        $this->exportGoodsSharePoster($res);
    }

    private function parsePromData($data){
        if(!$data){
            ajaxReturn(array('status'=>0,'msg'=>'获取失败'));
        }
        switch ($data['type']){
            case  0: //打折
                $data['pre_price'] = $data['price'];
                $data['price'] = $data['price']* $data['expression'] /100;
                break;
            case 1:  //减价
                $data['pre_price'] = $data['price'];
                $data['price'] =  $data['price']-$data['expression'];
                break;
            case  2: //固定金额
                $data['pre_price'] = $data['price'];
                $data['price'] =  $data['expression'];
                break;
            //赠送代金券暂不处理
        }
        $data['desc'] = '限时优惠价';
        return $data;
    }

    /**
     * 生成商品分享海报（直接输出）
     */
    private function exportGoodsSharePoster($data){
        header("content-type: image/png");
        $goods_path = ROOT_PATH.goods_thum_images($data['goods_id'], 500, 500,0);
        $img_type = getimagesize($goods_path);
        $img_func = 'imagecreatefrom'.image_type_to_extension($img_type[2],false);
        $goods_img = $img_func($goods_path);
        $wxacode = imagecreatefromstring($this->getGoodsWxacode($data));
        $back_img = imagecreatetruecolor(540,960);  //创建底图
        $white = imagecolorallocate($back_img , 255, 255, 255);
        imagefill($back_img , 0, 0, $white);
        imagecopyresampled($back_img,$goods_img,20,40,0,0,500,500,imagesx($goods_img),imagesy($goods_img));
        imagecopyresampled($back_img,$wxacode,290,700,0,0,220,220,imagesx($wxacode),imagesy($wxacode));
        $black = imagecolorallocate($back_img,0,0,0);
        $red = imagecolorallocate($back_img,236,81,81);
        $gray = imagecolorallocate($back_img,153,153,153);
        $white = imagecolorallocate($back_img,255,255,255);
        $font = ROOT_PATH.'/public/static/font/ztc.ttf';
        $content = "";
        $line = 0;
        $letter = [];
        for ($i=0;$i<mb_strlen($data['goods_name']);$i++) {
            $letter[] = mb_substr($data['goods_name'], $i, 1);
        }
        foreach ($letter as $val) {
            $str = $content.$val;
            $box = imagettfbbox(28, 0, $font, $str);
            // 判断拼接后的字符串是否超过预设的宽度
            if (($box[2] > 500) && ($content !== "")) {
                $content .= "\n";
                $line++;
            }
            if($line<2){
                $content .= $val;
            }
        }
        imagettftext($back_img,28,0,20,590,$black,$font,$content);
        if(isset($data['desc'])){
            $box2 = imagettfbbox(16, 0, $font, $data['desc']); //活动标题
            //ajaxReturn($box2);
            imagefilledrectangle($back_img,30,680,30+$box2[2],704, $red);
            imagettftext($back_img,16,0,30,700,$white,$font,$data['desc']);
        }
        imagettftext($back_img,35,0,20,750,$red,$font,'￥'.sprintf("%.2f",$data['price']));
        isset($data['pre_price']) && imagettftext($back_img,16,0,30,776,$gray,$font,'原价:￥'.sprintf("%.2f",$data['pre_price']));
        //imageline($back_img,20,760,520,760,$gray);
        imagettftext($back_img,20,0,20,850,imagecolorallocate($back_img,50,50,50),$font,'扫描或长按小程序码');
        imagettftext($back_img,14,0,20,890,$gray,$font,tpCache('shop_info')['store_name']);
        imagepng($back_img);
        imagedestroy($goods_img);
        imagedestroy($back_img);
        imagedestroy($wxacode);
        exit();
    }


    /**
     * 获取商品分享小程序码
     */
    private function getGoodsWxacode($data){
        if(isset($data['path'])){
            $path=$data['path'];
        }else {
            $path='pages/goods/goodsInfo/goodsInfo';
        }
        $post_data = json_encode(['page' => $path,'scene' =>'g='.$data['goods_id'].'&i='.$data['item_id'].'&l='.$data['first_leader'],]);
        $minapp = new \app\common\logic\wechat\MiniAppUtil();
        $assecc_token = $minapp->getMinAppAccessToken();
        if($assecc_token == false){
            ajaxReturn(['status'=>0,'msg'=>$minapp->getError()]);
        }
        $result = $minapp->getWXACodeUnlimit($assecc_token,$post_data);
        if($result == false){
            ajaxReturn(['status'=>0,'msg'=>$minapp->getError()]);
        }
        return $result;
    }
    
 



}