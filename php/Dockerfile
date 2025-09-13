# Usa a imagem base do PHP 7.4 com FPM
FROM php:7.4-fpm

# Atualiza os pacotes e instala dependências (tudo em um único RUN)
RUN apt-get update && \
    apt-get install -y --no-install-recommends \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql \
    && rm -rf /var/lib/apt/lists/*

# Configurações para aumentar o limite de upload de arquivos
RUN { \
    echo 'upload_max_filesize = 2048M'; \
    echo 'post_max_size = 2048M'; \
    echo 'memory_limit = 512M'; \
    echo 'max_execution_time = 3600'; \
    echo 'max_input_time = 3600'; \
} > /usr/local/etc/php/conf.d/uploads.ini

# Define o usuário como www-data
USER www-data

# Define o diretório de trabalho
WORKDIR /var/www/html

# Copia o código PHP para o contêiner
COPY src/ .


