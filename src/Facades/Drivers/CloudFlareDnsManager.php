<?php

namespace Dolalima\Laravel\Dns\Facades\Drivers;

use App\Facades\Dns\DnsManagerInterface;
use App\Facades\Dns\DnsRecordModel;
use App\Facades\Dns\Exceptions\ExceptionDnsRecordAlreadyExist;
use Cloudflare\API\Auth\APIToken;
use Cloudflare\API\Adapter\Guzzle;
use Cloudflare\API\Endpoints\Zones;
use Cloudflare\API\Endpoints\DNS;

class CloudFlareDnsManager implements DnsManagerInterface
{

    protected $adapter;

    public function __construct()
    {
        $key = new APIToken(env('CLOUDFLARE_API_TOKEN'));
        $this->adapter = new Guzzle($key);
    }

    public function listZones()
    {
        $zones = new Zones($this->adapter);

        return $zones->listZones();
    }

    public function findZoneByName($zone)
    {
        $zones = new Zones($this->adapter);

        return $zones->getZoneID($zone);
    }

    public function listRecords($zone = null)
    {
        $list = [];
        $dns = new DNS($this->adapter);
        $records = $dns->listRecords($zone);
        while (count($records->result) > 0) {
            foreach ($records as $record) {
                $list[] = $this->convertRecord($record[0]);
            }
            $records = $records->next();
        }

        return $list;
    }

    public function findRecord($zone, $name='', $type='')
    {
        $dns = new DNS($this->adapter);

        $records = $dns->listRecords($zone, $type, $name);

        while (count($records->result) > 0) {

            foreach ($records as $record) {

                $record = $record[0];
                if($type && $record->type != $type) {
                    continue;
                }

                if ($record->name == $name ){
                    return $record;
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

        return $dns->addRecord($zone, $type, $name, $content, $ttl,$ssl_tunnel,$comment='created by Weebet Config Service');
    }

    public function updateRecord($zone, $name, $type, $content, $ttl)
    {
        // TODO: Implement updateRecord() method.
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


    public function convertRecord($record): DnsRecordModel
    {
        return DnsRecordModel::fromCloudFlare($record);
    }


}
