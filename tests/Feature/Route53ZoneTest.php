<?php

namespace tests\Feature;

use Dolalima\Laravel\Dns\Facades\Models\DnsRecordType;
use \Orchestra\Testbench\TestCase;
use Illuminate\Contracts\Config\Repository;
use Dolalima\Laravel\Dns\Facades\Dns;
use Dotenv\Dotenv;

class Route53ZoneTest extends TestCase
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

    public function testCreateRecord(){
        $this->app['config']->set('dns.default', 'route53');

        $dns = app('dns');
        $zone = Dns::findZoneByName(env('ROUTE53_TEST_ZONE'));
        $record = $zone->createRecord('teste-1-xyz', DnsRecordType::CNAME, env('ROUTE53_TEST_ZONE'));
        $this->assertIsObject($record);
    }

    public function testRemoveRecord(){
        $this->app['config']->set('dns.default', 'route53');

        $dns = app('dns');
        $zone = $dns->findZoneByName(env('ROUTE53_TEST_ZONE'));
        $record = $dns->findRecord($zone, 'teste-1-xyz');
        $result = $dns->deleteRecord($zone, 'teste-1-xyz');
        $this->assertIsBool($result);
    }

}
