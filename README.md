<h1 align="center"> Logistics </h1>

<p align="center">简单便捷查询运单快递信息</p>

### 重构版本
1.0.4

### 支持查询接口平台

| 平台 | 次数 | 是否需要快递公司编码 |
| :-----: | :----: | :----: |
| [快递100](https://www.kuaidi100.com/openapi/applyapi.shtml) | 100单/天(免费) | Y |
| [快递鸟](http://www.kdniao.com/api-all) | 3000单/天(免费) | Y |
| [聚合数据](https://www.juhe.cn/docs/api/id/43) | 100次(首次申请) | Y |
| [极速数据](https://www.jisuapi.com/api/express) | 1000次(免费) | N |
| [数据智汇](http://www.shujuzhihui.cn/apiDetails?id=1867) | 100次(免费) | N |
| [爱查快递](https://www.ickd.cn/api) | 无限次(抓取接口, 无法保证数据正确性) | N |

### 配置须知
* 配置文件: Config.php, 修改私有属性的$config数组.
* 只有快递鸟申请后会有两个,一个是用户ID, 填入到app_secret,另外一个则是api_key, 填入app_key, 其他则把申请的key填入到app_key 

### 环境需求
* PHP >= 7.0

### 安装

```shell
$ composer require echozzy/logistics -vvv
```

### 使用
```php
use Zzy\Logistics\Logistics
$logistics = new Logistics()
```

### 参数说明

```
array query(string $code, $channels = ['kuaidi100'],$config=[], string $company = '')
array queryByProxy(array $proxy, string $code, $channels = ['kuaidi100'],$config=[], string $company = '')
```

* query 与 queryByProxy 返回数组结构是一样, 只是多了一个参数代理IP
* $proxy - 代理地址 结构: ['proxy' => '代理IP:代理端口']
* $code - 运单号
* $channel - 渠道名称, 可选参数,默认快递鸟.
* $config - 配置参数, 可选参数,默认Config.php自动获取.二维数组,key与channels的key一一对应
* $company - 快递公司 具体看 SupportLogistics 文件

### 快递 100 接口获取物流信息 所有接口返回格式是统一
```php
$logistics->query('12313131231', ''); // 第二参数不设,则默认快递鸟接口
$logistics->query('12313131231', 'kuaidi100');
$config = [
    ['app_key' => 'kuaidi100', 'app_secret' => 'kuaidi100', 'vip' => false]
];
$logistics->query('12313131231', ['kuaidi100'],$config);
```
示例:

```php 
[
   'kuaidi100' => [
       'channel' => 'kuaidi100',
       'status' => 'success',
       'result' => [
           [
               'status' => 200,
               'message'  => 'OK',
               'error_code' => 0,
               'data' => [
                   ['time' => '2019-01-09 12:11', 'description' => '仓库-已签收'],
                   ['time' => '2019-01-07 12:11', 'description' => '广东XX服务点'],
                   ['time' => '2019-01-06 12:11', 'description' => '广东XX转运中心']
               ],
               'logistics_company' => '申通快递',
               'logistics_bill_no' => '12312211'
           ],
           [
               'status' => 201,
               'message' => '快递公司参数异常：单号不存在或者已经过期',
               'error_code' => 0,
               'data' => '',
               'logistics_company' => '',
               'logistics_bill_no' => ''
           ]
       ]
   ]
]
```

### 多接口获取物流信息
```php
$logistics->query('12313131231');
$config = [
    ['app_key' => 'kuaidi100', 'app_secret' => 'kuaidi100', 'vip' => false],
    ['app_key' => 'ickd', 'app_secret' => 'ickd', 'vip' => false]
];
$logistics->query('12313131231', ['kuaidi100', 'ickd'],$config);
```
示例:

```php 
[
   'kuaidi100' => [
       'channel' => 'kuaidi100',
       'status' => 'success',
       'result' => [
           [
               'status' => 200,
               'message'  => 'OK',
               'error_code' => 0,
               'data' => [
                   ['time' => '2019-01-09 12:11', 'description' => '仓库-已签收'],
                   ['time' => '2019-01-07 12:11', 'description' => '广东XX服务点'],
                   ['time' => '2019-01-06 12:11', 'description' => '广东XX转运中心']
               ],
               'logistics_company' => '申通快递',
               'logistics_bill_no' => '12312211'
           ],
           [
               'status' => 201,
               'message' => '快递公司参数异常：单号不存在或者已经过期',
               'error_code' => 0,
               'data' => '',
               'logistics_company' => '',
               'logistics_bill_no' => ''
           ]
       ]
   ],
   'ickd' => [
       'channel' => 'ickd',
       'status' => 'success',
       'result' => [
           [
               'status' => 200,
               'message'  => 'OK',
               'error_code' => 0,
                'data' => [
                    ['time' => '2019-01-09 12:11', 'description' => '仓库-已签收'],
                    ['time' => '2019-01-07 12:11', 'description' => '广东XX服务点'],
                    ['time' => '2019-01-06 12:11', 'description' => '广东XX转运中心']
                ],
                'logistics_company' => '申通快递',
                'logistics_bill_no' => '12312211'
           ]
       ]
   ]
]
```

## 最后
欢迎提出 issue 和 pull request

## License
MIT
