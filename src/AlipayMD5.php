<?php
/* *
 * MD5
 * 详细：MD5加密
 * 版本：3.3
 * 日期：2012-07-19
 * 说明：该代码仅供学习和研究支付宝接口使用，只是提供一个参考。
 *
 */

namespace bing\alipay;
class AlipayMd5 {

    /**
     * 签名字符串
     *
     * @param $prestr String 需要签名的字符串
     * @param $key String 私钥
     * @return String 签名结果
     */
    public static function md5Sign($prestr, $key) {
        $prestr = $prestr . $key;
        return md5($prestr);
    }

    /**
     * 验证签名
     *
     * @param $prestr String 需要签名的字符串
     * @param $sign String 签名结果
     * @param $key String 私钥
     * @return String 签名结果
     */
    public static function md5Verify($prestr, $sign, $key) {
        $prestr = $prestr . $key;
        $mysgin = md5($prestr);

        if($mysgin == $sign) {
            return true;
        }
        else {
            return false;
        }
    }
}
