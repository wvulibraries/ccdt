FROM php:7.3.12-fpm

WORKDIR /var/www
ADD src/project-css /var/www

# Fix debconf warnings upon build
ARG DEBIAN_FRONTEND=noninteractive

# Install dependencies
RUN apt-get update && apt-get install -y \
    build-essential \
    default-mysql-client \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    libzip-dev \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install extensions
RUN docker-php-ext-install pdo_mysql mbstring zip exif pcntl \
    && docker-php-ext-configure gd --with-gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ --with-png-dir=/usr/include/ \
    && docker-php-ext-install gd

# install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install nodejs LTS
ENV	VERSION='14'
RUN curl -sL https://deb.nodesource.com/setup_${VERSION}.x | bash - \
	&& apt -y install build-essential \
	&& apt -y install nodejs \
	&& npm i -g npm

RUN chown -R www-data:www-data \
        /var/www/storage \
        /var/www/bootstrap/cache

# set php
ADD ./config/php.ini /usr/local/etc/php/

EXPOSE 9000

ADD ./scripts/startup.sh /usr/bin/
RUN chmod -v +x /usr/bin/startup.sh
CMD ["/usr/bin/startup.sh"]