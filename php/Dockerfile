# Usa a imagem base do PHP 7.4 com FPM
FROM php:8.1-fpm

# Define o usuário como root (em prod somente)
USER root

# Atualiza pacotes e instala dependencias
RUN apt-get update && \
    apt-get install -y --no-install-recommends \
    git \
    unzip \
    curl \
    libpq-dev && \
    docker-php-ext-install pdo pdo_pgsql && \
    rm -rf /var/lib/apt/lists/*

# Instala o Composer globalmente
RUN curl -sS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/local/bin --filename=composer

# instalando o psql para testar    
RUN apt-get update && \
    apt-get install -y --no-install-recommends \
        libpq-dev \
        postgresql-client \
    && docker-php-ext-install pdo pdo_pgsql \
    && rm -rf /var/lib/apt/lists/*


# Define o diretório de trabalho
WORKDIR /var/www/html

# Copia o código PHP 
COPY src/ .

COPY src/.env .

# Ajusta permissões (libera geral — para desenvolvimento)
RUN chmod -R 777 /var/www/html

# Configurações para uploads grandes
RUN { \
    echo 'upload_max_filesize = 2048M'; \
    echo 'post_max_size = 2048M'; \
    echo 'memory_limit = 512M'; \
    echo 'max_execution_time = 3600'; \
    echo 'max_input_time = 3600'; \
} > /usr/local/etc/php/conf.d/uploads.ini

# Verifica Composer
RUN composer --version

# Comando padrão
CMD ["php-fpm"]
