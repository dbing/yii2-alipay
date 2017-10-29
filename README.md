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
"dbing/yii2-alipay": "~1.0.0"
```

composer.json.

使用
-----
要使用此扩展，只需在应用程序配置中添加以下代码:

```php
return [
    //....
    'components' => [
        'alipay' =>[
            'class'         =>'bing\alipay\Alipay',
            'partner'       =>'208812xxxxxxxxxx',                           //合作身份者id
            'seller_email'  =>'itbing@sina.cn',                             //收款支付宝账号
            'key'           =>'1cvr0ix35iyy7qbkgs3gwyxxxxxxxxxx',           //安全检验码，
            'return_url'    =>'http://www.test.com/index.php/order/return', //同步通知地址（注意：不能加?id=123这类自定义参数）
            'notify_url'    =>'http://www.test.com/index.php/order/notify', //异步通知地址（注意：同上且不能写成内网域如localhost）

        ]
    ],
];

```

获取单纯的支付链接:

```php
$payUrl = Yii::$app->alipay->payUrl(time() . rand(10000,99999),'必应商城订单',0.01,'买了一个栗子');
```


你也可以获取带有支付链接的A标签:

```php
$payUrl = Yii::$app->alipay
    ->compose('去支付','btn btn-default')
    ->payUrl(time() . 99999,'必应商城订单',0.01,'买了一头猪');
```

通知地址处理:
```php
// 验签
$result = Yii::$app->alipay->verifyReturn();
if($result)
{
    $get = Yii::$app->request->get();
    ...
    // 系统业务
    ...
    ...
    
}
```
异步地址处理:
```php
// 验签
$result = Yii::$app->alipay->verifyNotify();
if($result)
{
    $post = Yii::$app->request->post();
    ...
    // 系统业务
    ...
    ...    
}
```
---