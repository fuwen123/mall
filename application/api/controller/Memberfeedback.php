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
 * 用户反馈控制器
 */
class Memberfeedback extends MobileMember
{
    public function _initialize()
    {
        parent::_initialize(); 
    }

    /**
     * 添加反馈
     */
    public function feedback_add()
    {
        $mbfeedback_model = model('mbfeedback');

        $param = array();
        $param['mbfb_content'] = input('post.feedback');
        $param['mbfb_type'] = $this->member_info['member_clienttype'];
        $param['mbfb_time'] = TIMESTAMP;
        $param['member_id'] = $this->member_info['member_id'];
        $param['member_name'] = $this->member_info['member_name'];

        $result = $mbfeedback_model->addMbfeedback($param);

        if ($result) {
            ds_json_encode(10000,'',1);
        }
        else {
            ds_json_encode(10001,lang('ds_common_op_succ'));
        }
    }
}