<?php
namespace app\admin\validate;

use think\Validate;
use think\Db;

class MiniAppAudit extends Validate
{
    // 验证规则
    protected $rule = [
        'title' => 'require|max:32',
        'tag' => 'checkTag',
    ];
    //错误信息
    protected $message = [
        'title.require' => '小程序标题必填',
        'title.max' => '小程序标题长度不大于32个字符',
    ];

    protected function checkTag($value, $rule, $data)
    {
        $tags = explode(' ', $value);
        if (count($tags) > 10) {
            return '标签不能多于10个';
        }
        foreach ($tags as $tag) {
            if (count($tag) > 20) {
                return '标签长度不超过20';
            }
        }
        return true;
    }
}