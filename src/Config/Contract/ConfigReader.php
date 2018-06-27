<?php

namespace Nbj\Config\Contract;

interface ConfigReader
{
    /**
     * Gets a value from the loaded configuration
     *
     * @param string $keys
     * @param mixed $default
     * @param string $delimiter
     *
     * @return mixed
     */
    public function get($keys, $default = null, $delimiter = '.');
}
