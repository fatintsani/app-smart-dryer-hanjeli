# syntax=docker/dockerfile:1
FROM php:8.4-fpm-bookworm AS php-base

RUN apt-get update && apt-get install -y --no-install-recommends \
        libicu-dev libzip-dev libonig-dev libicu72 libzip4 libonig5 unzip \
    && docker-php-ext-install -j"$(nproc)" pdo_mysql mbstring intl bcmath pcntl zip opcache \
    && apt-get purge -y --auto-remove libicu-dev libzip-dev libonig-dev $PHPIZE_DEPS \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html
COPY docker/php/production.ini /usr/local/etc/php/conf.d/production.ini
COPY docker/php/fpm.conf /usr/local/etc/php-fpm.d/zz-production.conf

FROM php-base AS vendor
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer
COPY . .
RUN composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction --no-progress \
    && composer check-platform-reqs --no-dev

FROM node:22.23.2-bookworm-slim AS frontend
WORKDIR /build
COPY package.json package-lock.json .npmrc ./
RUN npm ci
COPY resources ./resources
COPY public ./public
COPY vite.config.js ./
COPY scripts/pwa-build.js ./scripts/pwa-build.js
COPY tests/frontend ./tests/frontend
# These are public browser settings. Never pass server secrets as build args.
ARG VITE_APP_NAME=Smart_Dryer_Hanjeli
ARG APP_BUILD_REVISION=local
ARG VITE_REVERB_APP_KEY
ARG VITE_REVERB_HOST=hanjeli-dryer.stas-rg.com
ARG VITE_REVERB_PORT=443
ARG VITE_REVERB_SCHEME=https
ARG VITE_API_BASE_URL=/api
ARG VITE_GOOGLE_CLIENT_ID=
RUN test -n "$VITE_REVERB_APP_KEY" \
    && test "$VITE_REVERB_HOST" = hanjeli-dryer.stas-rg.com \
    && test "$VITE_REVERB_PORT" = 443 \
    && test "$VITE_REVERB_SCHEME" = https \
    && npm run test:frontend \
    && npm run build \
    && test -s public/build/manifest.json \
    && node --input-type=module -e 'import fs from "node:fs"; const keys = ["VITE_APP_NAME", "VITE_REVERB_APP_KEY", "VITE_REVERB_HOST", "VITE_REVERB_PORT", "VITE_REVERB_SCHEME", "VITE_API_BASE_URL", "VITE_GOOGLE_CLIENT_ID"]; fs.writeFileSync("public/build/deployment.json", JSON.stringify(Object.fromEntries(keys.map(k => [k, process.env[k] || ""]))));'

FROM php-base AS runtime
COPY --from=vendor --chown=root:root /var/www/html /var/www/html
COPY --from=frontend /build/public/build ./public/build
COPY --from=frontend /build/public/build/sw.js ./public/sw.js
RUN mkdir -p storage/app/public storage/app/private storage/framework/cache/data \
        storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && ln -s /var/www/html/storage/app/public public/storage \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R u=rwX,g=rwX,o=rX storage bootstrap/cache \
    && chmod +x docker/entrypoint.sh \
    && rm -rf tests phpunit.xml
USER www-data
ENTRYPOINT ["/var/www/html/docker/entrypoint.sh"]
CMD ["php-fpm", "-F"]

# Used only for CI/local verification; development packages never enter runtime.
FROM php-base AS test
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer
COPY . .
COPY --from=frontend /build/public/build ./public/build
COPY --from=frontend /build/public/build/sw.js ./public/sw.js
RUN composer install --prefer-dist --no-interaction --no-progress
ENV APP_ENV=testing
# Empty dotenv file exists only in the disposable test container, never an image.
CMD ["sh", "-c", "touch .env.testing && php artisan test --fail-on-warning"]

FROM nginx:stable-alpine AS web
WORKDIR /var/www/html
COPY docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf
COPY --from=runtime /var/www/html/public ./public
USER nginx
ENTRYPOINT ["nginx"]
CMD ["-g", "daemon off;"]
