<?php

namespace Dolalima\Laravel\Dns\Facades\Drivers;

use Dolalima\Laravel\Dns\Facades\DnsManagerInterface;
use Aws\Route53\Route53Client;

class Route53DnsManager implements DnsManagerInterface
{
    protected $route53;

    public function __construct(){
        $this->route53 = new Route53Client([
            'version' => 'latest',
            'region' => 'us-east-1'
        ]);
    }

    // List all hosted zones

    /**
     * @return array
     */
    public function listZones()
    {
        $zones = $this->route53->listHostedZones();
        return $zones;
    }

    public function listRecords($zone = null)
    {
        // TODO: Implement listRecords() method.
    }

    public function findRecord($zone, $name, $type = null)
    {
        // TODO: Implement findRecord() method.
    }

    public function createRecord($zone, $name, $type, $content, $ttl)
    {
        // TODO: Implement createRecord() method.
    }

    public function updateRecord($zone, $name, $type, $content, $ttl)
    {
        // TODO: Implement updateRecord() method.
    }

    public function deleteRecord($zone, $name,)
    {
        // TODO: Implement deleteRecord() method.
    }

    public function getRecord($zone, $name, $type)
    {
        // TODO: Implement getRecord() method.
    }
}
