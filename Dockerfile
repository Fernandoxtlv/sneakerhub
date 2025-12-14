# Imagen base de PHP 8.2 con extensiones necesarias
FROM php:8.2-fpm

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    && docker-php-ext-install zip pdo pdo_mysql

# Instalar Composer
COPY --from=composer:2.5 /usr/bin/composer /usr/bin/composer

# Crear directorio de trabajo
WORKDIR /var/www/html

# Copiar archivos
COPY . .

# Instalar dependencias de Laravel
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Establecer permisos
RUN chmod -R 777 storage bootstrap/cache

# Exponer puerto
EXPOSE 8000

# Comando de ejecución
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]

