# syntax=docker/dockerfile:1
FROM php:8.4-fpm


# Replace ALL Debian repos with ArvanCloud mirror (Trixie)
RUN rm -f /etc/apt/sources.list.d/* && \
    echo "deb http://mirror.arvancloud.ir/debian trixie main" > /etc/apt/sources.list && \
    echo "deb http://mirror.arvancloud.ir/debian-security trixie-security main" >> /etc/apt/sources.list && \
    echo 'Acquire::Check-Valid-Until "false";' > /etc/apt/apt.conf.d/99no-check-valid && \
    echo 'Acquire::AllowInsecureRepositories "true";' >> /etc/apt/apt.conf.d/99no-check-valid && \
    apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libgd-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl \
    libzip-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        gd \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        zip

# Install Redis extension from GitHub (bypass pecl.php.net)
RUN curl -L https://github.com/phpredis/phpredis/archive/refs/tags/6.1.0.tar.gz -o /tmp/redis.tar.gz \
    && mkdir -p /tmp/redis \
    && tar -xzf /tmp/redis.tar.gz -C /tmp/redis --strip-components=1 \
    && docker-php-ext-install /tmp/redis \
    && rm -rf /tmp/redis /tmp/redis.tar.gz

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy existing application directory contents
COPY . /var/www

# Set permissions
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www

COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["entrypoint.sh"]

# Expose port 9000 and start php-fpm server
EXPOSE 9000
