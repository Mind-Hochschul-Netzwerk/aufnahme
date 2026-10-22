FROM trafex/php-nginx:3.12

LABEL Maintainer="Henrik Gebauer <code@henrik-gebauer.de>" \
      Description="mind-hochschul-netzwerk.de"

COPY --from=composer /usr/bin/composer /usr/bin/composer

USER root

RUN set -x \
  && apk upgrade --no-cache

# install packages
RUN set -x \
  && apk --no-cache add \
       php85-pdo_mysql \
       php85-iconv \
  && chown nobody:nobody /var/www

USER nobody

COPY config/nginx/ /etc/nginx
COPY config/php-custom.ini /etc/php85/conf.d/custom.ini
COPY --chown=nobody app/ /var/www

RUN composer install -d "/var/www/" --optimize-autoloader --no-dev --no-interaction --no-progress --no-cache
