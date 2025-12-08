# syntax=docker/dockerfile:1
FROM ubuntu:24.04

LABEL maintainer="Mahdi Mahmoudi"

ARG WWWGROUP
ARG MYSQL_CLIENT="mysql-client"
ARG POSTGRES_VERSION=18

WORKDIR /var/www/html

ENV DEBIAN_FRONTEND=noninteractive
ENV TZ=UTC
ENV SUPERVISOR_PHP_COMMAND="/usr/bin/php -d variables_order=EGPCS /var/www/html/artisan serve --host=0.0.0.0 --port=80"
ENV SUPERVISOR_PHP_USER="sail"

# === SYSTEM SETUP ===
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone \
    && echo "Acquire::http::Pipeline-Depth 0;" > /etc/apt/apt.conf.d/99custom \
    && echo "Acquire::http::No-Cache true;" >> /etc/apt/apt.conf.d/99custom \
    && echo "Acquire::BrokenProxy true;" >> /etc/apt/apt.conf.d/99custom

# === INSTALL SYSTEM & PHP EXTENSIONS ===
RUN apt-get update && apt-get upgrade -y \
    && mkdir -p /etc/apt/keyrings \
    && apt-get install -y gnupg gosu curl ca-certificates zip unzip git supervisor sqlite3 libcap2-bin \
       libpng-dev python3 dnsutils librsvg2-bin fswatch ffmpeg nano \
    # Add Ondrej PHP repo
    && curl -sS 'https://keyserver.ubuntu.com/pks/lookup?op=get&search=0xb8dc7e53946656efbce4c1dd71daeaab4ad4cab6' \
        | gpg --dearmor -o /etc/apt/keyrings/ppa_ondrej_php.gpg \
    && echo "deb [signed-by=/etc/apt/keyrings/ppa_ondrej_php.gpg] https://ppa.launchpadcontent.net/ondrej/php/ubuntu noble main" \
        > /etc/apt/sources.list.d/ppa_ondrej_php.list \
    && apt-get update \
    && apt-get install -y \
       php8.4-cli php8.4-dev php8.4-pgsql php8.4-sqlite3 php8.4-gd php8.4-curl php8.4-mongodb \
       php8.4-imap php8.4-mysql php8.4-mbstring php8.4-xml php8.4-zip php8.4-bcmath php8.4-soap \
       php8.4-intl php8.4-readline php8.4-ldap php8.4-msgpack php8.4-igbinary php8.4-redis \
       php8.4-swoole php8.4-memcached php8.4-pcov php8.4-imagick php8.4-xdebug \
    # PostgreSQL repo
    && curl -sS https://www.postgresql.org/media/keys/ACCC4CF8.asc \
        | gpg --dearmor -o /etc/apt/keyrings/pgdg.gpg \
    && echo "deb [signed-by=/etc/apt/keyrings/pgdg.gpg] http://apt.postgresql.org/pub/repos/apt noble-pgdg main" \
        > /etc/apt/sources.list.d/pgdg.list \
    && apt-get update \
    && apt-get install -y $MYSQL_CLIENT postgresql-client-$POSTGRES_VERSION \
    # Composer
    && curl -sLS https://getcomposer.org/installer \
        | php -- --install-dir=/usr/bin/ --filename=composer \
    && apt-get -y autoremove && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

RUN setcap "cap_net_bind_service=+ep" /usr/bin/php8.4

# === Composer Dependencies (Cacheable Layer) ===
COPY composer.json composer.lock ./

RUN --mount=type=cache,target=/root/.composer/cache \
    composer install -n --prefer-dist --no-dev --no-scripts --optimize-autoloader

# === Copy Application Code ===
COPY . .

RUN composer dump-autoload --optimize

# === Permissions & User Setup ===
RUN userdel -r ubuntu 2>/dev/null || true
RUN groupadd --force -g $WWWGROUP sail
RUN useradd -ms /bin/bash --no-user-group -g $WWWGROUP -u 1337 sail
RUN git config --global --add safe.directory /var/www/html

COPY docker/8.4/start-container /usr/local/bin/start-container
COPY docker/8.4/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/8.4/php.ini /etc/php/8.4/cli/conf.d/99-sail.ini
RUN chmod +x /usr/local/bin/start-container

# Fix permissions
RUN chown -R sail:$WWWGROUP storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 80/tcp

ENTRYPOINT ["start-container"]