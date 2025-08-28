# ใช้ภาพจาก PHP รุ่น 8.0 ที่ใช้ Apache
FROM php:8.0-apache

# เปิดใช้งาน mod_rewrite
RUN a2enmod rewrite

# ติดตั้ง Dependencies ที่จำเป็น
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    unzip \
    git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd \
    && docker-php-ext-install zip \
    && docker-php-ext-install mysqli \
    && docker-php-ext-install pdo pdo_mysql

# ติดตั้ง Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# ตั้งค่า directory สำหรับทำงาน
WORKDIR /var/www/html

# คัดลอกไฟล์ทั้งหมดใน directory ปัจจุบันไปยัง Docker container
COPY . /var/www/html

# เปิดใช้งานพอร์ต 80 สำหรับการเข้าถึงเว็บเซิร์ฟเวอร์
EXPOSE 80
