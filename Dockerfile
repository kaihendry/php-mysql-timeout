FROM dunglas/frankenphp

RUN cp $PHP_INI_DIR/php.ini-development $PHP_INI_DIR/php.ini
RUN sed -i "s/output_buffering = 4096/output_buffering = off/" $PHP_INI_DIR/php.ini
# use pdo_mysql
RUN install-php-extensions pdo_mysql
