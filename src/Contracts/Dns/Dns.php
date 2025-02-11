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
    public function listZones();

    /**
     * Find a zone by name
     * @param string $name
     * @return DnsRecord[]
     */
    public function listRecords($zone = null);

    /**
     * Find a zone by name
     * @param string $name
     * @return DnsRecord|null
     */
    public function findRecord($zone, $name, $type = null);

    /**
     * Find a zone by name
     * @param string $name
     * @return DnsZone
     */
    public function createRecord($zone, $name, $type, $content, $ttl, $ssl_tunnel = false);

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
     * @param string $name
     * @return DnsZone
     */
    public function deleteRecord($zone, $name);

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
    public function convertRecord($record): DnsRecord;

}
