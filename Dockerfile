FROM php:8.0-apache

RUN docker-php-ext-install pdo_mysql mysqli
