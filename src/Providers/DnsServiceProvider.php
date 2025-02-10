<?php

namespace Dolalima\Laravel\Dns\Providers;
use Dolalima\Laravel\Dns\Facades\DnsManager;
use Illuminate\Support\ServiceProvider;

class DnsServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('dns', function () {

            return new DnsManager($this->app);

        });
    }

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/dns.php' => config_path('dns.php'),
        ], 'config');
    }

}
