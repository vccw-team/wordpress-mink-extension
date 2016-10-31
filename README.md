# WordPress Extension for the Behat

[![Build Status](https://travis-ci.org/vccw-team/wordpress-extension.svg?branch=master)](https://travis-ci.org/vccw-team/wordpress-extension)

BDD testing extension for the WordPress

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

```
default:
  suites:
    default:
      paths:
        - %paths.base%/features
      contexts:
        - Behat\MinkExtension\Context\MinkContext
        - VCCW\Mink\WordPressExtension\Context
  extensions:
    Behat\MinkExtension:
      base_url: http://127.0.0.1:8080
      sessions:
        default:
          selenium2:
            wd_host: http://127.0.0.1:4444/wd/hub
```

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

Run the test!

```
$ npm test
```

Stop phantomjs.

```
$ npm stop
```
