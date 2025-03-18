FROM php:8.4-fpm

ARG USER_ID=1000
ENV USER_NAME=www-data
ARG GROUP_NAME=www-data

RUN apt-get update && apt-get install -y \
    git \
    curl \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libmemcached-dev \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    librdkafka-dev \
    libpq-dev \
    openssh-server \
    zip \
    unzip

RUN docker-php-ext-install pdo pdo_pgsql zip gd mbstring exif pcntl

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && php -r "unlink('composer-setup.php');"

RUN curl -sL https://deb.nodesource.com/setup_20.x | bash -
RUN apt-get install -y nodejs

WORKDIR /app

COPY . .

RUN usermod -u ${USER_ID} ${USER_NAME}
RUN groupmod -g ${USER_ID} ${GROUP_NAME}
RUN chown -R ${USER_NAME}:${GROUP_NAME} /app && \
    chown -R ${USER_NAME}:${GROUP_NAME} /tmp && \
    chmod -R guo+w storage && \
    chmod -R guo+w bootstrap/cache

RUN composer install
RUN npm install
RUN npm run build

EXPOSE 9000

CMD ["php-fpm"]
