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
 * Author: 当燃
 * 专题管理
 * Date: 2016-03-09
 */

namespace app\admin\controller;

use app\common\model\BargainFirst;
use app\common\model\BargainList;
use app\common\logic\MessageFactory;
use think\Page;
use think\Loader;
use think\Db;

class PromotionBargain extends Base
{

    //砍价活动
    public function index()
    {
		header("Content-type: text/html; charset=utf-8");
exit("商业用途必须购买正版,使用盗版将追究法律责任");
    }

    /**
     * 发起者列表
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function bargain_first()
    {
		header("Content-type: text/html; charset=utf-8");
exit("商业用途必须购买正版,使用盗版将追究法律责任");
    }

    /**
     * 参与者列表
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function bargain_list()
    {
		header("Content-type: text/html; charset=utf-8");
exit("商业用途必须购买正版,使用盗版将追究法律责任");
    }


    /**
     * 创建和更新砍价
     * @return mixed
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function bargain_info()
    {
		header("Content-type: text/html; charset=utf-8");
exit("商业用途必须购买正版,使用盗版将追究法律责任");
    }

    /**
     * 添加活动发送消息
     */
    public function sendActivity($data)
    {
        adminLog("管理员添加抢购活动 " . $data['title']);
        if($data['mmt_message_switch'] == 1) {
            $goods_original_img = Db::name('goods')->where("goods_id", $data['goods_id'])->value('original_img');
            // 发送抢购活动通知消息
            $send_data = [
                'message_title' => $data['title'],
                'message_content' => $data['description'],
                'img_uri' => $goods_original_img,
                'end_time' => $data['end_time'],
                'mmt_code' => 'promotion_bargain_activity',
                'prom_type' => 8,
                'users' => [],
                'message_val' => [],
                'category' => 1,
                'prom_id' => $data['id']
            ];
            $messageFactory = new MessageFactory();
            $messageLogic = $messageFactory->makeModule($send_data);
            $messageLogic->sendMessage();
        }

    }

    public function bargain_del()
    {
        $id = I('del_id/d');
        $bargain_first = db('bargain_first')->where(['bargain_id'=>$id,'is_end'=>0])->find();
        if($bargain_first){ $this->ajaxReturn(['status'=>0,'msg'=>'该活动已存在订单，不能删除']);}
        if ($id) {
            $PromotionBargain = \app\common\model\PromotionBargain::get(['id'=>$id]);
            if($PromotionBargain['promotion_bargain_goods_item'][0]['item_id'] > 0){
                //有规格
                $item_ids = get_arr_column($PromotionBargain['promotion_bargain_goods_item'], 'item_id');
                $item_ids = array_unique($item_ids);
                db('spec_goods_price')->where(['item_id'=>['IN', $item_ids],'prom_id'=>$id,'prom_type'=>8])->save(['prom_type' => 0, 'prom_id' => 0]);
                $goodsPromCount = Db::name('spec_goods_price')->where(['goods_id'=>$PromotionBargain['goods_id']])->where('prom_type','>',0)->count('item_id');
                if($goodsPromCount == 0){
                    db('goods')->where("goods_id", $PromotionBargain['goods_id'])->save(['prom_type' => 0, 'prom_id' => 0]);
                }
            }else{
                db('goods')->where(["goods_id"=>$PromotionBargain['goods_id'], 'prom_id' => $id])->save(['prom_type' => 0, 'prom_id' => 0]);
            }
//            $spec_goods = Db::name('spec_goods_price')->where(['prom_type' => 8, 'prom_id' => $id])->find();
//            //有活动商品规格
//            if($spec_goods){
//                Db::name('spec_goods_price')->where(['prom_type' => 8, 'prom_id' => $id])->save(array('prom_id' => 0, 'prom_type' => 0));
//                //商品下的规格是否都没有活动
//                $goods_spec_num = Db::name('spec_goods_price')->where(['prom_type' => 8, 'goods_id' => $spec_goods['goods_id']])->find();
//                if(empty($goods_spec_num)){
//                    //商品下的规格都没有活动,把商品回复普通商品
//                    Db::name('goods')->where(['goods_id' => $spec_goods['goods_id']])->save(array('prom_id' => 0, 'prom_type' => 0));
//                }
//            }else{
//                //没有商品规格
//                Db::name('goods')->where(['prom_type' => 8, 'prom_id' => $id])->save(array('prom_id' => 0, 'prom_type' => 0));
//            }
            $PromotionBargain->save(['deleted'=>1]);
            db('promotion_bargain_goods_item')->where(['bargain_id' => $id])->update(['deleted'=>1]);
            // 删除砍价消息
            $messageFactory = new MessageFactory();
            $messageLogic = $messageFactory->makeModule(['category' => 1]);
            $messageLogic->deletedMessage($id, 1);
            $this->ajaxReturn(['status'=>1,'msg'=>'删除成功']);
        } else {
            $this->ajaxReturn(['status'=>0,'msg'=>'删除失败']);
        }
    }

    /**
     * 点击按钮关闭
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function change_prom_is_end()
    {
        $id = input('id/d');
        $PromotionBargain = new \app\common\model\PromotionBargain();
        $PromotionBargain = $PromotionBargain->find($id);
        if ($PromotionBargain['end_time'] < time()) {
            $this->ajaxReturn(['status'=>0,'msg'=>'该活动已经过期']);
        }
        $PromotionBargain['is_end'] == 0 ? $PromotionBargain['is_end'] = 1 : $PromotionBargain['is_end'] = 0;
        $PromotionBargain->save();
        clearCache();
        $this->ajaxReturn(['status'=>1,'msg'=>'成功']);
    }



}