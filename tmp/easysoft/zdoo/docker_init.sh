#!/usr/bin/env bash
set -e

if [ "`ls -A /www/zdoo`" = "" ]; then
  cp -a /var/www/zdoo/* /www/zdoo
fi
chmod -R 777 /www/zdoo/www/data
chmod -R 777 /www/zdoo/tmp
chmod 777 /www/zdoo/www
chmod 777 /www/zdoo/config
chmod -R a+rx /www/zdoo/bin/*
chown -R www-data:www-data /www/zdoo
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

