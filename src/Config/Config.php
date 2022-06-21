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
        self::guardAgainstConfigReaderNotBeingInitialized();

        return self::$configReader->get($keys, $default, $delimiter);
    }

    /**
     * Sets a value in the loaded configuration
     *
     * @param string $key
     * @param mixed $value
     * @param string $delimiter
     */
    public static function write($key, $value, $delimiter = '.')
    {
        self::guardAgainstConfigReaderNotBeingInitialized();

        self::$configReader->set($key, $value, $delimiter);
    }

    /**
     * Throw an exception if a config reader has not been initialized
     *
     * @throws RuntimeException
     */
    protected static function guardAgainstConfigReaderNotBeingInitialized()
    {
        if ( ! self::$configReader) {
            throw new RuntimeException('No ConfigReader has been initialized.');
        }
    }
}
