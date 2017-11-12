# yii2-alipay

PC端即时到账支付宝付款接口

安装
------------
安装此扩展的首选方法是通过  [composer](http://getcomposer.org/download/).

运行

```
php composer.phar require --prefer-dist dbing/yii2-alipay
```

或者 添加

```json
"dbing/yii2-alipay": "~1.1.0"
```

composer.json.

使用
------------
要使用此扩展，只需在应用程序配置中添加以下代码:

```php
return [
    //....
    'components' => [
        'alipay' =>[
            'class'         =>'bing\alipay\Payment',
            'partner'       =>'208812xxxxxxxxxx',                           //合作身份者id
            'seller_email'  =>'itbing@sina.cn',                             //收款支付宝账号
            'key'           =>'1cvr0ix35iyy7qbkgs3gwyxxxxxxxxxx',           //安全检验码，
            'return_url'    =>'http://www.demo.com/pay/return', //同步通知地址（注：不能加?id=123这类自定义参数）
            'notify_url'    =>'http://www.demo.com/pay/notify', //异步通知地址（注：不能加自定义参数且不能写成内网域如localhost）
            'refund_url'    =>'http://www.demo.com/pay/refund', //退款通知地址（注：不能加自定义参数且内网域如localhost）

        ]
    ],
];

```

获取单纯的支付链接:

```php
$payUrl = Yii::$app->alipay->payUrl(唯一订单号,订单名称,付款金额,订单描述,[自定义参数],[商品展示地址]);
eg:
$payUrl = Yii::$app->alipay->payUrl(time() . rand(10000,99999),'必应商城订单',0.01,'买了一个栗子');
```


你也可以获取带有支付链接的A标签:

```php
$payUrl = Yii::$app->alipay
    ->compose('去支付','btn btn-default')
    ->payUrl(time() . 99999,'必应商城订单',0.01,'买了一头猪');
```

同步地址处理:
```php
// 验签
if(Yii::$app->alipay->verifyReturn())
{
    $get = Yii::$app->request->get();
    if($get['trade_status'] == 'TRADE_SUCCESS' || $get['trade_status'] == 'TRADE_FINISHED')
    {
        //判断该笔订单是否在商户网站中已经做过处理
        //否，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
        //是，不执行商户的业务程序
        ...
        ...
        ...
    }
}
```

异步地址处理:
```php
// 验签
$result = Yii::$app->alipay->verifyNotify();
if($result)
{
    $post = Yii::$app->request->post();
    if($post['trade_status'] == 'TRADE_FINISHED')
    {
        //判断该笔订单是否在商户网站中已经做过处理
        //否，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
        //是，不执行商户的业务程序
        ...
        ...
        ...
        //注意：
        //退款日期超过可退款期限后（如三个月可退款），支付宝系统发送该交易状态通知
    }
    else if($post['trade_status'] == 'TRADE_SUCCESS')
    {
        ...
        ...
        ...        
        //注意：
        //付款完成后，支付宝系统发送该交易状态通知
    }

    echo "success";     //请不要修改或删除
}
else
{
    //验证失败
    echo "fail";
}
```

退款
------------
获取单纯的退款链接:

```php
$refundUrl = Yii::$app->alipay->refund(批次号序列号[3至24位],退款笔数,退款详细数据);

eg:
$refundUrl = Yii::$app->alipay->refund(0000001,1,'2017111221001104770549058379^0.01^协商退款');

注：退款详细数据=原付款支付宝交易号[trade_no]^退款总金额^退款理由
```

获取带有A标签和样式的退款方式:

```php
$refundUrl = Yii::$app->alipay->compose('退款','btn btn-success')->refund(0000002,1,'2017111221001104770549058379^0.01^协商退款');
```


退款异步通知处理:
```php
// 验签
$result = Yii::$app->alipay->refundNotify();
if($result){

    //——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
    
    //批次号
    $batch_no = $_POST['batch_no'];

    //批量退款数据中转账成功的笔数
    $success_num = $_POST['success_num'];

    //批量退款数据中的详细信息
    $result_details = $_POST['result_details'];

    //判断是否在商户网站中已做过这次通知的处理,然后再决定是否执行商户的业务程序
        
    echo "success";     //请不要修改或删除

    //调试用，写文本函数记录程序运行情况是否正常
    //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");

    //——请根据您的业务逻辑来编写程序（以上代码仅作参考）——
    
}
else {
    //验证失败
    echo "fail";

    //调试用，写文本函数记录程序运行情况是否正常
    //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
}

```

---