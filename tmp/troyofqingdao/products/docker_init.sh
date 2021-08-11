#!/usr/bin/env bash
set -e

if [ "`ls -A /www/zentaoalm`" = "" ]; then
  cp -a /var/www/zentaoalm/* /www/zentaoalm
fi
chmod -R 777 /www/zentaoalm/www/data
chmod -R 777 /www/zentaoalm/tmp
chmod 777 /www/zentaoalm/www
chmod 777 /www/zentaoalm/config
chmod -R a+rx /www/zentaoalm/bin/*
chown -R www-data:www-data /www/zentaoalm
rm -rf /var/run/apache2/apache2.pid
chown -R mysql:mysql /var/lib/mysql/
/etc/init.d/apache2 restart

if [ "`ls -A /var/lib/mysql/`" = "" ]; then
  mysql_install_db --defaults-file=/etc/mysql/my.cnf
  /etc/init.d/mysql restart
  /usr/bin/mysqladmin -uroot password $MYSQL_ROOT_PASSWORD
else
  /etc/init.d/mysql restart
fi

touch /tmp/daemon.log
tail -f /tmp/daemon.log

