<?php
namespace tests\Unit;

use Dolalima\Laravel\Dns\Facades\Dns\Drivers\CloudFlareDnsManager;
use Dolalima\Laravel\Dns\Facades\Dns\Drivers\Route53DnsManager;
use Orchestra\Testbench\TestCase;


class DnsServiceProviderTest extends TestCase
{
    use \Orchestra\Testbench\Concerns\WithLaravel;

    protected $app;

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        // Setup a testing configuration (important!)
        $app['config']->set('dns.default', 'cloudflare');
        $app['config']->set('dns.drivers', [
            'cloudflare' => [
                'driver' => 'cloudflare',
                'email' => env('CLOUDFLARE_EMAIL'),
                'key' => env('CLOUDFLARE_KEY'),
                'zone' => env('CLOUDFLARE_ZONE')
            ]
        ]);

    }

    /**
     * Get package providers. At a minimum this must include
     * your library's service provider.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            \Dolalima\Laravel\Dns\Providers\DnsServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app)
    {
        return [
            'Dns' => \Dolalima\Laravel\Dns\Facades\Dns::class,
            'Config' => \Illuminate\Support\Facades\Config::class,
        ];
    }
}
