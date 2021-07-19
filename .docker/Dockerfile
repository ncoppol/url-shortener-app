FROM php:7.3-apache
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN chmod +x /usr/local/bin/install-php-extensions && sync && \
    install-php-extensions intl mysqli


COPY . /app
COPY .docker/.env /app/.env
COPY .docker/vhost.conf /etc/apache2/sites-available/000-default.conf
RUN chown -R www-data:www-data /app && a2enmod rewrite
COPY .docker/wait-for-it.sh /tmp/wait-for-it.sh
RUN chmod +x /tmp/wait-for-it.sh
COPY .docker/db.sh /tmp/db.sh
RUN chmod +x /tmp/db.sh