UPMP PHP SDK

2013-02-05

==== 基本要求 ====

    1. PHP 5.x版本（php 4.x版本兼容性未测试，如有需要，可自行修改和测试）
    2. PHP 的 mbstring 或者 iconv 模块
    3. 如果需要后台交易和查询请求，必须有curl模块
        Ubuntu:
            sudo apt-get install php5_curl php5_mbstring
        Windows: 修改php.ini，去掉以下配置行首的分号
            ;extension=php_curl.dll 
            ;extension=php_mbstring.dll 
    修改完后记得重新启动Web Server(apache/nginx/ligttpd/iis等)
    注：可通过 <?php phpinfo(); ?> 来查看是否有对应的模块


==== 文件结构 ====

<php sdk>
  │
  ├ conf
  │  │
  │  └ upmp_config.php ┈┈┈┈┈┈基础配置文件
  │
  ├ examples
  │  │
  │  ├ purchase.php ┈┈┈┈┈┈┈ 订单推送请求接口实例文件
  │  │
  │  ├ query.php ┈┈┈┈┈┈┈┈┈交易信息查询接口实例文件
  │  │
  │  ├ refund.php ┈┈┈┈┈┈┈┈ 退货接口实例文件
  │  │
  │  └ void.php ┈┈┈┈┈┈┈┈┈ 消费撤销接口实例文件
  │
  ├ lib
  │  │
  │  ├ upmp_core.php ┈┈┈┈┈┈┈公用函数文件
  │  │
  │  └ upmp_service.php ┈┈┈┈┈ 接口处理核心类
  │
  ├ notify_url.php ┈┈┈┈┈┈┈┈服务器异步通知页面文件
  │
  └ readme.txt ┈┈┈┈┈┈┈┈┈┈使用说明文本


※注意※
请修改配置文件：conf/upmp_config.php

本代码示例中获取远程HTTP信息使用的是curl模块。
如果您不想使用该方式实现获取远程HTTP功能，可用其他方式替代，此时需您自行编写代码。
