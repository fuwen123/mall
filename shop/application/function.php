<?php
/**
 * 断点调试
 * @param  mix $data   数据
 * @return mix $reutrn 结果集
*/
function p($data='')
{
    echo '<pre>';
    var_dump($data);
    exit;
}