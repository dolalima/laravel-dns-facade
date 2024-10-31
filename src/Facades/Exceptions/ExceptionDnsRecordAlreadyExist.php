<?php

namespace Dolalima\Laravel\Dns\Facades\Exceptions;

class ExceptionDnsRecordAlreadyExist extends \Exception
{
    public function __construct($message = 'DNS record already exist', $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}
