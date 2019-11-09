<?php
/**
 * tpshop
 * ============================================================================
 * 版权所有 2015-2027 深圳搜豹网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.tp-shop.cn
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用 .
 * 不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * Author: IT宇宙人
 * Date: 2015-09-09
 */

namespace app\common\model;


use think\Model;


class BespeakTemplate extends Model
{
    //自定义初始化
//    protected static function init()
//    {
//        //TODO:自定义的初始化
//    }
    public function BespeakTemplateUnit()
    {
        return $this->hasMany('BespeakTemplateUnit', 'template_id', 'template_id')->where(['deleted'=>0])->order('sort desc ,template_unit_id asc');
    }

    public function getWeekDayAttr($value, $data)
    {
        $arr = [];
        if ($data['monday']) {
            array_push($arr, '周一');
        }
        if ($data['tuesday']) {
            array_push($arr, '周二');
        }
        if ($data['wednesday']) {
            array_push($arr, '周三');
        }
        if ($data['thursday']) {
            array_push($arr, '周四');
        }
        if ($data['friday']) {
            array_push($arr, '周五');
        }

        if ($data['saturday']) {
            array_push($arr, '周六');
        }
        if ($data['sunday']) {
            array_push($arr, '周日');
        }
        $desc = implode('、', $arr);

        return $desc;
    }


    public function getAdminWeekDayAttr($value, $data)
    {
        $arr = [];
        $week_arr = [];
        if ($data['monday']) {
            array_push($arr, '周一');
        }
        if ($data['tuesday']) {
            array_push($arr, '周二');
        }
        if ($data['wednesday']) {
            array_push($arr, '周三');
        }
        if ($data['thursday']) {
            array_push($arr, '周四');
        }
        if ($data['friday']) {
            array_push($arr, '周五');
        }


        if ($data['saturday']) {
            array_push($week_arr, '周六');
        }
        if ($data['sunday']) {
            array_push($week_arr, '周日');
        }

        $desc = implode('、', $arr);
        $work_time = 'am('.$data['work_am_start_time'].'~'.$data['work_am_end_time'].'),pm('.$data['work_pm_start_time'].'~'.$data['work_pm_end_time'].')';
        $week_time = 'am('.$data['weekend_am_start_time'].'~'.$data['weekend_am_end_time'].'),pm('.$data['weekend_pm_start_time'].'~'.$data['weekend_pm_end_time'].')';
        $desc_week = implode('、', $week_arr);
        return $desc.$work_time.'。'.$desc_week.$week_time;
    }
    public function getWeekArrAttr($value, $data)
    {
        $arr = array('monday'=>$data['monday'], 'tuesday'=>$data['tuesday'], 'wednesday'=>$data['wednesday'], 'thursday'=>$data['thursday'], 'friday'=>$data['friday'], 'saturday'=>$data['saturday'], 'sunday'=>$data['sunday']);
        foreach ($arr as $k => $v) {
            $v_data['data'] = $k;
            $v_data['time'] = explode(',',$v);
            $res[$v][] = $v_data;
        }
        return $res;
    }


    public function getEquipmentArrAttr($value, $data)
    {
        $arr = explode(',',$data['equipment']);
        return $arr;
    }
}
