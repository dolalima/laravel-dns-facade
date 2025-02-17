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
    protected $provider;
    protected $config;

    public function __construct(string $provider, array $config)
    {
        $this->provider = $provider;
        $this->config = $config;

        $key = new APIToken($config['key']);
        $this->adapter = new Guzzle($key);
    }

    public function listZones(): array
    {
        $zones = new Zones($this->adapter);
        $response = $zones->listZones();
        $list = [];
        foreach ($response->result as $zone) {
            $list[] = DnsZone::fromCloudFlare($zone, $this->provider);
        }
        return $list;
    }

    public function findZoneByName(string $name): ?DnsZone
    {
        $zones = new Zones($this->adapter);
        $zoneID = $zones->getZoneID($name);
        if (!$zoneID) {
            return null;
        }
        return DnsZone::fromCloudFlare($zones->getZoneById($zoneID)->result, $this->provider);
    }

    public function getZoneById($id): ?DnsZone
    {
        $zones = new Zones($this->adapter);
        return DnsZone::fromCloudFlare($zones->getZoneById($id)->result, $this->provider);
    }


    public function listRecords(DnsZone|string $zone = null, $type = '', $name = '', $content = ''): array
    {

        if (is_string($zone)) {
            $zone = $this->getZoneById($zone);
        }

        $list = [];
        $dns = new DNS($this->adapter);
        $records = $dns->listRecords($zone->id, $type, $type, $content);
        while (count($records->result) > 0) {
            foreach ($records->result as $record) {

                $list[] = $this->convertRecord($record, $zone);
            }
            $records = $dns->listRecords($zone->id, $type, $type, $content, $records->result_info->page + 1);
        }

        return $list;
    }

    public function findRecord(DnsZone|string $zone, $name = '', $type = ''): ?DnsRecord
    {
        $cloudflare = new DNS($this->adapter);

        if (is_string($zone)) {
            $zone = $this->getZoneById($zone);
        }

        $name = $name . '.' . $zone->name;

        $records = $cloudflare->listRecords($zone->id, $type, $name);

        while (count($records->result) > 0) {

            foreach ($records as $record) {

                $record = $record[0];
                if ($type && $record->type != $type) {
                    continue;
                }

                if ($record->name == $name) {
                    return DnsRecord::fromCloudFlare($record, $zone);
                }
            }
            $records = $records->next();
        }

        return null;
    }

    public function createRecord(DnsZone|string $zone, $name, $type, $content, $ttl = 300, $ssl_tunnel = false): DnsRecord
    {

        if (is_string($zone)) {
            $zone = $this->getZoneById($zone);
        }

        $exists = $this->findRecord($zone, $name, $type);
        if ($exists) {
            throw new ExceptionDnsRecordAlreadyExist();
        }

        $cloudflare = new DNS($this->adapter);

        return $cloudflare->addRecord($zone, $type, $name, $content, $ttl, $ssl_tunnel, $comment = 'created by Dns Manager');
    }

    public function updateRecord($zone, $name, $type, $content, $ttl)
    {
        $record = $this->findRecord($zone, $name);
        if ($record) {
            $cloudflare = new DNS($this->adapter);
            return $cloudflare->updateRecord($zone, $record->id, $type, $name, $content, $ttl);
        }
    }

    public function deleteRecord(DnsZone|string $zone, string $name): bool
    {

        if (is_string($zone)) {
            $zone = $this->getZoneById($zone);
        }

        $record = $this->findRecord($zone, $name);
        if ($record) {
            $cloudflare = new DNS($this->adapter);
            return $cloudflare->deleteRecord($zone, $record->id);
        }

        return false;

    }

    public function getRecord($zone, $name, $type)
    {
        $record = $this->findRecord($zone, $name, $type);

    }


    /**
     * @param $record
     * @return DnsRecord
     */
    public function convertRecord($record, $zone): DnsRecord
    {
        return DnsRecord::fromCloudFlare($record, $zone);
    }


}
