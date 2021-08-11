FROM ubuntu:18.04
MAINTAINER dongdong <jiadongdong@easycorp.ltd>

ENV DEBIAN_FRONTEND=noninteractive
RUN apt-get update && apt-get install -y nginx php7.2-fpm mariadb-server php7.2 php7.2-curl php7.2-gd php7.2-ldap \ 
    php7.2-mbstring php7.2-mysql php7.2-xml php7.2-zip php7.2-cli php7.2-json curl \
    vim unzip locales 

ENV LANG="en_US.UTF8"
ENV TZ=Asia/Shanghai
ENV MYSQL_ROOT_PASSWORD="123456"
RUN echo -e "LANG=\"en_US.UTF-8\"\nLANGUAGE=\"en_US:en\"" > /etc/default/locale && locale-gen en_US.UTF-8 && \
    ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo '$TZ' > /etc/timezone && \
    rm -rf /etc/nginx/sites-enabled/default

COPY docker_init.sh /.docker_init.sh
COPY config/vimrc /root/.vimrc
COPY config/ioncube_loader_lin_7.2.so /usr/lib/php/20170718/ioncube_loader_lin_7.2.so
COPY config/00-ioncube.ini /etc/php/7.2/fpm/conf.d/
COPY config/00-ioncube.ini /etc/php/7.2/cli/conf.d/

COPY config/mcrypt.so /usr/lib/php/20170718/
COPY config/20-mcrypt.ini /etc/php/7.2/fpm/conf.d/
COPY config/20-mcrypt.ini /etc/php/7.2/cli/conf.d/
COPY config/libmcrypt.so.4.4.8 /usr/lib/libmcrypt.so.4

COPY config/nginx.conf /etc/nginx/sites-enabled/nginx.conf

RUN echo 'user = www-data \n\
group = www-data\n\
listen=127.0.0.1:9000\n'\
>> /etc/php/7.2/fpm/php-fpm.conf && \
     mkdir -p /run/php && \
     chmod a+x /.docker_init.sh && rm -rf /var/lib/mysql/* && \
     sed -i '/^memory_limit/cmemory_limit = 1024M' /etc/php/7.2/fpm/php.ini

VOLUME /var/lib/mysql
ENTRYPOINT ["/.docker_init.sh"]
