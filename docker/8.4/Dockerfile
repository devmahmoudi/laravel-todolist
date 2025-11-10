# syntax=docker/dockerfile:1
FROM ubuntu:24.04

LABEL maintainer="Mahdi Mahmoudi"

ARG WWWGROUP
ARG NODE_VERSION=22
ARG MYSQL_CLIENT="mysql-client"
ARG POSTGRES_VERSION=17

WORKDIR /var/www/html

ENV DEBIAN_FRONTEND=noninteractive
ENV TZ=UTC
ENV SUPERVISOR_PHP_COMMAND="/usr/bin/php -d variables_order=EGPCS /var/www/html/artisan serve --host=0.0.0.0 --port=80"
ENV SUPERVISOR_PHP_USER="sail"

# === SYSTEM SETUP (Cached if base image unchanged) ===
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone \
    && echo "Acquire::http::Pipeline-Depth 0;" > /etc/apt/apt.conf.d/99custom \
    && echo "Acquire::http::No-Cache true;" >> /etc/apt/apt.conf.d/99custom \
    && echo "Acquire::BrokenProxy true;" >> /etc/apt/apt.conf.d/99custom

# === INSTALL SYSTEM & PHP EXTENSIONS (One layer, cached) ===
RUN apt-get update && apt-get upgrade -y \
    && mkdir -p /etc/apt/keyrings \
    && apt-get install -y gnupg gosu curl ca-certificates zip unzip git supervisor sqlite3 libcap2-bin libpng-dev python3 dnsutils librsvg2-bin fswatch ffmpeg nano \
    && curl -sS 'https://keyserver.ubuntu.com/pks/lookup?op=get&search=0xb8dc7e53946656efbce4c1dd71daeaab4ad4cab6' | gpg --dearmor | tee /etc/apt/keyrings/ppa_ondrej_php.gpg > /dev/null \
    && echo "deb [signed-by=/etc/apt/keyrings/ppa_ondrej_php.gpg] https://ppa.launchpadcontent.net/ondrej/php/ubuntu noble main" > /etc/apt/sources.list.d/ppa_ondrej_php.list \
    && apt-get update \
    && apt-get install -y \
       php8.4-cli php8.4-dev php8.4-pgsql php8.4-sqlite3 php8.4-gd php8.4-curl php8.4-mongodb \
       php8.4-imap php8.4-mysql php8.4-mbstring php8.4-xml php8.4-zip php8.4-bcmath php8.4-soap \
       php8.4-intl php8.4-readline php8.4-ldap php8.4-msgpack php8.4-igbinary php8.4-redis \
       php8.4-swoole php8.4-memcached php8.4-pcov php8.4-imagick php8.4-xdebug \
    && curl -sLS https://getcomposer.org/installer | php -- --install-dir=/usr/bin/ --filename=composer \
    && curl -fsSL https://deb.nodesource.com/gpgkey/nodesource-repo.gpg.key | gpg --dearmor -o /etc/apt/keyrings/nodesource.gpg \
    && echo "deb [signed-by=/etc/apt/keyrings/nodesource.gpg] https://deb.nodesource.com/node_$NODE_VERSION.x nodistro main" > /etc/apt/sources.list.d/nodesource.list \
    && apt-get update && apt-get install -y nodejs \
    && npm install -g npm pnpm bun \
    && curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | gpg --dearmor | tee /etc/apt/keyrings/yarn.gpg >/dev/null \
    && echo "deb [signed-by=/etc/apt/keyrings/yarn.gpg] https://dl.yarnpkg.com/debian/ stable main" > /etc/apt/sources.list.d/yarn.list \
    && curl -sS https://www.postgresql.org/media/keys/ACCC4CF8.asc | gpg --dearmor | tee /etc/apt/keyrings/pgdg.gpg >/dev/null \
    && echo "deb [signed-by=/etc/apt/keyrings/pgdg.gpg] http://apt.postgresql.org/pub/repos/apt noble-pgdg main" > /etc/apt/sources.list.d/pgdg.list \
    && apt-get update \
    && apt-get install -y yarn $MYSQL_CLIENT postgresql-client-$POSTGRES_VERSION \
    && apt-get -y autoremove && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

RUN setcap "cap_net_bind_service=+ep" /usr/bin/php8.4

# === STAGE: Install Dependencies (Cached via BuildKit cache mounts) ===

# Copy lockfiles first
COPY composer.json composer.lock package.json package-lock.json ./

# Composer install with cache mount
RUN --mount=type=cache,target=/root/.composer/cache \
    composer install -n --prefer-dist --no-dev --no-scripts --optimize-autoloader

# npm install + build with cache mount
RUN --mount=type=cache,target=/root/.npm \
    --mount=type=cache,target=/root/.npm/_cacache \
    npm ci --omit=dev --no-audit --no-fund

# Copy Vite config + source (only what build needs)
COPY vite.config.ts ./
COPY resources ./resources
COPY public ./public
COPY tsconfig.json ./

# Build (only runs if source or config changes)
RUN npm run build

# === Copy Application Code (Only this layer changes on code push) ===
COPY . .
COPY .env.example .env

# Final Composer setup
RUN composer dump-autoload --optimize

# === Permissions & User Setup ===
RUN userdel -r ubuntu 2>/dev/null || true
RUN groupadd --force -g $WWWGROUP sail
RUN useradd -ms /bin/bash --no-user-group -g $WWWGROUP -u 1337 sail

COPY ./docker/8.4/start-container /usr/local/bin/start-container
COPY ./docker/8.4/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY ./docker/8.4/php.ini /etc/php/8.4/cli/conf.d/99-sail.ini
RUN chmod +x /usr/local/bin/start-container

# Fix permissions
RUN chown -R sail:$WWWGROUP storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 80/tcp

ENTRYPOINT ["start-container"]