<?php

namespace tests\Feature;


use Dolalima\Laravel\Dns\Facades\Dns;
use \Orchestra\Testbench\TestCase;
use Illuminate\Contracts\Config\Repository;
use Dotenv\Dotenv;


class CloudFlareZoneTest extends TestCase
{

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

    public function testFindRecordStatic()
    {
        $zone = Dns::findZoneByName(env('CLOUDFLARE_TEST_ZONE'));

        $record = Dns::findRecord($zone->id, 'bet1');

        $this->assertIsObject($record);

    }

    public function testFindRecord()
    {
        $record = Dns::provider('cloudflare')->findZoneByName(env('CLOUDFLARE_TEST_ZONE'))->findRecord('bet1');
        $this->assertIsObject($record);
    }



}
