# Usa a imagem base do PHP 8.1 com FPM
FROM php:8.1-fpm

# Temporariamente root para instalar dependências
USER root

# Atualiza pacotes e instala dependências
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    unzip \
    curl \
    libpq-dev \
    postgresql-client \
    && docker-php-ext-install pdo pdo_pgsql \
    && rm -rf /var/lib/apt/lists/*

# Instala o Composer globalmente
RUN curl -sS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/local/bin --filename=composer

# Define o diretório de trabalho
WORKDIR /var/www/html

# Copia código da aplicação e .env
COPY src/ .
COPY src/.env .

# Cria pastas internas necessárias apenas para o container (não bind mounts)
RUN mkdir -p /var/www/html/storage/logs \
    && mkdir -p /var/www/html/bootstrap/cache

# Ajusta permissões **somente dentro do container**, para pastas não bind-mounted
RUN chmod -R 775 /var/www/html/storage/logs \
    && chmod -R 775 /var/www/html/bootstrap/cache

# Configura PHP para uploads grandes
RUN { \
    echo 'upload_max_filesize = 3072M'; \
    echo 'post_max_size = 3072M'; \
    echo 'memory_limit = 1024M'; \
    echo 'max_execution_time = 3600'; \
    echo 'max_input_time = 3600'; \
} > /usr/local/etc/php/conf.d/uploads.ini

# Ajusta o usuário padrão para PHP-FPM
USER www-data

# Comando padrão
CMD ["php-fpm"]
