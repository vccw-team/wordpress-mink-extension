# Contributing

## Automated Testing

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

Start a WordPress server.

```
$ export WP_VERSION=latest
$ export WP_THEME=twentysixteen
$ npm run install-wp
$ npm run wp
```

Run the test!

```
$ npm test
```
