# -----------------------
# PHP 8.3 FPM com Debian Bookworm
# -----------------------
FROM php:8.3-fpm-bookworm

# UID/GID para ambiente local (opcional)
ARG UID=1000
ARG GID=1000

USER root

# Instala dependências do sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libpq-dev \
    libzip-dev \
    libonig-dev \
    libxml2-dev \
    && docker-php-ext-install \
        pdo \
        pdo_pgsql \
        mbstring \
        zip \
        intl \
        xml \
        opcache \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Instala Composer
RUN curl -sS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/local/bin --filename=composer

# Cria usuário não root
RUN groupadd -g $GID devgroup \
    && useradd -u $UID -g devgroup -m devuser

# Define diretório principal do Laravel
WORKDIR /var/www/html

# Copia toda a aplicação do host para a imagem
COPY src/ /var/www/html/

# Cria pastas necessárias que podem não existir
RUN mkdir -p \
    /var/www/html/storage \
    /var/www/html/storage/logs \
    /var/www/html/bootstrap/cache \
    /var/www/html/public/uploads

# Ajusta permissões de todas as pastas críticas
RUN chown -R devuser:devgroup /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public/uploads

# Configurações de PHP para uploads grandes
RUN { \
    echo "upload_max_filesize = 3072M"; \
    echo "post_max_size = 3072M"; \
    echo "memory_limit = 1024M"; \
    echo "max_execution_time = 3600"; \
    echo "max_input_time = 3600"; \
} > /usr/local/etc/php/conf.d/uploads.ini

USER devuser

CMD ["php-fpm"]