<?php
/* *
 * 支付宝接
 * 详细：该类是获取支付链接、同步通知和异步通知验证类
 * 版本：3.3
 * 日期：2012-07-19
 * 说明：该代码仅供学习和研究支付宝接口使用，只是提供一个参考。
 *
 */

namespace bing\alipay;

class Payment {

    /**
     * @var String 支付类型 ，默认1:商品购买(目前仅支持此类型)
     */
    const PAYMENT_TYPE = 1;

    /**
     * @var String 签名方式 不需修改(目前支持)
     */
    public $sign_type = 'MD5';

    /**
     * @var String 字符编码格式 目前支持 gbk 或 utf-8
     */
    public $input_charset = 'utf-8';

    /**
     * @var String ca证书路径地址，用于curl中ssl校验
     * 请保证cacert.pem文件在当前文件夹目录中
     */
    public $cacert = '\cacert.pem';

    /**
     * @var String 访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
     */
    public $transport = 'http';


    public $extra_common_param = '';

    /**
     * @var String 用于生产带有支付URL的a标签
     */
    public $compose;

    /**
     * 组装A标签
     *
     * @param string $link_name 标签名
     * @param string $class     类名
     * @param string $target    打开方式
     * @return $this
     */
    public function compose($link_name='去支付',$class='',$target='_blank')
    {
        $this->compose = '<a href="%HREF_VAL%" class="'.$class.'" target="'.$target.'">' . $link_name .'</a>';
        return $this;
    }

    /**
     * 获取支付链接 
     *
     * @param $out_trade_no String  商户订单号，商户网站订单系统中唯一订单号，必填
     * @param $subject String       订单名称
     * @param $total_fee String     付款金额
     * @param $body String          订单描述
     * @param $common_param         自定义参数
     * @param $show_url String      商品展示地址
     * @return String 支付表单
     */
    public function payUrl($out_trade_no, $subject, $total_fee, $body, $common_param='', $show_url='') {

        //支付类型
        $payment_type = self::PAYMENT_TYPE;

        //防钓鱼时间戳
        $anti_phishing_key = "";
        //若要使用请调用类文件submit中的query_timestamp函数

        //客户端的IP地址
        $exter_invoke_ip = "";
        //非局域网的外网IP地址，如：221.0.0.1

        //构造要请求的参数数组，无需改动
        $parameter = array(
            "service" => "create_direct_pay_by_user",
            "partner" => trim($this->partner),
            "seller_email" => trim($this->seller_email),
            "payment_type" => $payment_type,
            "notify_url" => $this->notify_url,
            "return_url" => $this->return_url,
            "out_trade_no" => $out_trade_no,
            "subject" => $subject,
            "total_fee" => $total_fee,
            "body" => $body,
            "show_url" => $show_url,
            "extra_common_param" => $common_param,
            "anti_phishing_key" => $anti_phishing_key,
            "exter_invoke_ip" => $exter_invoke_ip,
            "_input_charset" => trim(strtolower($this->input_charset))
        );

        //建立请求
        $alipaySubmit = new AlipaySubmit($this->bulidConfig());
        $payUrl = $alipaySubmit->buildRequestUrl($parameter);
        if(!empty($this->compose))
        {
            return str_replace('%HREF_VAL%', $payUrl, $this->compose);
        }
        return $payUrl;
    }

    /**
     * 生成退款链接
     *
     * @param string $batch_no      批次号序列号[3至24位]
     * @param string $batch_num     退款笔数
     * @param string $detail_data   退款详细数据
     * @return string 生成退款URL 
     */
    public function refund($batch_no,$batch_num,$detail_data)
    {
        //构造要请求的参数数组，无需改动
        $parameter = array(
            "service"       => "refund_fastpay_by_platform_pwd",
            "partner"       => trim($this->['partner']),
            "notify_url"    => $this->refund_url,
            "seller_email"  => trim($this->seller_email),
            "refund_date"   => date('Y-m-d H:i:s'),
            "batch_no"      => date('Y-m-d').$batch_no,
            "batch_num"     => $batch_num,
            "detail_data"   => $detail_data,
            "_input_charset"=> trim(strtolower($this->input_charset))
        );

        //建立请求
        $alipaySubmit = new AlipaySubmit($this->bulidConfig());
        $alipaySubmit->buildRefundUrl($parameter);

    }

    /**
     * 验证异步签名
     *
     * @return bool
     */
    public function verifyNotify() {
        $alipayNotify = new AlipayVerify($this->bulidConfig());
        $verify_result = $alipayNotify->verifyNotify();
        return $verify_result;
    }

    /**
     * 验证同步签名
     *
     * @return bool
     */
    public function verifyReturn() {
        $alipayNotify = new AlipayVerify($this->bulidConfig());
        $verify_result = $alipayNotify->verifyReturn();
        return $verify_result;
    }

    /**
     * 验证退款签名
     *
     * @return bool
     */
    public function refundNotify() {
        $alipayNotify = new AlipayVerify($this->bulidConfig());
        $refund_result = $alipayNotify->verifyNotify();
        return $refund_result;
    }


    private function bulidConfig() {
        //构造要请求的配置数组
        $alipay_config = array(
            'partner' => $this->partner,
            'seller_email' => $this->seller_email,
            'key' => $this->key,
            'sign_type' => $this->sign_type,
            'input_charset' => $this->input_charset,
            'cacert' => $this->cacert,
            'transport' => $this->transport,
        );
        return $alipay_config;
    }

}