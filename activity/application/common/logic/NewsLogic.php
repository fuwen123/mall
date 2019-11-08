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
 * Author: xwy
 * Date: 2018-05-08
 */

namespace app\common\logic;

use app\common\model\News;
use app\common\model\NewsCat;
use think\Model;
use think\Db;

/**
 * Class
 * @package Home\Model
 */
class NewsLogic extends Model
{

    /**
     * 获取新闻列表
     * @param $data
     * @return array
     */
    public static function news_list($data)
    {
        $page = I('post.page/d', 1);//页数
        $limit = News::$LIMIT;//要显示的数量

        $list = M('news')
            ->alias('n')
            ->field("article_id,title,click,thumb,description,tags,link,FROM_UNIXTIME(publish_time,'%Y-%m-%d') as publish_time") //
            ->join('__NEWS_CAT__ cat', 'cat.cat_id = n.cat_id', 'LEFT')
            ->where(['is_open' => News::$STATUS_OPEN]) //'check_type' => News::$CHECK_PASS,
            ->limit(($page - 1) * $limit, $limit)
            ->order('publish_time desc')
            ->select();
        $data = PageLogic::getPage($list, $page);
        return $data;
    }
    /**
     * 获取新闻数据
     * @return mixed
     */
    public function moreNews($cat_id,$p = 1)
    {
        $limit = News::$LIMIT;//要显示的数量
        $where=$cat_id ? ['cat_id'=>$cat_id] :'';
        $list = M('news')
            ->field("article_id,title,click,thumb,description,tags,link,FROM_UNIXTIME(publish_time,'%Y-%m-%d') as publish_time") 
            ->where($where)
            ->where("check_type",1)
            ->limit(($p - 1) * $limit, $limit)
            ->order('publish_time desc')
            ->page($p, 10)
            ->select();
        return $list;
    }

    /**
     * 获取新闻详情
     * @param $data
     * @return array
     */
    public static function news_detail($data)
    {
        $list = M('news')
            ->alias('n')
            ->join('__NEWS_CAT__ cat', 'cat.cat_id = n.cat_id', 'LEFT')
            ->field('article_id,title,n.cat_id,click,thumb,description,tags,cat_name,publish_time,content')
            ->where(['is_open' => News::$OPEN_STATUS, 'article_id' => $data['id']]) //'open_type' => News::$OPEN_TYPE,
            ->find();
//      $list['addtime'] = date('Y-m-d',$list['addtime']);
        if ($list) {
            $list['content'] = htmlspecialchars_decode($list['content']);
            $list['time'] = date('Y-m-d H:i', $list['publish_time']);
        }
        if ($list) {
            return array('status' => 1, 'msg' => '操作成功', 'result' => $list);
        }
        return array('status' => 1, 'msg' => '操作成功', 'result' => array());

    }
    /**
     * 获取新闻数据
     * @return mixed
     */
    public function newsComment($cat_id,$p = 1)
    {
        $limit = News::$LIMIT;//要显示的数量
        $where=$cat_id ? ['cat_id'=>$cat_id] :'';
        $list = M('news')
            ->field("article_id,title,click,thumb,description,tags,link,FROM_UNIXTIME(publish_time,'%Y-%m-%d') as publish_time") 
            ->where($where)
            ->where("check_type",1)
            ->limit(($p - 1) * $limit, $limit)
            ->order('publish_time desc')
            ->page($p, 10)
            ->select();
        return $list;
    }
    
    /**
     *生成文章海报
     */
    public function getNewsSharePoster($article_id){
        header("content-type: image/png");
        $data = Db::name('news')->where('article_id',$article_id)->find();
        $article_path = ROOT_PATH.$data['thumb'];
        $img_type = getimagesize($article_path);
        $img_func = 'imagecreatefrom'.image_type_to_extension($img_type[2],false);
        $article_img = $img_func($article_path);
        $wxacode = imagecreatefromstring($this->getNewsWxacode($data));
   
        $back_img = imagecreatetruecolor(540,960);  //创建底图
        $white = imagecolorallocate($back_img , 255, 255, 255);
        imagefill($back_img , 0, 0, $white);
        imagecopyresampled($back_img,$article_img,20,40,0,0,500,500,imagesx($article_img),imagesy($article_img));
        imagecopyresampled($back_img,$wxacode,290,700,0,0,220,220,imagesx($wxacode),imagesy($wxacode));
        $black = imagecolorallocate($back_img,0,0,0);
        $red = imagecolorallocate($back_img,236,81,81);
        $gray = imagecolorallocate($back_img,153,153,153);
        $white = imagecolorallocate($back_img,255,255,255);
        $font = ROOT_PATH.'/public/static/font/ztc.ttf';
        $content = "";
        $line = 0;
        $letter = [];
        for ($i=0;$i<mb_strlen($data['title']);$i++) {
            $letter[] = mb_substr($data['title'], $i, 1);
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
        imagettftext($back_img,20,0,20,850,imagecolorallocate($back_img,50,50,50),$font,'扫描或长按小程序码');
        imagepng($back_img);
        imagedestroy($article_img);
        imagedestroy($back_img);
        imagedestroy($wxacode);
        exit();
    }
    
    /**
     * 获取商品分享小程序码
     */
    private function getNewsWxacode($data){    
        $path='pages/news/news_detail/news_detail';        
        $post_data = json_encode(['page' => $path,'scene' =>'g='.$data['article_id'].'&l='.$data['first_leader'],]);
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