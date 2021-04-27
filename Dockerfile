FROM mindhochschulnetzwerk/php-base

LABEL Maintainer="Henrik Gebauer <code@henrik-gebauer.de>" \
      Description="mind-hochschul-netzwerk.de"

COPY config/nginx/ /etc/nginx/
COPY entry.d/ /entry.d/
COPY update.d/ /update.d/
COPY app/ /var/www/

RUN set -ex \
  && apk --no-cache add \
    php7-mysqli \
    php7-xml \
    php7-zip \
    php7-curl \
    php7-gd \
    php7-ldap \
    php7-json \
    php7-session \
    php7-ctype \
  && mkdir /var/www/vendor && chown www-data:www-data /var/www/vendor \
  && su www-data -s /bin/sh -c "composer install -d /var/www --optimize-autoloader --no-dev --no-interaction --no-progress --no-cache" \
  && chown -R nobody:nobody /var/www \
  && mkdir -p /tmp /tmp/templates_c /tmp/cache \
  && touch /tmp/letztewartung \
  && chown -R www-data:www-data /tmp/*
