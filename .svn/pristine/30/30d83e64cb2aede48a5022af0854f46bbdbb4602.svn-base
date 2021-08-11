<?php
/* make an image of zentao 
* example: php zentaoBuilder.php dockerName=zentao#tag=v1.0#package=./zentao.zip
* example: php zentaoBuilder.php dockerName=easysoft/zentao#tag=12.3.3#package=./ZenTaoPMS.12.3.3.zip#domain=zentao.cc#save=true
*/
$baseRoot = dirname(__FILE__);
include $baseRoot . DIRECTORY_SEPARATOR . 'builder.php';

/* init: create base directory and copy lamp base files */
$builder    = new dockerBuilder($dockerName);
$dockerPath = $builder->dockerPath;

/* copy zentao product file */
exec("cp $package $dockerPath/zentao.zip");

/* deal with the zentao directory after unzip */
exec("unzip -l $package", $filesList);
$dirInfo = explode(" ", $filesList[3]);
$webDir  = rtrim(array_pop($dirInfo), '/');

/* modify apache conf */
exec("sed -i 's/DOCUMENT_ROOT/\/www\/$webDir\/www\//g' $dockerPath/config/apache.conf");
if(isset($params['domain'])) exec("sed -i '/DocumentRoot/i\\    ServerName {$params['domain']}' $dockerPath/config/apache.conf");

/* config commands */
$commands = array();
$commands['Dockerfile'] = array();
$commands['Dockerfile'][] = "COPY zentao.zip /tmp/";
$commands['Dockerfile'][] = "COPY config/apache.conf /etc/apache2/sites-enabled/";
$commands['Dockerfile'][] = "RUN mkdir /www";
$commands['Dockerfile'][] = "RUN unzip /tmp/zentao.zip -d /www/ > /dev/null";
$commands['Dockerfile'][] = "RUN rm /etc/apache2/sites-enabled/000-default.conf";
$commands['Dockerfile'][] = "RUN rm /tmp/zentao.zip";
$commands['init'] = array();
$commands['init'][] = "chmod -R 777 /www/$webDir/www/data";
$commands['init'][] = "chmod -R 777 /www/$webDir/tmp";
$commands['init'][] = "chmod 777 /www/$webDir/www";
$commands['init'][] = "chmod 777 /www/$webDir/config";
$commands['init'][] = "chmod -R a+rx /www/$webDir/bin/*";
$commands['init'][] = "chown -R www-data:www-data /www/$webDir";

/* build image */
$builder->build($dockerName, $tag, $commands, $save);
