<?php

namespace Dolalima\Laravel\Dns\Facades\Models;

use Dolalima\Laravel\Dns\Facades\Dns;

class DnsZone
{
    public $provider;
    public $name;
    public $id;
    public $records;

    public function __construct($name, $id, $records = [],$provider)
    {
        $this->name = $name;
        $this->id = $id;
        $this->records = $records;
        $this->provider = $provider;
    }

    /**
     * List all records from a zone
     * @return DnsRecord
     */
    public function getRecord(string|DnsRecord $record)
    {
        if (is_string($record)) {
            $record_id = $record;
        } else {
            $record_id = $record->id;
        }
        return Dns::provider($this->provider)->findRecord($this->id, $record_id);
    }

    /**
     * List all records from a zone
     * @return DnsRecord[]
     */
    public function listRecords()
    {
        return Dns::provider($this->provider)->listRecords($this);
    }


    public function findRecord(string $name, string $type = ''): ?DnsRecord
    {
        return Dns::provider($this->provider)->findRecord($this->id, $name, $type);
    }


    public function createRecord($name, $type, $content, $ttl=300, $ssl_tunnel = false): DnsRecord
    {
        return Dns::provider($this->provider)->createRecord($this, $name, $type, $content, $ttl, $ssl_tunnel);
    }

    public function removeRecord($record)
    {
        $index = array_search($record, $this->records);
        if ($index !== false) {
            unset($this->records[$index]);
        }
    }

    public static function fromCloudFlare($data,$provider): DnsZone
    {
        $zone = new DnsZone($data->name, $data->id, [],$provider);
        if (!isset($data->records)) return $zone;
        foreach ($data->records as $record) {
            $zone->addRecord(DnsRecord::fromCloudFlare($record,$zone));
        }
        return $zone;
    }

    public static function fromRoute53($data,$provider): DnsZone
    {
        $zone = new DnsZone($data->Name, $data->Id, [],$provider);
        if (!isset($data->Records)) return $zone;
        foreach ($data->Records as $record) {
            $zone->addRecord(DnsRecord::fromRoute53($record));
        }
        return $zone;
    }

}
