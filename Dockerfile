FROM bitnami/laravel:latest

USER root

WORKDIR /app

COPY ./server/HyperionServer /app

RUN composer install --no-interaction --optimize-autoloader

RUN chmod -R 777 storage bootstrap/cache

EXPOSE 8000

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]