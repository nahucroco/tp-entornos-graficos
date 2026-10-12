FROM php:8.3-apache

# Habilitar módulos de Apache requeridos
RUN a2enmod rewrite headers

# Instalar y compilar extensiones de PDO MySQL
RUN docker-php-ext-install pdo pdo_mysql

# Redirigir el DocumentRoot de Apache al directorio public
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Copiar el proyecto
WORKDIR /var/www/html
COPY . /var/www/html/

# Asegurar permisos de escritura para storage de logs
RUN mkdir -p /var/www/html/storage/logs && chmod -R 777 /var/www/html/storage

EXPOSE 80
CMD ["apache2-foreground"]
