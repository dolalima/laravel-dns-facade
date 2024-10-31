<?php

namespace Dolalima\Laravel\Dns\Facades\Models;

class DnsZoneModel
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

}
