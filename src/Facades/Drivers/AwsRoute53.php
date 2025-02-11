<?php

namespace Dolalima\Laravel\Dns\Facades\Drivers;


use Aws\Route53\Route53Client;
use \Dolalima\Laravel\Dns\Contracts\Dns\Dns as DnsContract;
use Dolalima\Laravel\Dns\Facades\Models\DnsRecord;
use Dolalima\Laravel\Dns\Facades\Models\DnsZone;

class AwsRoute53 implements DnsContract
{
    protected $route53;

    public function __construct($config){

        $this->route53 = new Route53Client([
            'credentials' => [
                'key' => $config['key'],
                'secret' => $config['secret']
            ],
        ]);
    }

    // List all hosted zones

    /**
     * @return array
     */
    public function listZones()
    {
        $zones = $this->route53->listHostedZones();
        $list = [];
        foreach ($zones['HostedZones'] as $zone) {
            $list[] = new DnsZone($zone['Name'], $zone['Id'], []);
        }
        return $list;
    }

    public function findZoneByName(string $name)
    {
        $name = $name . '.';
        $zones = $this->route53->listHostedZones();
        foreach ($zones['HostedZones'] as $zone) {
            if ($zone['Name'] == $name) {
                return new DnsZone($zone['Name'], $zone['Id'], []);
            }
        }
        return null;
    }

    public function getZoneById($id)
    {
        $zones = $this->route53->listHostedZones();
        foreach ($zones['HostedZones'] as $zone) {
            if ($zone['Id'] == $id) {
                return new DnsZone($zone['Name'], $zone['Id'], []);
            }
        }
        return null;
    }


    public function listRecords($zone = null)
    {
        $zone = $this->findZoneByName($zone);

        $records = $this->route53->listResourceRecordSets([
            'HostedZoneId' => $zone->id
        ]);

        $list = [];

        foreach ($records['ResourceRecordSets'] as $record) {
            $list[] = new DnsRecord($zone->name,$record['Name'], $record['Type'], $record['TTL'], $record['ResourceRecords'][0]['Value']);
        }

        return $list;
    }

    public function findRecord($zone, $name, $type = null)
    {

        $zone = $this->findZoneByName($zone);

        $name = $name . '.'.$zone->name;

        $result = $this->route53->listResourceRecordSets([
            'HostedZoneId' => $zone->id
        ]);



        $next = true;

        while ($next) {
            foreach ($result['ResourceRecordSets'] as $record) {
                if ($record['Name'] == $name) {
                    return new DnsRecord($zone->name,$record['Name'], $record['Type'], $record['TTL'], $record['ResourceRecords'][0]['Value']);
                }
            }
            if($result['IsTruncated']){
                $result = $this->route53->listResourceRecordSets([
                    'HostedZoneId' => $zone->id,
                    'StartRecordName' => $record['Name'],
                    'StartRecordType' => $record['Type']
                ]);
            }else{
                $next = false;
            }

        }



        return null;
    }

    public function createRecord($zone, $name, $type, $content, $ttl, $ssl_tunnel = false)
    {
        $zone = $this->findZoneByName($zone);

        $this->route53->changeResourceRecordSets([
            'HostedZoneId' => $zone->id,
            'ChangeBatch' => [
                'Changes' => [
                    [
                        'Action' => 'CREATE',
                        'ResourceRecordSet' => [
                            'Name' => $name . '.' . $zone->name,
                            'Type' => $type,
                            'TTL' => $ttl,
                            'ResourceRecords' => [
                                [
                                    'Value' => $content
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]);

        return new DnsRecord($zone->name,$name, $type, $ttl, $content);
    }


    public function updateRecord($zone, $name, $type, $content, $ttl)
    {
        $zone = $this->findZoneByName($zone);

        $this->route53->changeResourceRecordSets([
            'HostedZoneId' => $zone->id,
            'ChangeBatch' => [
                'Changes' => [
                    [
                        'Action' => 'UPSERT',
                        'ResourceRecordSet' => [
                            'Name' => $name . '.' . $zone->name,
                            'Type' => $type,
                            'TTL' => $ttl,
                            'ResourceRecords' => [
                                [
                                    'Value' => $content
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]);

        return new DnsRecord($zone->name,$name, $type, $ttl, $content);
    }

    public function deleteRecord($zone, $name)
    {
        // TODO: Implement deleteRecord() method.
    }


    public function getRecord($zone, $name, $type)
    {
        // TODO: Implement getRecord() method.
    }

    public function convertRecord($record): DnsRecord
    {
        return DnsRecord::fromAwsRoute53($record);
    }




}
