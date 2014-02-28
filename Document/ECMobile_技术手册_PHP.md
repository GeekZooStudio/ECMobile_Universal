#目录结构

<pre>
ecmobile/respond.php			接口回调文件
ecmobile/readme.txt				alipay文件说明
ecmobile/cacert.pem				alipay包
ecmobile/alipay.config.php		配置地址
ecmobile/key/public_key.pem		公钥
ecmobile/key/private_key.pem	密钥
</pre>

#后台配置

以Demo商城（http://shop.ecmobile.me）为例：

1. 下载源代码并解压
2. 将/ecmobile/目录，复制到ecshop网站的根目录
3. 对/ecmobile/目录，进行用户组及读写权限设置
4. 尝试在浏览器中访问 `http://shop.ecmobile.me/ecmobile`，将看到如下信息：

<pre>                                                                           
     _/_/_/                      _/        _/_/_/_/_/                     
  _/          _/_/      _/_/    _/  _/          _/      _/_/      _/_/    
 _/  _/_/  _/_/_/_/  _/_/_/_/  _/_/          _/      _/    _/  _/    _/   
_/    _/  _/        _/        _/  _/      _/        _/    _/  _/    _/    
 _/_/_/    _/_/_/    _/_/_/  _/    _/  _/_/_/_/_/    _/_/      _/_/       
                                                                        

Copyright 2013-2014, Geek Zoo Studio
</pre>

5. 完成后台配置

#客户端配置

1. 打开iOS/Android工程，将接口URL替换为http://<ecshop域名>/ecmobile/?url=
2. 以上面为例，即：http://shop.ecmobile.me/ecmobile/?url=
3. 打包安装，并进行测试

#联系方式

官方论坛：http://bbs.ecmobile.cn/    

QQ群1：329673575    
QQ群2：239571314    
QQ群3：347624547    