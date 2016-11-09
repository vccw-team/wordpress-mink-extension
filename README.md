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

### Install dependencies

The recomended way to install is by using Composer.

```
$ composer require vccw-team/wordpress-extension:@stable
```

### Initialize Behat

After that you will be able to initialize the project.

```
$ vendor/bin/behat --init
```

### Configuration

Place the `behat.yml` like following.

```
default:
  suites:
    default:
      paths:
        - %paths.base%/features
      contexts:
        - FeatureContext
        - VCCW\Behat\Mink\WordPressExtension\Context\WordPressContext
        - Behat\MinkExtension\Context\MinkContext
  extensions:
    VCCW\Behat\Mink\WordPressExtension:
      roles:
        administrator:
          username: admin
          password: admin
    Behat\MinkExtension:
      base_url: http://127.0.0.1:8080
      sessions:
        default:
          selenium2:
            wd_host: http://127.0.0.1:4444/wd/hub
```

* Add user accounts of your WordPress site to "VCCW\Behat\Mink\WordPressExtension > roles".
* Update value of the `Behat\MinkExtension > base_url` to your hostname.

#### You can add multiple user like following.

```
  extensions:
    VCCW\Behat\Mink\WordPressExtension:
      roles:
        administrator:
          username: admin
          password: admin
        editor:
          username: editor
          password: editor
```

https://github.com/vccw-team/wordpress-extension/blob/master/behat.yml.dist

### Write features

You can write features with Gherkin language.

https://github.com/cucumber/cucumber/wiki/Gherkin

Example `*.feature` are in the following.

https://github.com/vccw-team/wordpress-extension/tree/master/features

#### Examples

Login as the administrator role and I should see "Dashboard".

```
Feature: I login as the specfic role

  @javascript
  Scenario: Login as the "administrator" role

    When I login as the "administrator" role
    Then I should see "Welcome to WordPress!"
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

Start a WordPress site.

```
$ npm run install-wp
$ npm run wp
```

Run the test!

```
$ npm test
```
