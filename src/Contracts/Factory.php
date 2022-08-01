<?php

namespace Farsh4d\Bank\Contracts;


interface Factory
{
    /**
     * Get an Gateway provider implementation.
     *
     * @param string $driver
     * @return Driver
     */
    public function driver($driver = null);
}
