FROM php:8.3-fpm

RUN apt-get update && apt-get install -y curl libcurl4-openssl-dev libmcrypt-dev libonig-dev

RUN docker-php-ext-install curl mbstring