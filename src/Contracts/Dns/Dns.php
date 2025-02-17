<?php

namespace Dolalima\Laravel\Dns\Contracts\Dns;

use Dolalima\Laravel\Dns\Facades\Models\DnsZone;
use Dolalima\Laravel\Dns\Facades\Models\DnsRecord;

interface Dns
{

    /**
     * List all zones
     * @return DnsZone[]
     */
    public function listZones(): array;

    /**
     * Find a zone by id
     * @param string|int $id
     * @return ?DnsZone
     */
    public function getZoneById(string|int $id): ?DnsZone;

    /**
     * Find a zone by name
     * @param string $name
     * @return ?DnsZone
     */
    public function findZoneByName(string $name): ?DnsZone;

    /**
     * Find a zone by name
     * @param string $name
     * @return DnsRecord[]
     */
    public function listRecords(DnsZone|string $zone = null): array;

    /**
     * Find a zone by name
     * @return DnsRecord|null
     */
    public function findRecord(DnsZone|string $zone, string $name, string $type = null): ?DnsRecord;

    /**
     * Find a zone by name
     * @return DnsRecord
     */
    public function createRecord(DnsZone $zone, string $name, string $type, string $content, int $ttl=300, bool $ssl_tunnel = false):DnsRecord;

    /**
     * @param $zone
     * @param $name
     * @param $type
     * @param $content
     * @param $ttl
     * @return DnsRecord
     */
    public function updateRecord($zone, $name, $type, $content, $ttl);

    /**
     * Find a zone by name
     * @param DnsZone|string $name
     * @param string $name
     * @return bool
     */
    public function deleteRecord(DnsZone|string $zone, string $name):bool;

    /**
     * Find a zone by name
     * @param string $zone
     * @param string $name
     * @param string $type
     * @return DnsZone|null
     */
    public function getRecord($zone, $name, $type);


    /**
     * Convert a record to a DnsRecord
     * @param mixed $record
     * @return DnsRecord
     */
    public function convertRecord($record,$zone): DnsRecord;

}
