FROM php:8.3-apache

# Установка системных зависимостей
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libpq-dev \
    zip \
    unzip \
    libmagickwand-dev \
    && rm -rf /var/lib/apt/lists/*

# Установка PHP расширений
RUN docker-php-ext-install \
    pdo_mysql \
    pdo_pgsql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip

# Установка ImageMagick
RUN pecl install imagick && docker-php-ext-enable imagick

# Включение mod_rewrite для Apache
RUN a2enmod rewrite

# Установка Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Установка рабочей директории
WORKDIR /var/www/html

# Копирование всех файлов проекта
COPY . .

# Установка зависимостей PHP
RUN composer install --no-dev --optimize-autoloader

# Создание необходимых папок и установка прав
RUN mkdir -p runtime web/assets \
    && chown -R www-data:www-data runtime web/assets \
    && chmod -R 777 runtime/ \
    && chmod -R 777 web/assets/ \
    && chmod 755 yii

# Настройка Apache DocumentRoot и PHP
RUN sed -i 's!/var/www/html!/var/www/html/web!g' /etc/apache2/sites-available/000-default.conf \
    && echo '<Directory /var/www/html/web>\n\
    AllowOverride All\n\
    Require all granted\n\
    DirectoryIndex index.php index.html\n\
    </Directory>\n\
    \n\
    <FilesMatch "\.php$">\n\
    SetHandler application/x-httpd-php\n\
    </FilesMatch>' > /etc/apache2/conf-available/docker-php.conf \
    && a2enconf docker-php

# Изменение владельца файлов
RUN chown -R www-data:www-data /var/www/html

# Открытие порта
EXPOSE 80

# Запуск Apache
CMD ["apache2-foreground"]
