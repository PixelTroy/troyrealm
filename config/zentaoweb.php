<?php
/* config commands */
$commands = array();

$commands['pre'] = array();
$commands['pre'][] = "cp $package $dockerPath/zsite.zip";
$commands['pre'][] = "sed -i 's/DOCUMENT_ROOT/\/www\/$packageBasename\/www\//g' $dockerPath/config/apache.conf";
$commands['pre'][] = "sed -i 's/DOCUMENT_ROOT/\/www\/$packageBasename\/www\//g' $dockerPath/config/apache.conf";
$commands['pre'][] = "sed -i 's/mariadb-server //g' $dockerPath/Dockerfile";
if(isset($params['domain'])) $commands['pre'][] = "sed -i '/DocumentRoot/i\\    ServerName {$params['domain']}' $dockerPath/config/apache.conf";
$initCodes = <<<EOT
#!/usr/bin/env bash
set -e
rm -rf /var/run/apache2/apache2.pid
/etc/init.d/apache2 restart
touch /tmp/daemon.log
tail -f /tmp/daemon.log
EOT;
$commands['pre'][] = "echo '$initCodes' > $dockerPath/docker_init.sh";

$commands['Dockerfile'] = array();
$commands['Dockerfile'][] = "COPY zsite.zip /tmp/";
$commands['Dockerfile'][] = "COPY config/apache.conf /etc/apache2/sites-enabled/";
$commands['Dockerfile'][] = "RUN mkdir /www";
$commands['Dockerfile'][] = "RUN unzip /tmp/zsite.zip -d /www/ > /dev/null";
$commands['Dockerfile'][] = "RUN rm /etc/apache2/sites-enabled/000-default.conf";
$commands['Dockerfile'][] = "RUN rm /tmp/zsite.zip";

$commands['init'] = array();
$commands['init'][] = "chmod -R 777 /www/$packageBasename/www/data";
$commands['init'][] = "chmod 777 /www/$packageBasename/www";
$commands['init'][] = "chown -R www-data:www-data /www/$packageBasename";
