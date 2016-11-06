#!/usr/bin/env bash

set -ex;

DB_USER=root
DB_NAME=wp-behat-tests
WP_PORT=8080
WP_PATH=/tmp/wordpress
WP_TITLE='Welcome to the WordPress'
WP_DESC='Hello World!'

if [ -e $WP_PATH ]; then
  rm -fr $WP_PATH
fi

mysql -e "drop database IF EXISTS \`$DB_NAME\`;" -uroot
mysql -e "create database IF NOT EXISTS \`$DB_NAME\`;" -uroot

curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli-nightly.phar
chmod 755 ./wp-cli-nightly.phar

if [ ! $WP_VERSION ]; then
  WP_VERSION=latest
fi

./wp-cli-nightly.phar core download --path=$WP_PATH --locale=en_US --version=$WP_VERSION --force

./wp-cli-nightly.phar core config \
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

./wp-cli-nightly.phar core install \
--path=$WP_PATH \
--url=http://127.0.0.1:$WP_PORT \
--title="WordPress" \
--admin_user="admin" \
--admin_password="admin" \
--admin_email="admin@example.com"

./wp-cli-nightly.phar rewrite structure "/archives/%post_id%" --path=$WP_PATH

./wp-cli-nightly.phar option update blogname "$WP_TITLE" --path=$WP_PATH
./wp-cli-nightly.phar option update blogdescription "$WP_DESC" --path=$WP_PATH

./wp-cli-nightly.phar user create editor editor@example.com --role=editor --user_pass=editor --path=$WP_PATH

./wp-cli-nightly.phar plugin install wordpress-importer --activate --path=$WP_PATH
./wp-cli-nightly.phar import bin/theme-unit-test-data.xml --authors=create --path=$WP_PATH > /dev/null 2>&1

if [ $WP_THEME ]; then
  ./wp-cli-nightly.phar theme install $WP_THEME --activate --path=$WP_PATH --force
fi
