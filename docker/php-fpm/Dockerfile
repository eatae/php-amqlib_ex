FROM php:7.4-fpm

RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libpq-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl \
    && usermod -u 1000 www-data

RUN apt-get clean && rm -rf /var/lib/apt/lists/*

RUN apt-get update && apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        # mbstring
        libonig-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd pdo pdo_pgsql bcmath

#intl
RUN apt-get install -y libicu-dev \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# xdebug
RUN pecl install xdebug-2.9.8 \
    && docker-php-ext-enable xdebug

#  NodeJS (with NPM), and Yarn
RUN curl -sL https://deb.nodesource.com/setup_15.x | bash -
RUN apt-get update \
 && apt-get install -y \
 nodejs
RUN npm install -g yarn

# add php.imi
ADD ./php.ini /usr/local/etc/php/php.ini

WORKDIR /var/www/php_rabbit
