<?php

namespace Dolalima\Laravel\Dns\Facades\Models;

class DnsRecord
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

    public static function fromArray($data): DnsRecord
    {
        return new DnsRecord($data['zone'], $data['name'], $data['type'], $data['content'], $data['ttl']);
    }

    /**
     * @param $data
     * @return DnsRecord
     */
    public static function fromCloudFlare($data,$zone=null): DnsRecord
    {
        return new DnsRecord(null, $data->name, $data->type, $data->content, $data->ttl);
    }

    /**
     * @param $data
     * @return DnsRecord
     */
    public static function fromAwsRoute53($data): DnsRecord
    {
        return new DnsRecord($data['zone'], $data['name'], $data['type'], $data['content'], $data['ttl']);
    }

}
