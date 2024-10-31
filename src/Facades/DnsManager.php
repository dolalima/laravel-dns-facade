<?php

namespace Dolalima\Laravel\Dns\Facades;


use Illuminate\Support\Facades\Facade;

class DnsManager extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'dns';
    }
}
