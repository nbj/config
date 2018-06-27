<?php

namespace Nbj\Config;

use RuntimeException;
use Nbj\Config\Contract\ConfigReader;

class Config
{
    /**
     * Holds an instance of a ConfigReader
     *
     * @var ConfigReader $configReader
     */
    protected static $configReader;

    /**
     * Initializes a ConfigReader
     *
     * @param ConfigReader $configReader
     */
    public static function init(ConfigReader $configReader)
    {
        self::$configReader = $configReader;
    }

    /**
     * Reads a value from the loaded configuration
     *
     * @param string $keys
     * @param mixed $default
     * @param string $delimiter
     *
     * @return mixed
     */
    public static function read($keys, $default = null, $delimiter = '.')
    {
        if (!self::$configReader) {
            throw new RuntimeException('No ConfigReader has been initialized.');
        }

        return self::$configReader->get($keys, $default, $delimiter);
    }
}
