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
If you are using Laravel 5.5 or later, you can skip this step, as the package will be auto-discovered.
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

or

Dns::provider('cloudflare')->zones();
```


### Find Zone
```php
use Dolalima\Laravel\Dns\Facades\Dns;
$zone = Dns::zone('example.com');

```    

### List Records
```php
use Dolalima\Laravel\Dns\Facades\Dns;
$zone = Dns::zone('example.com');
$records = Dns::records($zone);

or

$records = $zone->records();
```

### Create Record
```php
use Dolalima\Laravel\Dns\Facades\Dns;
$zone = Dns::zone('example.com');
$record = Dns::create($zone, 'A', 'www');
```

### Delete Record
```php
use Dolalima\Laravel\Dns\Facades\Dns;
$zone = Dns::zone('example.com');
$result = Dns::delete($zone,'www');
```


### Abilities
- [x] List Zones
- [x] Find Zone
- [x] List Records
- [x] Create Record
- [ ] Update Record
- [x] Delete Record

## Available DNS Drivers

- [x] AWS Route 53
- [x] Cloudflare
- [ ] DigitalOcean (not implemented yet)
- [ ] Google Cloud DNS (not implemented yet)

