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
 * Author: dyr
 * Date: 2016-08-23
 */

namespace app\common\logic;

use think\Model;

/**
 * Class
 * @package Home\Model
 */
class PageLogic extends Model
{

    /**
     * 分页
     * @param $result
     * @param $page
     * @return array
     */
    public static function getPage($result, $page)
    {
        $return = $result;   //保存原来的数据
        $result['page'] = $page;     //分页数据处理
        if ($result['page'] == 1 && $result[0] || $result['page'] > 1) {
            //分页显
            if ($result[0]) {
                return array('status' => 1, 'msg' => '获取成功', 'result' => $return);
            } else {
                return array('status' => 1, 'msg' => '已加载完毕', 'result' => array());
            }
        } else if (count($return) == 0) {
            return array('status' => 1, 'msg' => '还没有相关信息', 'result' => array());
        } else {
            return array('status' => -1, 'msg' => '获取失败');
        }
    }

}