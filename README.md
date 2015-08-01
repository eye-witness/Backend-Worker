# Backend API Handler
By Eye Witness

This repo contains the PHP backend where the police crime data is handled.
The client sends POST requests to get information of crimes in blocks.

It can also support PUT requests from co-operating police forces or fetch crime
data when published by police forces.

API documentation can be found [here](https://github.com/eye-witness/Documentation/blob/master/API%20Docs.md) in our documentation repository

## Installation

This project **requires PHP 7** (beta1 or later).
I suggest you either use the instructions at Zend's [dedicated PHP 7 site](http://php7.zend.com/repo.php) or the [vagrant box](https://github.com/rlerdorf/php7dev) provided by Rasmus

1. Clone the respository
1. If you don't have composer in your path run `curl -sS https://getcomposer.org/installer | php`
1. Run `composer install`

## Test suite

There is a test suite available. Simply run `php bin/phpunit -c app/` after dependencies are installed (on PHP 7).
