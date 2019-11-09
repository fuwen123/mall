<?php
/**
 * tpshop 微信支付插件
 * ============================================================================
 * 版权所有 2015-2027 深圳搜豹网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.tp-shop.cn
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用 .
 * 不允许对程序代码以任何形式任何目的的再发布。
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ============================================================================
 * Author: IT宇宙人
 * Date: 2015-09-09
 */

/**
 * 支付 逻辑定义
 * Class
 * @package Home\Payment
 */

class weixin
{
    /**
     * 析构流函数
     */
    public function  __construct($code=""){
        require_once("lib/WxPay.Api.php"); // 微信扫码支付demo 中的文件
        require_once("example/WxPay.NativePay.php");
        require_once("example/WxPay.JsApiPay.php");
        if(!$code){
            $code = 'weixin';
        }
        $paymentPlugin = M('Plugin')->where(['code'=>$code , 'type' => 'payment'])->find(); // 找到微信支付插件的配置
        $config_value = unserialize($paymentPlugin['config_value']); // 配置反序列化
        WxPayConfig::$appid = $config_value['appid']; // * APPID：绑定支付的APPID（必须配置，开户邮件中可查看）
        WxPayConfig::$mchid = $config_value['mchid']; // * MCHID：商户号（必须配置，开户邮件中可查看）
        WxPayConfig::$key = $config_value['key']; // KEY：商户支付密钥，参考开户邮件设置（必须配置，登录商户平台自行设置）
        WxPayConfig::$appsecret = empty($config_value['appsecret']) ? '' : $config_value['appsecret']; // 公众帐号secert（仅JSAPI支付的时候需要配置)，
        WxPayConfig::$app_type = $code;
		
		$wxConfig = new WxPayConfig();
        $ssl_path_arr = $wxConfig->getSslPaths();
        WxPayConfig::$apiclient_cert = $ssl_path_arr[$code]['ssl_cert'];
        WxPayConfig::$apiclient_key = $ssl_path_arr[$code]['ssl_key'];
    }
    /**
     * 生成支付代码
     * @param   array   $order      订单信息
     * @param   array   $config    支付方式信息
     */
    function get_code($order, $config)
    {
        $notify_url = SITE_URL . '/index.php/Home/Payment/notifyUrl/pay_code/weixin'; // 接收微信支付异步通知回调地址，通知url必须为直接可访问的url，不能携带参数。

        $input = new WxPayUnifiedOrder();
        $input->SetBody($config['body']); // 商品描述
        $input->SetAttach("weixin"); // 附加数据，在查询API和支付通知中原样返回，该字段主要用于商户携带订单的自定义数据
        $input->SetOut_trade_no($order['order_sn'] . time()); // 商户系统内部的订单号,32个字符内、可包含字母, 其他说明见商户订单号
        $input->SetTotal_fee($order['order_amount'] * 100); // 订单总金额，单位为分，详见支付金额
        $input->SetNotify_url($notify_url); // 接收微信支付异步通知回调地址，通知url必须为直接可访问的url，不能携带参数。
        $input->SetTrade_type("NATIVE"); // 交易类型   取值如下：JSAPI，NATIVE，APP，详细说明见参数规定    NATIVE--原生扫码支付
        $input->SetProduct_id("123456789"); // 商品ID trade_type=NATIVE，此参数必传。此id为二维码中包含的商品ID，商户自行定义。
        $notify = new NativePay();
        $result = $notify->GetPayUrl($input); // 获取生成二维码的地址
        if($result['return_code'] == 'FAIL'){
            echo json_encode($result,256);exit;
        }
        $url2 = $result["code_url"];
        if (empty($url2))
            return '没有获取到支付地址, 请检查支付配置' . print_r($result, true);
        return '<img alt="模式二扫码支付" src="/index.php?m=Home&c=Index&a=qr_code&data='.urlencode($url2).'" style="width:110px;height:110px;"/>';
    }
    /**
     * 服务器点对点响应操作给支付接口方调用
     *
     */
    function response()
    {
        require_once("example/notify.php");
        $notify = new PayNotifyCallBack();
        $notify->Handle(false);
    }

    /**
     * 页面跳转响应操作给支付接口方调用
     */
    function respond2()
    {
        // 微信扫码支付这里没有页面返回
    }

    function getJSAPI($order)
    {
        if(stripos($order['order_sn'],'recharge') !== false){
            $go_url = U('Mobile/User/recharge_list',array('type'=>'recharge'));
            if($order['level_id']>0){
                $back_url = U('Mobile/Distribut/become_distribut');
            }else{
                $back_url = U('Mobile/User/recharge',array('order_id'=>$order['order_id']));
            }
        }else{
            // $go_url = U('Mobile/Order/order_detail',array('id'=>$order['order_id']));
            $go_url = U('Mobile/Order/paySuccess',array('id'=>$order['order_id'])); 
            $back_url = U('Mobile/Cart/cart4',array('order_id'=>$order['order_id']));
        }
        //①、获取用户openid
        $tools = new JsApiPay();
        //$openId = $tools->GetOpenid();
        $openId = $_SESSION['openid'];
        //②、统一下单
        $input = new WxPayUnifiedOrder();
        $input->SetBody("支付订单：".$order['order_sn']);
        $input->SetAttach("weixin");
        $input->SetOut_trade_no($order['order_sn'].time());
        $input->SetTotal_fee($order['order_amount']*100);
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 600));
        $input->SetGoods_tag("tp_wx_pay");
        $input->SetNotify_url(SITE_URL.'/index.php/Home/Payment/notifyUrl/pay_code/weixin');
        $input->SetTrade_type("JSAPI");
        $input->SetOpenid($openId);
        $order2 = WxPayApi::unifiedOrder($input);
        //echo '<font color="#f00"><b>统一下单支付单信息</b></font><br/>';
        //printf_info($order);exit;
        $jsApiParameters = $tools->GetJsApiParameters($order2);
        if(tpCache("debug.wx_mp_pay_debug")){
            $is_alert ="alert(res.err_code+res.err_desc+res.err_msg);" ;
        }

        $html = <<<EOF
	<script type="text/javascript">
	//调用微信JS api 支付
	function jsApiCall()
	{
		WeixinJSBridge.invoke(
			'getBrandWCPayRequest',$jsApiParameters,
			function(res){
				//WeixinJSBridge.log(res.err_msg);
				 if(res.err_msg == "get_brand_wcpay_request:ok") {
				    location.href='$go_url';
				 }else{
				    $is_alert;
				 	//alert(res.err_code+res.err_desc+res.err_msg);
				    location.href='$back_url';
				 }
			}
		);
	}

	function callpay()
	{
		if (typeof WeixinJSBridge == "undefined"){
		    if( document.addEventListener ){
		        document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
		    }else if (document.attachEvent){
		        document.attachEvent('WeixinJSBridgeReady', jsApiCall);
		        document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
		    }
		}else{
		    jsApiCall();
		}
	}
	callpay();
	</script>
EOF;

        return $html;

    }
    // 微信提现批量转账
    function transfer($data){
        header("Content-type: text/html; charset=utf-8");
exit("请联系TPshop官网客服购买高级版支持此功能");
    }

    /**
     * 将一个数组转换为 XML 结构的字符串
     * @param array $arr 要转换的数组
     * @param int $level 节点层级, 1 为 Root.
     * @return string XML 结构的字符串
     */
    function array2xml($arr, $level = 1) {
        $s = ($level == 1 )? "<xml>" : '';
        foreach($arr as $tagname => $value) {
            if (is_numeric($tagname)) {
                $tagname = $value['TagName'];
                unset($value['TagName']);
            }
            if(!is_array($value)) {
                //$s .= "<{$tagname}>".(!is_numeric($value) ? '<![CDATA[' : '').$value.(!is_numeric($value) ? ']]>' : '')."</{$tagname}>";
                $s .= "<{$tagname}>". '<![CDATA['.$value. ']]>'."</{$tagname}>";
            } else {
                $s .= "<{$tagname}>" . $this->array2xml($value, $level + 1)."</{$tagname}>";
            }
        }
        $s = preg_replace("/([\x01-\x08\x0b-\x0c\x0e-\x1f])+/", ' ', $s);
        return $level == 1 ? $s."</xml>" : $s;
    }

    function http_post($url, $param, $wxchat) {
        $oCurl = curl_init();
        if (stripos($url, "https://") !== FALSE) {
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, FALSE);
        }
        if (is_string($param)) {
            $strPOST = $param;
        } else {
            $aPOST = array();
            foreach ($param as $key => $val) {
                $aPOST[] = $key . "=" . urlencode($val);
            }
            $strPOST = join("&", $aPOST);
        }
        curl_setopt($oCurl, CURLOPT_URL, $url);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($oCurl, CURLOPT_POST, true);
        curl_setopt($oCurl, CURLOPT_POSTFIELDS, $strPOST);
        if($wxchat){
            curl_setopt($oCurl,CURLOPT_SSLCERT,$wxchat['api_cert']);
            curl_setopt($oCurl,CURLOPT_SSLKEY,$wxchat['api_key']);
            curl_setopt($oCurl,CURLOPT_CAINFO,$wxchat['api_ca']);
        }
        $sContent = curl_exec($oCurl);

        $aStatus = curl_getinfo($oCurl);
        curl_close($oCurl);
        if (intval($aStatus["http_code"]) == 200) {
            return $sContent;
        } else {
            return false;
        }
    }

    // 微信订单退款原路退回
    public function payment_refund($data){
        header("Content-type: text/html; charset=utf-8");
exit("请联系TPshop官网客服购买高级版支持此功能");
    }

    //微信红包
    public function red_packets($data){
        $sender = $data['sender'];
        $wxchat['api_cert'] = ROOT_PATH.WxPayConfig::$apiclient_cert;
        $wxchat['api_key'] =  ROOT_PATH.WxPayConfig::$apiclient_key;
        $pay_params = array();
        $pay_params['wxappid'] = WxPayConfig::$appid;
        $pay_params['mch_id'] = WxPayConfig::$mchid;
        $pay_params['mch_billno'] = $pay_params['mch_id'] . date('YmdHis') . rand(1000, 9999);//组合成28位，根据官方开发文档，可以自行设置
        $pay_params['client_ip'] = $_SERVER['REMOTE_ADDR'];
        $pay_params['re_openid'] = $data['openid'];//接收红包openid
        $pay_params['total_amount'] = $data['total_amount']*100;
        $pay_params['total_num'] = 1;//发放给的人数
        $pay_params['send_name'] = $sender;
        $pay_params['nick_name'] = $sender;
        $pay_params['wishing'] = "佣金下发，感谢您加入".$sender;
        $pay_params['act_name'] = $sender.'的红包';
        $pay_params['remark'] = $sender . "红包";
        $pay_params['nonce_str'] = md5(time());

        foreach ($pay_params as $k => $v) {
            $tarr[] =$k.'='.$v;
        }
        sort($tarr);
        $sign = implode($tarr, '&');
        $sign .= '&key='.WxPayConfig::$key;//商户支付秘钥
        $pay_params['sign']=strtoupper(md5($sign));
        $wget = $this->array2xml($pay_params);
        $pay_url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/sendredpack';
        $res = $this->http_post($pay_url, $wget, $wxchat);
        if(!$res){
            return array('status'=>0, 'msg'=>"Can't connect the server" );
        }
        $content = simplexml_load_string($res, 'SimpleXMLElement', LIBXML_NOCDATA);

        if(strval($content->return_code) == 'FAIL'){
            return array('status'=>0, 'msg'=>strval($content->return_msg));
        }
        if(strval($content->result_code) == 'FAIL'){
            return array('status'=>0, 'msg'=>strval($content->err_code),'err_code_des'=>strval($content->err_code_des));
        }

        //判断发送成功需下面两个值都为SUCCESS
		if ($content->return_code == 'SUCCESS' && $content->result_code == 'SUCCESS') {
			return array('status'=>1, 'send_listid'=>strval($content->send_listid));
		}
        return false;
    }



}