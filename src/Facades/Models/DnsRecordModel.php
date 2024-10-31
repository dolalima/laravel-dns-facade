<?php

namespace Dolalima\Laravel\Dns\Facades\Models;

class DnsRecordModel
{
    public $zone;
    public $name;
    public $type;
    public $content;
    public $ttl;

    public function __construct($zone, $name, $type, $content, $ttl)
    {
        $this->zone = $zone;
        $this->name = $name;
        $this->type = $type;
        $this->content = $content;
        $this->ttl = $ttl;
    }

    public static function fromArray($data): DnsRecordModel
    {
        return new DnsRecordModel($data['zone'], $data['name'], $data['type'], $data['content'], $data['ttl']);
    }

    /**
     * @param $data
     * @return DnsRecordModel
     */
    public static function fromCloudFlare($data): DnsRecordModel
    {
        return new DnsRecordModel($data['zone'], $data['name'], $data['type'], $data['content'], $data['ttl']);
    }

    /**
     * @param $data
     * @return DnsRecordModel
     */
    public static function fromAwsRoute53($data): DnsRecordModel
    {
        return new DnsRecordModel($data['zone'], $data['name'], $data['type'], $data['content'], $data['ttl']);
    }

}
