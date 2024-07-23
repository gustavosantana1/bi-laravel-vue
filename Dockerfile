FROM php:8.1-fpm

# Install dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    unzip \
    git \
    curl \
    gnupg \
    libicu-dev \
    libpq-dev \
    libxslt-dev \
    zlib1g-dev \
    sudo \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql zip

# Install Node.js
RUN curl -fsSL https://deb.nodesource.com/setup_16.x | bash - \
    && apt-get install -y nodejs

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Create user and group
RUN groupadd -r www && useradd -r -g www www

# Set working directory
WORKDIR /var/www

# Copy application files
COPY . /var/www

# Set permissions


RUN chmod -R 777 /var/www

# Install Composer dependencies
RUN composer install

# Install NPM dependencies and build assets

# Expose port
EXPOSE 9000

# Command to run
CMD ["sh", "-c", "sleep 30 && npm install && npm run build && php artisan migrate --force && php artisan db:seed --class=SalesDataSeeder && php-fpm"]
