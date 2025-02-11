<?php

namespace tests\Feature;

use \Orchestra\Testbench\TestCase;
use Illuminate\Contracts\Config\Repository;
use Dotenv\Dotenv;

class Route53Test extends TestCase
{

    protected $zone_test = 'sisgr.com';

    protected $loadEnvironmentVariables = true;

    /**
     * Automatically enables package discoveries.
     *
     * @var bool
     */
    protected $enablesPackageDiscoveries = true;

    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array<int, class-string<\Illuminate\Support\ServiceProvider>>
     */
    protected function getPackageProviders($app)
    {
        return [
            \Dolalima\Laravel\Dns\Providers\DnsServiceProvider::class
        ];
    }

    protected function getPackageAliases($app)
    {
        return [
            'Dns' => \Dolalima\Laravel\Dns\Facades\Dns::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../../workbench');
        $dotenv->load();
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function defineEnvironment($app)
    {
        $app['config']->set('dns', require __DIR__ . '/../../src/config/dns.php');
    }


    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        // Code before application created.

        $this->afterApplicationCreated(function () {
            // Code after application created.

        });

        $this->beforeApplicationDestroyed(function () {
            // Code before application destroyed.
        });

        parent::setUp();
    }

    public function testLoadConfig()
    {
        $this->app['config']->set('dns.default', 'route53');
        $this->assertArrayHasKey('dns', config());
    }

    public function testListZone(){
        $this->app['config']->set('dns.default', 'route53');
        $dns = app('dns');
        $zones = $dns->listZones();
        $this->assertIsArray($zones);
    }

    public function testFindZone(){
        $this->app['config']->set('dns.default', 'route53');
        $dns = app('dns');
        $zone = $dns->findZoneByName($this->zone_test);
        $this->assertIsObject($zone);
    }

    public function testListRecords(){
        $this->app['config']->set('dns.default', 'route53');
        $dns = app('dns');
        $records = $dns->listRecords($this->zone_test);
        $this->assertIsArray($records);
    }

    public function testFindRecord(){
        $this->app['config']->set('dns.default', 'route53');
        $dns = app('dns');
        $record = $dns->findRecord($this->zone_test, 'lti');
        $this->assertIsObject($record);
    }

}
