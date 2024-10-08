FROM dunglas/frankenphp

RUN cp $PHP_INI_DIR/php.ini-development $PHP_INI_DIR/php.ini
# make sure logs are written to stdout
RUN sed -i "s/output_buffering = 4096/output_buffering = off/" $PHP_INI_DIR/php.ini
RUN install-php-extensions pdo_mysql
