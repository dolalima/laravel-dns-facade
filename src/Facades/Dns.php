<?php

namespace Dolalima\Laravel\Dns\Facades;


use Dolalima\Laravel\Dns\Facades\Models\DnsZone;
use Illuminate\Support\Facades\Facade;



/**
 * @method static Dolalima\Laravel\Dns\Contracts\Dns\Dns listZones(string)
 * @method static Dolalima\Laravel\Dns\Contracts\Dns\Dns findZoneByName(string)
 * @method static Dolalima\Laravel\Dns\Contracts\Dns\Dns listRecords(string)
 * @method static Dolalima\Laravel\Dns\Contracts\Dns\Dns findRecord(string $zone, string $name, string|null $type)
 * @method static Dolalima\Laravel\Dns\Contracts\Dns\Dns createRecord(DnsZone|string $zone, string $name, string $type, string $content, int $ttl=300, bool $ssl_tunnel = false)
 * @method static provider(string $provider)
 * @see DnsManager
 */
class Dns extends Facade
{

    protected static function getFacadeAccessor()
    {
        return 'dns';
    }
}
