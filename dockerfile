FROM ubuntu:22.04 AS base

LABEL maintainter="CHEC Dev"

ENV DEBIAN_FRONTEND=noninteractive
ENV TZ=UTC
ENV APPLICATION_ENV="local"

RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

RUN apt-get update \
    && apt-get install -y \
    && apt install -y software-properties-common ca-certificates lsb-release apt-transport-https \
    && LC_ALL=C.UTF-8 add-apt-repository ppa:ondrej/php \
    && apt update \
    && apt install -y php5.6 php5.6-cli php5.6-common php5.6-dev php5.6-curl php5.6-gd php5.6-imagick php5.6-imap \
    php5.6-intl php5.6-mbstring php5.6-mcrypt php5.6-memcache php5.6-mysql php5.6-opcache \
    php5.6-ps php5.6-pspell php5.6-readline php5.6-tidy php5.6-xml php5.6-xmlrpc php5.6-xsl php5.6-zip \ 
    php5.6-xdebug apache2 libapache2-mod-php5.6 \
    && apt install wget \
    && rm -rf /var/lib/apt/lists/* 

RUN echo "Mutex posixsem" >> /etc/apache2/apache2.conf
RUN a2enmod rewrite
RUN a2enmod ssl
RUN a2enmod lbmethod_byrequests

# WORKDIR /var/www/vhosts/epr
# COPY . htdocs

# WORKDIR /etc/apache2/sites-enabled
# COPY vhost.conf epr.conf

# CMD /usr/sbin/apache2ctl -D FOREGROUND



RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

# RUN docker-php-ext-install mysqli pdo pdo_mysql
# RUN apt-get install -y wget

RUN wget -O wkhtml.deb https://github.com/wkhtmltopdf/packaging/releases/download/0.12.6.1-2/wkhtmltox_0.12.6.1-2.jammy_amd64.deb
RUN dpkg -i --force-depends wkhtml.deb
RUN rm -fr wkhtml.deb

RUN mkdir /var/log/php 2>/dev/null || :
RUN chown www-data /var/log/php
RUN chown www-data /var/log/php
RUN ls /etc/php/5.6/apache2/php.ini
RUN sed -i "s/^display_errors = Off/display_errors = On/" /etc/php/5.6/apache2/php.ini
RUN sed -i "s/^display_startup_errors = Off/display_startup_errors = On/" /etc/php/5.6/apache2/php.ini
RUN sed -i "s|^;date.timezone =|date.timezone = ${TZ:-'Europe/London'}|" /etc/php/5.6/apache2/php.ini
RUN sed -i "s/;error_log = php_errors.log/error_log = \/var\/log\/php_errors.log/" /etc/php/5.6/apache2/php.ini
RUN sed -i "s/^display_errors = Off/display_errors = On/" /etc/php/5.6/cli/php.ini
RUN sed -i "s/^display_startup_errors = Off/display_startup_errors = On/" /etc/php/5.6/cli/php.ini
RUN sed -i "s/;error_log = php_errors.log/error_log = \/var\/log\/php_errors.log/" /etc/php/5.6/cli/php.ini
RUN sed -i "s|^;date.timezone =|date.timezone = ${TZ:-'Europe/London'}|" /etc/php/5.6/cli/php.ini

# Enable mod_rewrite for CodeIgniter
RUN a2enmod rewrite
RUN phpenmod mcrypt
RUN phpenmod imagick

RUN sed -i 's%<policy domain="coder" rights="none" pattern="PDF" />%<policy domain="coder" rights="read|write" pattern="PDF" />%' /etc/ImageMagick-6/policy.xml &>/dev/null || :
RUN sed -i 's%<policy domain="coder" rights="none" pattern="PDF" />%<policy domain="coder" rights="read|write" pattern="PDF" />%' /etc/ImageMagick/policy.xml &>/dev/null || :

RUN rm -rf /var/lib/apt/lists/* \
    && curl -sS https://getcomposer.org/installer -o /tmp/composer-setup.php \
    && php /tmp/composer-setup.php --install-dir=/usr/local/bin --filename=composer

# Copy the current directory contents into the container at /var/www/html
COPY . /var/www/html

# Set the working directory
WORKDIR /var/www/html

# Set the correct permissions for the Apache web server
RUN chown -R www-data:www-data /var/www/html

# Expose port 80
EXPOSE 80

# Restart Apache to apply changes
# CMD ["apache2-foreground"]

CMD /usr/sbin/apache2ctl -D FOREGROUND