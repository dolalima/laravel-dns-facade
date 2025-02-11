<?php

namespace tests\Feature;


use \Orchestra\Testbench\TestCase;
use Illuminate\Contracts\Config\Repository;
use Dotenv\Dotenv;

class CloudFlareTest extends TestCase
{

    protected $zone_test = 'wee.bet';

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
        $this->assertArrayHasKey('dns', config());
    }

    public function testLoadConfigDrivers()
    {
        $this->assertArrayHasKey('providers', config('dns'));
    }

    public function testLoadConfigDriversCloudFlare()
    {
        $this->assertArrayHasKey('cloudflare', config('dns.providers'));
    }

    public function testListZones()
    {
        $dns = app('dns');
        $zones = $dns->listZones();
        $this->assertIsArray($zones);
    }

    public function testFindZoneByName()
    {
        $dns = app('dns');
        $zone = $dns->findZoneByName('wee.bet');

        $this->assertIsObject($zone);
    }

    public function testListRecords()
    {
        $dns = app('dns');
        $zones = $dns->listZones();
        $records = $dns->listRecords($zones[0]->id);
        $this->assertIsArray($records);
    }

    public function testFindRecord()
    {
        $dns = app('dns');
        $zone = $dns->findZoneByName($this->zone_test);
        $record = $dns->findRecord($zone->id, 'bet1');
        $this->assertIsObject($record);
    }



}
