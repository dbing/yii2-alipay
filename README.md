# yii2-alipay

Alipay PC terminal instant payment payment interface(PC端即时到账支付宝付款接口)

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist dbing/yii2-alipay
```

or add

```json
"dbing/yii2-alipay": "~1.0.0"
```

to the require section of your composer.json.

Usage
-----

To use this extension,  simply add the following code in your application configuration:

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

You can get pay url link:

```php
$payUrl = Yii::$app->alipay->payUrl(time() . rand(10000,99999),'必应商城订单',0.01,'买了一个栗子');
```


You can also pay a link:

```php
$payUrl = Yii::$app->alipay
    ->compose('去支付','btn btn-default')
    ->payUrl(time() . 99999,'必应商城订单',0.01,'买了一头猪');
```

---

 