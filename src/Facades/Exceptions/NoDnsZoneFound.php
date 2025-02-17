<?php

namespace Dolalima\Laravel\Dns\Facades\Exceptions;

class NoDnsZoneFound extends \Exception
{
    public function __construct($message = 'No DNS zone found', $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}
