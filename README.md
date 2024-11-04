# Laravel DNS Facade
___
[![Latest Stable Version](http://poser.pugx.org/dolalima/laravel-dns-facade/v)](https://packagist.org/packages/dolalima/laravel-dns-facade) 
[![Total Downloads](http://poser.pugx.org/dolalima/laravel-dns-facade/downloads)](https://packagist.org/packages/dolalima/laravel-dns-facade) 
[![Latest Unstable Version](http://poser.pugx.org/dolalima/laravel-dns-facade/v/unstable)](https://packagist.org/packages/dolalima/laravel-dns-facade)
[![License](http://poser.pugx.org/dolalima/laravel-dns-facade/license)](https://packagist.org/packages/dolalima/laravel-dns-facade) 
[![PHP Version Require](http://poser.pugx.org/dolalima/laravel-dns-facade/require/php)](https://packagist.org/packages/dolalima/laravel-dns-facade)

This package provides a DNS facade for the Laravel framework, allowing you to manage DNS records using different DNS providers.  
Installation

## Install the package via Composer:
```bash
composer require dolalima/laravel-dns-facade
```

## Publish the configuration file:
```bash
php artisan vendor:publish --tag=config
```

## Add the service provider to the providers array in config/app.php:
```php
'providers' => [
// Other service providers...
Dolalima\Laravel\Dns\Providers\DnsServiceProvider::class,
],
```

## Configuration
After publishing the configuration file, you can configure the package by editing the config/dns.php file.

### List Zones
```php
use Dolalima\Laravel\Dns\Facades\Dns;
$zones = Dns::zones();
```

### List Records
```php
use Dolalima\Laravel\Dns\Facades\Dns;
$records = Dns::records('example.com');
```

### Create Record
```php
use Dolalima\Laravel\Dns\Facades\Dns;
Dns::create('example.com', 'A', 'www');
```
