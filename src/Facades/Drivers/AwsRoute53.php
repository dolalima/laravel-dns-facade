<?php

namespace Dolalima\Laravel\Dns\Facades\Drivers;


use Aws\Route53\Route53Client;
use \Dolalima\Laravel\Dns\Contracts\Dns\Dns as DnsContract;
use Dolalima\Laravel\Dns\Facades\DnsManager;
use Dolalima\Laravel\Dns\Facades\Models\DnsRecord;
use Dolalima\Laravel\Dns\Facades\Models\DnsZone;

class AwsRoute53 implements DnsContract
{
    protected $route53;
    protected $provider;
    protected $config;

    public function __construct(string $provider, array $config){

        $this->provider = $provider;
        $this->config = $config;

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
    public function listZones(): array
    {
        $zones = $this->route53->listHostedZones();
        $list = [];
        foreach ($zones['HostedZones'] as $zone) {
            $list[] = new DnsZone($zone['Name'], $zone['Id'], [],$this->provider);
        }
        return $list;
    }

    public function findZoneByName(string $name): ?DnsZone
    {
        $name = $name . '.';
        $zones = $this->route53->listHostedZones();
        foreach ($zones['HostedZones'] as $zone) {
            if ($zone['Name'] == $name) {
                return new DnsZone($zone['Name'], $zone['Id'], [],$this->provider);
            }
        }
        return null;
    }

    public function getZoneById($id): ?DnsZone
    {
        $zones = $this->route53->listHostedZones();
        foreach ($zones['HostedZones'] as $zone) {
            if ($zone['Id'] == $id) {
                return new DnsZone($zone['Name'], $zone['Id'], [],$this->provider);
            }
        }
        return null;
    }


    public function listRecords($zone = null) : array
    {
        $zone = $this->findZoneByName($zone);

        $records = $this->route53->listResourceRecordSets([
            'HostedZoneId' => $zone->id
        ]);

        $list = [];

        foreach ($records['ResourceRecordSets'] as $record) {
            $list[] = new DnsRecord($zone,$record['Name'],$record['Name'], $record['Type'], $record['TTL'], $record['ResourceRecords'][0]['Value']);
        }

        return $list;
    }

    public function findRecord(DnsZone|string $zone, $name, $type = null) : ?DnsRecord
    {

        if(is_string($zone)){
            $zone = $this->getZoneById($zone);
            }
        else {
            $zone = $zone;
        }


        $name = $name . '.'.$zone->name;

        $result = $this->route53->listResourceRecordSets([
            'HostedZoneId' => $zone->id
        ]);



        $next = true;

        while ($next) {
            foreach ($result['ResourceRecordSets'] as $record) {
                if ($record['Name'] == $name) {
                    return DnsRecord::fromAwsRoute53($record,$zone);
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

    public function createRecord(DnsZone|string $zone, string $name, string $type, string $content, int $ttl=300, bool $ssl_tunnel = false): DnsRecord
    {

        if(is_string($zone))
            $zone = $this->getZoneById($zone);
        else
            $zone = $zone;



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

        $new_record = $this->findRecord($zone->id, $name, $type);

        return $new_record;
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

    public function deleteRecord(DnsZone|string $zone, $name): bool
    {
       if(is_string($zone)){
           $zone = $this->getZoneById($zone);
       }

       $record = $this->findRecord($zone, $name);

       if($record){
           $this->route53->changeResourceRecordSets([
               'HostedZoneId' => $zone->id,
               'ChangeBatch' => [
                   'Changes' => [
                       [
                           'Action' => 'DELETE',
                           'ResourceRecordSet' => [
                               'Name' => $record->name,
                               'Type' => $record->type,
                               'TTL' => $record->ttl,
                               'ResourceRecords' => [
                                   [
                                       'Value' => $record->content
                                   ]
                               ]
                           ]
                       ]
                   ]
               ]
           ]);
           return true;
       }

       return false;

    }


    public function getRecord($zone, $name, $type)
    {
        // TODO: Implement getRecord() method.
    }

    public function convertRecord($record,$zone): DnsRecord
    {
        return DnsRecord::fromAwsRoute53($record,$zone);
    }




}
