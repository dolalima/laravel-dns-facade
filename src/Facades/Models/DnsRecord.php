<?php

namespace Dolalima\Laravel\Dns\Facades\Models;

use Dolalima\Laravel\Dns\Facades\Dns;

class DnsRecord
{
    public $zone;
    public $id;
    public $name;
    public $type;
    public $content;
    public $ttl;

    public function __construct(DnsZone $zone,$id, $name, $type, $content, $ttl)
    {
        $this->zone = $zone;
        $this->id = $id;
        $this->name = $name;
        $this->type = $type;
        $this->content = $content;
        $this->ttl = $ttl;
    }

    public function updateName($name)
    {
        $this->name = $name;

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
        return new DnsRecord($zone,$data->id, $data->name, $data->type, $data->content, $data->ttl);
    }

    /**
     * @param $data
     * @return DnsRecord
     */
    public static function fromAwsRoute53($data,$zone = null): DnsRecord
    {
        return new DnsRecord($zone,$data['Name'], $data['Name'], $data['Type'], $data['ResourceRecords'][0]['Value'], $data['TTL']);
    }

    public function delete()
    {
        Dns::provider($this->zone->provider)->deleteRecord($this->zone, $this->id);
    }

}
