<?php

namespace Dolalima\Laravel\Dns\Facades\Drivers;


use Cloudflare\API\Auth\APIKey;
use Cloudflare\API\Auth\APIToken;
use Cloudflare\API\Adapter\Guzzle;
use Cloudflare\API\Endpoints\User;
use Cloudflare\API\Endpoints\Zones;
use Cloudflare\API\Endpoints\DNS;
use Dolalima\Laravel\Dns\Facades\DnsManagerInterface;
use Dolalima\Laravel\Dns\Facades\Models\DnsRecord;
use Dolalima\Laravel\Dns\Facades\Models\DnsZone;
use Dolalima\Laravel\Dns\Facades\Exceptions\ExceptionDnsRecordAlreadyExist;
use \Dolalima\Laravel\Dns\Contracts\Dns\Dns as DnsContract;

class CloudFlare implements DnsContract
{

    protected $adapter;
    protected $user;

    public function __construct($config)
    {

        $key = new APIToken($config['key']);
        $this->adapter = new Guzzle($key);
    }

    public function listZones()
    {
        $zones = new Zones($this->adapter);
        $response = $zones->listZones();
        $list = [];
        foreach ($response->result as $zone) {
            $list[] = DnsZone::fromCloudFlare($zone);
        }
        return $list;
    }

    public function findZoneByName(string $name)
    {
        $zones = new Zones($this->adapter);
        $zoneID = $zones->getZoneID($name);
        if(!$zoneID) {
            return null;
        }
        return DnsZone::fromCloudFlare($zones->getZoneById($zoneID)->result);
    }

    public function getZoneById($id)
    {
        $zones = new Zones($this->adapter);
        return DnsZone::fromCloudFlare($zones->getZoneById($id)->result);
    }


    public function listRecords($zone = null, $type = '', $name = '',$content='')
    {
        $list = [];
        $dns = new DNS($this->adapter);
        $records = $dns->listRecords($zone,$type ,$type,$content);
        while (count($records->result) > 0) {
            foreach ($records->result as $record) {

                $list[] = $this->convertRecord($record);
            }
            $records = $dns->listRecords($zone,$type ,$type,$content,$records->result_info->page + 1);
        }

        return $list;
    }

    public function findRecord($zone, $name='', $type='')
    {
        $dns = new DNS($this->adapter);

        $zone = $this->getZoneById($zone);
        $name = $name . '.' . $zone->name;

        $records = $dns->listRecords($zone->id, $type, $name);

        while (count($records->result) > 0) {

            foreach ($records as $record) {

                $record = $record[0];
                if($type && $record->type != $type) {
                    continue;
                }

                if ($record->name == $name ){
                    return DnsRecord::fromCloudFlare($record, $zone->name);
                }
            }
            $records = $records->next();
        }

        return null;
    }

    public function createRecord($zone, $name, $type, $content, $ttl=300,$ssl_tunnel=false)
    {

        $exists = $this->findRecord($zone, $name, $type);
        if($exists) {
            throw new ExceptionDnsRecordAlreadyExist();
        }

        $dns = new DNS($this->adapter);

        return $dns->addRecord($zone, $type, $name, $content, $ttl,$ssl_tunnel,$comment='created by Dns Manager');
    }

    public function updateRecord($zone, $name, $type, $content, $ttl)
    {
        $record = $this->findRecord($zone, $name);
        if($record){
            $dns = new DNS($this->adapter);
            return $dns->updateRecord($zone, $record->id, $type, $name, $content, $ttl);
        }
    }

    public function deleteRecord($zone, $name)
    {
        $record = $this->findRecord($zone, $name);
        if($record){
            $dns = new DNS($this->adapter);
            return $dns->deleteRecord($zone, $record->id);
        }

    }

    public function getRecord($zone, $name, $type)
    {
        // TODO: Implement getRecord() method.
    }


    /**
     * @param $record
     * @return DnsRecord
     */
    public function convertRecord($record): DnsRecord
    {
        return DnsRecord::fromCloudFlare($record);
    }


}
