# Usa a imagem base do PHP 8.1 com FPM
FROM php:8.1-fpm

# Recebe UID/GID do host (você passa no docker-compose)
ARG UID=1000
ARG GID=1000

# Temporariamente root para instalar dependências
USER root

# Atualiza pacotes e instala dependências
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    unzip \
    curl \
    libpq-dev \
    libicu-dev \ 
    postgresql-client \
    && docker-php-ext-install pdo pdo_pgsql intl \
    && rm -rf /var/lib/apt/lists/*

# Instala o Composer globalmente
RUN curl -sS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/local/bin --filename=composer

# Cria usuário de desenvolvimento com UID/GID do host
RUN groupadd -g $GID devgroup \
    && useradd -u $UID -g devgroup -m devuser

# Define o diretório de trabalho
WORKDIR /var/www/html

# Cria pastas internas necessárias (storage, cache, uploads)
RUN mkdir -p /var/www/html/storage /var/www/html/storage/logs \
    /var/www/html/bootstrap/cache \
    /var/www/html/public/uploads

# Ajusta permissões para o usuário devuser
RUN chown -R devuser:devgroup /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public/uploads \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public/uploads

# Configura PHP para uploads grandes
RUN { \
    echo 'upload_max_filesize = 3072M'; \
    echo 'post_max_size = 3072M'; \
    echo 'memory_limit = 1024M'; \
    echo 'max_execution_time = 3600'; \
    echo 'max_input_time = 3600'; \
} > /usr/local/etc/php/conf.d/uploads.ini

# Define o usuário padrão do container
USER devuser

# Comando padrão
CMD ["php-fpm"]
