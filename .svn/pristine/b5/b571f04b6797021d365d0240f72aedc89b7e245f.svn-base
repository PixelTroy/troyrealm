<?php
$commands = array();
$commands['pre'] = array();
$commands['pre'][] = "cp $package $dockerPath/zentao.zip";
$commands['pre'][] = "sed -i 's/DOCUMENT_ROOT/\/www\/$packageBasename\/www\//g' $dockerPath/config/apache.conf";
if(isset($params['domain'])) $commands['pre'][] = "sed -i '/DocumentRoot/i\\    ServerName {$params['domain']}' $dockerPath/config/apache.conf";

$commands['Dockerfile'] = array();
$commands['Dockerfile'][] = "COPY zentao.zip /tmp/";
$commands['Dockerfile'][] = "COPY config/apache.conf /etc/apache2/sites-enabled/";
$commands['Dockerfile'][] = "RUN mkdir /www";
$commands['Dockerfile'][] = "RUN unzip -q /tmp/zentao.zip -d /var/www > /dev/null";
$commands['Dockerfile'][] = "RUN rm /etc/apache2/sites-enabled/000-default.conf";
$commands['Dockerfile'][] = "RUN rm /tmp/zentao.zip";

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


