<?php

namespace Dolalima\Laravel\Dns\Contracts\Dns;

interface Factory
{
    /**
     * Get a dns implementation.
     *
     * @param string|null $name
     * @return \Dolalima\Laravel\Dns\Contracts\Dns\Dns
     */
    public function provider($name = null);


}
