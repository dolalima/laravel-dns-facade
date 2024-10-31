<?php

namespace Dolalima\Laravel\Dns\Facades;

interface DnsManagerInterface
{

    /**
     * List all zones
     * @return DnsZoneModel[]
     */
    public function listZones();

    /**
     * Find a zone by name
     * @param string $name
     * @return DnsRecordModel[]
     */
    public function listRecords($zone=null);

    /**
     * Find a zone by name
     * @param string $name
     * @return DnsRecordModel|null
     */
    public function findRecord($zone, $name, $type=null);

    /**
     * Find a zone by name
     * @param string $name
     * @return DnsZoneModel
     */
    public function createRecord($zone, $name, $type, $content, $ttl,$ssl_tunnel=false);

    /**
     * @param $zone
     * @param $name
     * @param $type
     * @param $content
     * @param $ttl
     * @return DnsRecordModel
     */
    public function updateRecord($zone, $name, $type, $content, $ttl);
    /**
     * Find a zone by name
     * @param string $name
     * @return DnsZoneModel
     */
    public function deleteRecord($zone, $name);

    /**
     * Find a zone by name
     * @param string $zone
     * @param string $name
     * @param string $type
     * @return DnsZoneModel|null
     */
    public function getRecord($zone, $name, $type);


    /**
     * Convert a record to a DnsRecordModel
     * @param mixed $record
     * @return DnsRecordModel
     */
    public function convertRecord($record): DnsRecordModel;




}
