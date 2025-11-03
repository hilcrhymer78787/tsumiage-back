FROM php:8.2-fpm

# Composer インストール
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# 必要なパッケージのインストール
RUN apt-get update && apt-get install -y \
    git \
    zip \
    unzip \
    vim \
    libpq-dev \
    libzip-dev \
    && docker-php-ext-install pdo_mysql pdo_pgsql \
    # Redis 拡張を PECL でインストール
    && pecl install redis \
    && docker-php-ext-enable redis

# 作業ディレクトリを設定
WORKDIR /var/www/html
