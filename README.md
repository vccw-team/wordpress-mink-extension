# WordPress Extension for the Behat

[![Build Status](https://travis-ci.org/vccw-team/wordpress-extension.svg?branch=master)](https://travis-ci.org/vccw-team/wordpress-extension)

## Getting Started

The recomended way to install is by using Composer.

```
$ composer require vccw-team/wordpress-extension
```

After that you will be able to initialize the project.

```
$ vendor/bin/behat --init
```

Example of the `behat.yml` is following. You should edit a value of the `base_url`.

https://github.com/vccw-team/wordpress-extension/blob/master/behat.yml.dist

Example `*.feature` are in the following.

https://github.com/vccw-team/wordpress-extension/tree/master/features

## Contributing

### Automated Testing

Clone this repository.

```
$ git clone git@github.com:vccw-team/wordpress-extension.git
```

Change into the directory.

```
$ cd wordpress-extension
```

Install dependencies.

```
$ npm install
$ composer install
```

Start the phantomjs as a daemon.

```
$ npm start
```

Start a WordPress site.

```
$ export WP_PATH=/tmp/wordpress
$ export WP_PORT=8080
$ bash bin/install-wp.sh
$ ./wp-cli-nightly.phar server --host=0.0.0.0 --port=$WP_PORT --docroot=$WP_PATH --path=$WP_PATH
```

Run the test!

```
$ npm test
```

Stop phantomjs.

```
$ npm stop
```
