<?php

namespace Dolalima\Laravel\Dns\Providers;
use Dolalima\Laravel\Dns\Facades\Dns\Drivers\Route53DnsManager;
use Dolalima\Laravel\Dns\Facades\Dns\Drivers\CloudFlareDnsManager;
use Illuminate\Support\ServiceProvider;

class DnsServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('dns', function () {


            $driver = config('dns.default');


            if ($driver == 'cloudflare') {
                return new CloudflareDnsManager();
            } elseif ($driver == 'route53') {
                return new Route53DnsManager();
            } else {
                throw new \Exception('Invalid DNS driver');
            }

        });
    }

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/dns.php' => config_path('dns.php'),
        ], 'config');
    }

}
