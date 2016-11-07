# WordPress Extension for the Behat

[![Build Status](https://travis-ci.org/vccw-team/wordpress-extension.svg?branch=master)](https://travis-ci.org/vccw-team/wordpress-extension)
[![Latest Stable Version](https://poser.pugx.org/vccw-team/wordpress-extension/v/stable)](https://packagist.org/packages/vccw-team/wordpress-extension)
[![Total Downloads](https://poser.pugx.org/vccw-team/wordpress-extension/downloads)](https://packagist.org/packages/vccw-team/wordpress-extension)
[![Latest Unstable Version](https://poser.pugx.org/vccw-team/wordpress-extension/v/unstable)](https://packagist.org/packages/vccw-team/wordpress-extension)
[![License](https://poser.pugx.org/vccw-team/wordpress-extension/license)](https://packagist.org/packages/vccw-team/wordpress-extension)

## Requires

* WordPress 4.6 or later
* PHP 5.5 or later

## Getting Started

The recomended way to install is by using Composer.

```
$ composer require vccw-team/wordpress-extension:@stable
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

Start a WordPress site.

```
$ npm run install-wp
$ npm run wp
```

Run the test!

```
$ npm test
```
