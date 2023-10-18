FROM rockylinux:9
RUN dnf -y upgrade --refresh

# time
RUN dnf -y reinstall glibc-common
RUN localedef -v -c -i ja_JP -f UTF-8 ja_JP.UTF-8; echo "";

ENV LANG=ja_JP.UTF-8
RUN rm -f /etc/localtime
RUN ln -fs /usr/share/zoneinfo/Asia/Tokyo /etc/localtime

# other tool
RUN dnf install vim -y
RUN dnf install wget -y
RUN dnf install make -y
RUN dnf install which -y
RUN dnf install less -y
RUN dnf install telnet -y

# httpd
RUN dnf -y install httpd
COPY ./web.conf /etc/httpd/conf.d/
COPY ./00-mpm.conf /etc/httpd/conf.modules.d/

# php
RUN dnf -y install epel-release
RUN dnf -y install https://rpms.remirepo.net/enterprise/remi-release-9.rpm
RUN dnf -y module install php:remi-8.1
# exclude module : php-xmlrpc 
RUN dnf -y install php php-cli php-fpm php-curl php-mysqlnd php-gd php-opcache php-zip php-intl php-common php-bcmath php-imagick php-json php-readline php-redis php-mbstring php-apcu php-xml php-dom php-redis php-memcache -y

COPY ./php.ini /etc/

COPY ./composer.phar /usr/local/bin/composer
RUN chmod a+x /usr/local/bin/composer

WORKDIR /var/www/server

EXPOSE 80

COPY ./start_up.sh /start_up.sh
RUN chmod 744 /start_up.sh
CMD ["/start_up.sh"]