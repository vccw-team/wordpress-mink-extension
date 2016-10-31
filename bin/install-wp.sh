#!/usr/bin/env bash

set -ex;

DB_USER=root
DB_NAME=wordpress
PORT=8080
WP_PATH=/tmp/wordpress
WP_TITLE='Welcome to the WordPress'
WP_DESC='Hello World!'

if ! bin/wp --info ; then
    curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli-nightly.phar
    rm -fr bin && mkdir bin
    mv wp-cli-nightly.phar bin/wp
    chmod 755 bin/wp
fi

bin/wp core download --path=$WP_PATH --locale=en_US --force

bin/wp core config \
--path=$WP_PATH \
--dbhost=localhost \
--dbname=$DB_NAME \
--dbuser=$DB_USER \
--dbprefix=wp_ \
--locale=en_US \
--extra-php <<PHP
define( 'JETPACK_DEV_DEBUG', true );
define( 'WP_DEBUG', true );
PHP

bin/wp core install \
--path=$WP_PATH \
--url=http://127.0.0.1:$PORT \
--title="WordPress" \
--admin_user="admin" \
--admin_password="admin" \
--admin_email="admin@example.com"

bin/wp rewrite structure "/archives/%post_id%" --path=$WP_PATH

bin/wp option update blogname "$WP_TITLE" --path=$WP_PATH
bin/wp option update blogdescription "$WP_DESC" --path=$WP_PATH

if [ -e "provision-post.sh" ]; then
    bash provision-post.sh
fi

bin/wp server --host=0.0.0.0 --port=$PORT --docroot=$WP_PATH --path=$WP_PATH > /dev/null &
