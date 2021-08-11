# 制作Zentao、Zsite、Zdoo的Docker镜像脚本
### 代码结构
* `config/`：包含不同产品的配置文件；
* `lamp/`：包含apache2、mysql、php所需的模板Dockerfile和启动脚本等文件；
* `lnmp/`：包含nginx、mysql、php所需的模板Dockerfile和启动脚本等文件；
* `tmp/`：脚本运行时生成的Dockerfile、docker_init.sh等文件；
* `builder.php`：脚本执行的入口文件。
### 脚本程序参数
###### 必须参数
* `dockerName`：制作镜像的名字；
* `tag`：制作镜像的标签；
* `package`：Zentao、Zsite或者Zdoo的源码zip包，如果有加密代码必须为php7.2版本；
* `配置文件目录`：脚本对应的产品线的配置文件；
###### 可选参数
* `type`：镜像中的服务类型，支持lamp和lnmp两种方式（默认：lamp）;
* `ssl`：是否开启https方式（true | false），默认false；
* `domain`：apache2或者nginx配置文件中执行的域名，默认不指定；
* `save`: 是否在制作完镜像后将镜像保存为归档文件，（true | false），默认false；
* `debug`：是否开启debug，打印配置参数，（0 | 1），默认0。
### 脚本例子
1. 制作zsite的lamp镜像
```shell
php builder.php dockerName=easysoft/zsite#tag=8.6.1#package=./zsite.8.6.1.php7.1.zip config/zsite.php
```
2. 制作zsite的lnmp并且配置ssl的镜像
```shell
php builder.php dockerName=easysoft/zsite#tag=8.6.1_nginx_ssl#type=lnmp#ssl=true#package=./zsite.8.6.1.php7.1.zip config/zsite.php
```
## 测试镜像
### 准备工作
1. 建空目录/www/
2. 如果想测试https的镜像，需要提前准备SSL证书文件放于某目录下，文件分别为fullchain.pem、privkey.pem、chain.pem。
### 启动容器
```shell
sudo docker run --name [容器名] -p [主机端口]:80 -v [主机代码目录]:/www/chanzhieps -v [主机mysql目录]:/var/lib/mysql -e MYSQL_ROOT_PASSWORD=[数据库密码] -d [镜像名]:[镜像标签]
```
* `容器名`：启动的容器名字，可随意指定；
* `主机端口`：主机端口为web访问端口；
* `主机代码目录`：必须指定，方便禅道代码、附件等数据的持久化，非升级情况需指定空目录；
* `主机mysql目录`：必须指定，方便禅道数据持久化，非升级情况需指定空目录；
* `数据库密码`： 容器内置mysql用户名为root,默认密码123456，如果不修改可以不指定该变量，如果想更改密码可以设置 MYSQL_ROOT_PASSWORD变量来更改密码；
* `镜像名`：镜像名字
* `镜像标签`：镜像的tag

例：
例1：```shell docker run --name zsite -p 8082:80 -v /www/test:/www/chanzhieps -v /www/mysqldata:/var/lib/mysql -e MYSQL_ROOT_PASSWORD=123456 -d easysoft/zsite:8.6.1```
例2：```shell docker run --name zsite -p 8082:443 -v /www/test:/www/chanzhieps -v /etc/certs:/etc/certs -v /www/mysqldata:/var/lib/mysql -e MYSQL_ROOT_PASSWORD=123456 -d easysoft/zsite:8.6.1_nginx_ssl```

### 访问站点
页面访问：http://[主机IP]:8082/
如果是https方式需要到zsite后台-》站点-》网络 设置为https

No problem.
