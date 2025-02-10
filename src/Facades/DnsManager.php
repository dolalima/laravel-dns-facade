<?php

namespace Dolalima\Laravel\Dns\Facades;


use Aws\Route53\Route53Client;
use Dolalima\Laravel\Dns\Contracts\Dns\Dns;
use Dolalima\Laravel\Dns\Contracts\Dns\Factory as FactoryContract;
use Dolalima\Laravel\Dns\Facades\Drivers\AwsRoute53;
use Dolalima\Laravel\Dns\Facades\Drivers\CloudFlare;
use Illuminate\Support\Facades\Facade;



/**
 * @mixin \Dolalima\Laravel\Dns\Contracts\Dns\Dns
 */
class DnsManager implements FactoryContract
{

    /**
     * The application instance.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * The array of resolved dsn provider.
     *
     * @var array
     */
    protected $providers = [];

    public function __construct($app)
    {
        $this->app = $app;
    }

    public function provider($name = null)
    {
        $name = $name ?: $this->getDefaultDriver();

        return $this->providers[$name] = $this->get($name);
    }

    /**
     * Get the dns connection configuration.
     *
     * @param  string  $name
     * @return array
     */
    protected function getConfig($name){
        return $this->app['config']["dns.providers.$name"]?: [];
    }


    /**
     * Get the default provider name.
     *
     * @return string
     */
    protected function getDefaultDriver()
    {
        return $this->app['config']['dns.default'];
    }

    /**
     * Attempt to get the provider from the local cache.
     *
     * @param $name
     * @return Dns
     */
    public function get($name)
    {
        return $this->providers[$name] = $this->resolve($name);
    }

    /**
     * Set the given provider instance.
     *
     * @param  string  $name
     * @param  mixed  $provider
     * @return $this
     */
    public function set($name, $provider)
    {
        $this->providers[$name] = $provider;
    }

    /**
     * Resolve the given provider.
     *
     * @param $name
     * @return Dns
     *
     * @throws \InvalidArgumentException
     */
    protected function resolve($name)
    {
        $config = $this->getConfig($name);

        $name = $config['driver'];

        $driverMethod = 'create'.ucfirst($name).'Driver';

        if (method_exists($this, $driverMethod)) {
            return $this->{$driverMethod}($config);
        } else {
            throw new InvalidArgumentException("Driver [{$name}] is not supported.");
        }

    }

    public function createRoute53Driver($config)
    {
        return new AwsRoute53($config);
    }

    public function createCloudflareDriver($config)
    {
        return new CloudFlare($config);
    }

    /**
     * Dynamically call the default driver instance.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return $this->provider()->$method(...$parameters);
    }
}
