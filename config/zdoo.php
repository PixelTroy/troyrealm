<?php
/* config commands */
$commands = array();

$commands['pre'] = array();
$commands['pre'][] = "cp $package $dockerPath/zdoo.zip";
$commands['pre'][] = "sed -i 's/DOCUMENT_ROOT/\/www\/$packageBasename\/www\//g' $dockerPath/config/apache.conf";
if(isset($params['domain'])) $commands['pre'][] = exec("sed -i '/DocumentRoot/i\\    ServerName {$params['domain']}' $dockerPath/config/apache.conf");

$commands['Dockerfile'] = array();
$commands['Dockerfile'][] = "COPY zdoo.zip /tmp/";
$commands['Dockerfile'][] = "COPY config/apache.conf /etc/apache2/sites-enabled/";
//$commands['Dockerfile'][] = "RUN mkdir /www";
//$commands['Dockerfile'][] = "RUN unzip /tmp/zdoo.zip -d /www/ > /dev/null";
$commands['Dockerfile'][] = "RUN unzip /tmp/zdoo.zip -d /var/www/ > /dev/null";
$commands['Dockerfile'][] = "RUN rm /etc/apache2/sites-enabled/000-default.conf";
$commands['Dockerfile'][] = "RUN rm /tmp/zdoo.zip";

$commands['init'] = array();
$commands['init'][] = "if [ \"`ls -A /www/$packageBasename`\" = \"\" ]; then";
$commands['init'][] = "\ \ cp -a /var/www/$packageBasename/* /www/$packageBasename";
$commands['init'][] = "fi";
$commands['init'][] = "chmod -R 777 /www/$packageBasename/www/data";
$commands['init'][] = "chmod -R 777 /www/$packageBasename/tmp";
$commands['init'][] = "chmod 777 /www/$packageBasename/www";
$commands['init'][] = "chmod 777 /www/$packageBasename/config";
$commands['init'][] = "chmod -R a+rx /www/$packageBasename/bin/*";
$commands['init'][] = "chown -R www-data:www-data /www/$packageBasename";
