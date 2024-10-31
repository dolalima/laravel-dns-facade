<?php
use Dolalima\Laravel\Dns\Providers\DnsServiceProvider;
use Dolalima\Laravel\Dns\Facades\Dns\Drivers\Route53DnsManager;
use Dolalima\Laravel\Dns\Facades\Dns\Drivers\CloudFlareDnsManager;
use Illuminate\Container\Container;
use Illuminate\Contracts\Foundation\Application;
use PHPUnit\Framework\TestCase;

class DnsServiceProviderTest extends TestCase
{
    protected $app;

    protected function setUp(): void
    {
        parent::setUp();
        $this->app = new Container();
        $this->app->instance('app', $this->app);
        $this->app->instance(Application::class, $this->app);
    }

    public function binds_dns_manager_based_on_configuration()
    {
        $this->app->instance('config', new class {
            public function get($key)
            {
                return 'cloudflare';
            }
        });

        $provider = new DnsServiceProvider($this->app);
        $provider->register();

        $dnsManager = $this->app->make('dns');
        $this->assertInstanceOf(CloudflareDnsManager::class, $dnsManager);
    }

    public function throws_exception_for_invalid_dns_driver()
    {
        $this->app->instance('config', new class {
            public function get($key)
            {
                return 'invalid_driver';
            }
        });

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid DNS driver');

        $provider = new DnsServiceProvider($this->app);
        $provider->register();

        $this->app->make('dns');
    }

    public function publishes_configuration_file()
    {
        $provider = new DnsServiceProvider($this->app);
        $provider->boot();

        $this->assertArrayHasKey('config', $provider->publishes);
        $this->assertEquals(
            [__DIR__ . '/../config/dns.php' => config_path('dns.php')],
            $provider->publishes['config']
        );
    }
}
