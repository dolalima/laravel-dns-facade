<?php

namespace tests\Feature;

use Dolalima\Laravel\Dns\Facades\Models\DnsRecordType;
use \Orchestra\Testbench\TestCase;
use Illuminate\Contracts\Config\Repository;
use Dolalima\Laravel\Dns\Facades\Dns;
use Dotenv\Dotenv;

class Route53Test extends TestCase
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
        $zone = $dns->findZoneByName(env('ROUTE53_TEST_ZONE'));
        $this->assertIsObject($zone);
    }

    public function testListRecords(){
        $this->app['config']->set('dns.default', 'route53');
        $dns = app('dns');
        $records = $dns->listRecords(env('ROUTE53_TEST_ZONE'));
        $this->assertIsArray($records);
    }

    public function testFindRecord(){
        $this->app['config']->set('dns.default', 'route53');
        $dns = app('dns');
        $zone = $dns->findZoneByName(env('ROUTE53_TEST_ZONE'));
        $record = $zone->findRecord('lti');
        $this->assertIsObject($record);
    }


    public function testCreateRecord(){

        $this->app['config']->set('dns.default', 'route53');

        $zone = Dns::provider('route53')->findZoneByName(env('ROUTE53_TEST_ZONE'));
        $record = Dns::createRecord($zone, 'teste-xyz', DnsRecordType::CNAME, env('ROUTE53_TEST_ZONE') );
        $this->assertIsObject($record);
    }


    public function testRemoveRecord(){

        $this->app['config']->set('dns.default', 'route53');

        $zone = Dns::findZoneByName(env('ROUTE53_TEST_ZONE'));
        $record = Dns::findRecord($zone, 'teste-xyz');



        $this->assertIsBool(Dns::deleteRecord($zone, 'teste-xyz'));
    }



}
