<?php
/* config commands */
$commands = array();

$commands['pre'] = array();
$commands['pre'][] = "cp $package $dockerPath/zsite.zip";
if($type == 'lamp')
{
    $commands['pre'][] = "sed -i 's/DOCUMENT_ROOT/\/www\/$packageBasename\/www\//g' $dockerPath/config/apache.conf";
    if(isset($params['domain'])) $commands['pre'][] = "sed -i '/DocumentRoot/i\\    ServerName {$params['domain']}' $dockerPath/config/apache.conf";

    if($ssl)
    {
        $commands['pre'][] = "sed -i 's/80/443/g' $dockerPath/config/apache.conf";
        $commands['pre'][] = "sed -i '/DocumentRoot /i\\    SSLEngine On' $dockerPath/config/apache.conf";
        $commands['pre'][] = "sed -i '/DocumentRoot /i\\    SSLCertificateFile /etc/certs/fullchain.pem' $dockerPath/config/apache.conf";
        $commands['pre'][] = "sed -i '/DocumentRoot /i\\    SSLCertificateKeyFile /etc/certs/privkey.pem' $dockerPath/config/apache.conf";
        $commands['pre'][] = "sed -i '/DocumentRoot /i\\    SSLCertificateChainFile /etc/certs/chain.pem' $dockerPath/config/apache.conf";
    }
}
else
{
    $commands['pre'][] = "sed -i 's/DOCUMENT_ROOT/\/www\/$packageBasename\/www\//g' $dockerPath/config/nginx.conf";
    if(isset($params['domain'])) 
    {
        $commands['pre'][] = "sed -i '/root /i\\    server_name {$params['domain']};' $dockerPath/config/nginx.conf";
    }
    else
    {
        $commands['pre'][] = "sed -i '/root /i\\    server_name _;' $dockerPath/config/nginx.conf";
    }

    if($ssl) 
    {
        $commands['pre'][] = "sed -i 's/80/443/g' $dockerPath/config/nginx.conf";
        $commands['pre'][] = "sed -i '/server_name/i\\    ssl on;' $dockerPath/config/nginx.conf";
        $commands['pre'][] = "sed -i '/server_name/i\\    ssl_certificate /etc/certs/fullchain.pem;' $dockerPath/config/nginx.conf";
        $commands['pre'][] = "sed -i '/server_name/i\\    ssl_certificate_key /etc/certs/privkey.pem;' $dockerPath/config/nginx.conf";
    }
}

$commands['Dockerfile'] = array();
$commands['Dockerfile'][] = "COPY zsite.zip /tmp/";
if($type == 'lamp') $commands['Dockerfile'][] = "COPY config/apache.conf /etc/apache2/sites-enabled/";
else $commands['Dockerfile'][] = "COPY config/nginx.conf /etc/nginx/sites-enabled/";
$commands['Dockerfile'][] = "RUN mkdir /www";
$commands['Dockerfile'][] = "RUN unzip /tmp/zsite.zip -d /var/www/ > /dev/null";
if($type == 'lamp') $commands['Dockerfile'][] = "RUN rm /etc/apache2/sites-enabled/000-default.conf";

$commands['Dockerfile'][] = "RUN rm /tmp/zsite.zip";

if($type == 'lamp' && $ssl) $commands['Dockerfile'][] = "RUN a2enmod ssl";

$commands['init'] = array();
$commands['init'][] = "if [ \"`ls -A /www/$packageBasename`\" = \"\" ]; then";
$commands['init'][] = "\ \ cp -a /var/www/$packageBasename/* /www/$packageBasename";
$commands['init'][] = "fi";
$commands['init'][] = "chmod -R 777 /www/$packageBasename/www/data";
$commands['init'][] = "chmod 777 /www/$packageBasename/www";
$commands['init'][] = "chown -R www-data:www-data /www/$packageBasename";
