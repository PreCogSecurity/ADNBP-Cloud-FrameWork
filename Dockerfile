FROM php:8.2-cli

# Install common PHP extensions used by the framework
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    unzip \
    git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd \
    && rm -rf /var/lib/apt/lists/*

# Install the PHP intl extension (for date/string handling)
RUN apt-get update && apt-get install -y libicu-dev \
    && docker-php-ext-install intl \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /app

# Copy composer files first for layer caching
COPY composer.json composer.lock* ./
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && composer install --no-dev --prefer-dist --no-autoloader --no-scripts \
    || true

# Copy the rest of the application
COPY . .

# Generate autoloader
RUN composer dump-autoload --optimize --no-dev 2>/dev/null || true

# Install dev dependencies for testing
RUN composer install --prefer-dist --no-progress 2>/dev/null || true

EXPOSE 8080

CMD ["php", "-S", "0.0.0.0:8080", "-t", "."]
