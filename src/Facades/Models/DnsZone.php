<?php

namespace Dolalima\Laravel\Dns\Facades\Models;

class DnsZone
{
    public $name;
    public $id;
    public $records;

    public function __construct($name, $id, $records)
    {
        $this->name = $name;
        $this->id = $id;
        $this->records = $records;
    }

    public function addRecord($record)
    {
        $this->records[] = $record;
    }

    public function removeRecord($record)
    {
        $index = array_search($record, $this->records);
        if ($index !== false) {
            unset($this->records[$index]);
        }
    }

    public static function fromCloudFlare($data): DnsZone
    {
        $zone = new DnsZone($data->name, $data->id, []);
        if(!isset($data->records)) return $zone;
        foreach ($data->records as $record) {
            $zone->addRecord(DnsRecord::fromCloudFlare($record));
        }
        return $zone;
    }

    public static function fromRoute53($data): DnsZone
    {
        $zone = new DnsZone($data->Name, $data->Id, []);
        if(!isset($data->Records)) return $zone;
        foreach ($data->Records as $record) {
            $zone->addRecord(DnsRecord::fromRoute53($record));
        }
        return $zone;
    }

}
