# === ЭТАП 1: Сборка и установка зависимостей ===
FROM php:8.5-alpine AS builder

# Устанавливаем системные утилиты для работы Composer
RUN apk add --no-cache git unzip bash

# Устанавливаем Composer внутрь сборочного контейнера
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Копируем файлы зависимостей
COPY composer.json composer.lock ./

# Устанавливаем зависимости (без dev-пакетов вроде линтеров для продакшена)
# Если вам НА СЕРВЕРЕ нужен PHPCS, удалите флаг --no-dev
RUN composer install --no-interaction --no-plugins --no-scripts --prefer-dist --no-dev

# === ЭТАП 2: Финальный продакшен-образ ===
FROM php:8.5-alpine

# Устанавливаем необходимые системные расширения, если они понадобятся
RUN apk add --no-cache bash

WORKDIR /var/www

# Копируем зависимости из первого этапа
COPY --from=builder /app/vendor ./vendor

# Копируем исходный код проекта (в вашем случае только папку public)
COPY public/ ./public

# Указываем порт, который будет слушать приложение
EXPOSE 8000

# Запускаем встроенный PHP-сервер (как в вашем Makefile)
CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
