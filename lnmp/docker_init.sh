#!/usr/bin/env bash
set -e

rm -rf /var/run/nginx.pid
chown -R mysql:mysql /var/lib/mysql/
/etc/init.d/php7.2-fpm restart
/etc/init.d/nginx restart

if [ "`ls -A /var/lib/mysql/`" = "" ]; then
  mysql_install_db --defaults-file=/etc/mysql/my.cnf
  /etc/init.d/mysql restart
  /usr/bin/mysqladmin -uroot password $MYSQL_ROOT_PASSWORD
else
  /etc/init.d/mysql restart
fi

touch /tmp/daemon.log
tail -f /tmp/daemon.log

