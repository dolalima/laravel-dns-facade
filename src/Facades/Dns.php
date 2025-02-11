<?php

namespace Dolalima\Laravel\Dns\Facades;


use Illuminate\Support\Facades\Facade;



/**
 * @see DnsManager
 */
class DnsManager extends Facade
{

    protected static function getFacadeAccessor()
    {
        return 'dns';
    }
}
