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
 * Author: 当燃
 * Date: 2015-09-09
 */
function getMenuArr(){
    $menuArr = include APP_PATH.'supplier/conf/menu.php';
    $act_list = session('act_list');
    if($act_list != 'all' && !empty($act_list)){
        $right = M('system_menu')->where("id in ($act_list)")->cache(true)->getField('right',true);
        $role_right = '';
        foreach ($right as $val){
            $role_right .= $val.',';
        }
		$role_right = explode(',', $role_right);
        foreach($menuArr as $k=>$val){
            foreach ($val['child'] as $j=>$v){
                foreach ($v['child'] as $s=>$son){
                    if(!in_array($son['op'].'@'.$son['act'], $role_right)){
                        unset($menuArr[$k]['child'][$j]['child'][$s]);//过滤菜单
                    }
                }
            }
        }
        foreach ($menuArr as $mk=>$mr){
            foreach ($mr['child'] as $nk=>$nrr){
                if(empty($nrr['child'])){
                    unset($menuArr[$mk]['child'][$nk]);
                }
            }
        }
    }
    return $menuArr;
}

/**
 * 导出excel
 * @param $strTable	表格内容
 * @param $filename 文件名
 */
function downloadExcel($strTable,$filename)
{
	header("Content-type: application/vnd.ms-excel");
	header("Content-Type: application/force-download");
	header("Content-Disposition: attachment; filename=".$filename."_".date('Y-m-d').".xls");
	header('Expires:0');
	header('Pragma:public');
	echo '<html><meta http-equiv="Content-Type" content="text/html; charset=utf-8" />'.$strTable.'</html>';
}

/**
 * 根据id获取地区名字
 * @param $regionId id
 */
function getRegionName($regionId){
    $data = M('region')->where(array('id'=>$regionId))->field('name')->find();
    return $data['name'];
}