FROM registry.docker.ir/php:5.6-apache

ENV DEBIAN_FRONTEND=noninteractive

EXPOSE 80

# Use archive.debian.org for Debian Stretch
RUN echo "deb [trusted=yes] http://archive.debian.org/debian stretch main" > /etc/apt/sources.list && \
    rm -f /etc/apt/sources.list.d/*

# Debian packages
RUN apt-get update && apt-get install -y --no-install-recommends \
    htop \
    wget \
    lynx \
    curl \
    tree \
    mc \
    vim \
    libmcrypt-dev \
    libicu-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libxml2-dev \
    mysql-client \
    unzip \
    git \
    locales \
    tzdata \
    graphicsmagick \
    imagemagick \
    libmagickwand-dev \
    msmtp \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Configure PHP to use msmtp as sendmail
RUN echo "sendmail_path = /usr/bin/msmtp -t" > /usr/local/etc/php/conf.d/mail.ini

# Locales
RUN printf 'en_GB.UTF-8 UTF-8\n' >> /etc/locale.gen \
    && printf 'en_US.UTF-8 UTF-8\n' >> /etc/locale.gen \
    && printf 'fa_IR.UTF-8 UTF-8\n' >> /etc/locale.gen \
    && locale-gen

ENV LANGUAGE=fa_IR.UTF-8
ENV LC_ALL=fa_IR.UTF-8
ENV LC_CTYPE=UTF-8
ENV LANG=fa_IR.UTF-8

# Localtime
ENV TZ='Asia/Tehran'
RUN echo $TZ > /etc/timezone \
    && rm /etc/localtime \
    && ln -snf /usr/share/zoneinfo/$TZ /etc/localtime \
    && dpkg-reconfigure -f noninteractive tzdata

# PHP modules
RUN docker-php-ext-install -j$(nproc) \
    mysqli \
    pdo \
    pdo_mysql \
    mbstring \
    mysql \
    iconv \
    mcrypt \
    intl \
    opcache \
    xml \
    soap \
    opcache \
    zip \
    exif \
    bcmath \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd

# PHP PECL modules
RUN pecl install xdebug-2.5.5 imagick \
    && docker-php-ext-enable xdebug imagick

# PHP ionCube
RUN curl -SL http://downloads3.ioncube.com/loader_downloads/ioncube_loaders_lin_x86-64.tar.gz | tar xzv --directory /tmp --strip-components=1 \
    && mv /tmp/ioncube_loader_lin_5.6.so /usr/local/lib/php/extensions/no-debug-non-zts-20131226/

# Apache modules
RUN a2enmod rewrite

# Apache rights
RUN usermod -o -u 1000 www-data

# ImageMagick
RUN ln -s /usr/lib/x86_64-linux-gnu/ImageMagick-6.8.9/bin-Q16/MagickWand-config /usr/bin/

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install php-amqplib
WORKDIR /var/www/html
RUN composer require php-amqplib/php-amqplib

# Install PEAR manually
RUN mkdir -p /tmp/pear && \
    curl -o /tmp/pear/install-pear-nozlib.phar https://pear.php.net/install-pear-nozlib.phar && \
    php /tmp/pear/install-pear-nozlib.phar && \
    rm -rf /tmp/pear

# Ensure pear binary is in PATH
ENV PATH="/usr/local/bin:${PATH}"

# Update PEAR channels and install Spreadsheet_Excel_Writer with its dependency
RUN pear channel-update pear.php.net && \
    pear install channel://pear.php.net/OLE-1.0.0RC3 && \
    pear install channel://pear.php.net/Spreadsheet_Excel_Writer-0.9.4

# Cleanup
RUN apt-get clean \
    && rm -rf /var/lib/apt/lists/* \
    && rm -rf /tmp/* \
    && rm -rf /var/tmp/*
