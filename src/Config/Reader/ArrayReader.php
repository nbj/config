<?php

namespace Nbj\Config\Reader;

use InvalidArgumentException;
use Nbj\Config\Contract\ConfigReader;

class ArrayReader implements ConfigReader
{
    /**
     * Holds all the loaded configuration files
     *
     * @var array $configs
     */
    protected $configs = [];

    /**
     * ArrayReader constructor.
     *
     * @param string $pathToConfigFiles
     */
    public function __construct($pathToConfigFiles)
    {
        $this->loadConfigurationFiles($pathToConfigFiles);
    }

    /**
     * Gets a value from the loaded configuration
     *
     * @param string $keys
     * @param mixed $default
     * @param string $delimiter
     *
     * @return mixed
     */
    public function get($keys, $default = null, $delimiter = '.')
    {
        // Checks that $keys is a non-empty string
        // Otherwise the $default value is returned
        if ( ! is_string($keys) || empty($keys)) {
            return $default;
        }

        // If keys does not contain the delimiter
        // Check whether the key has a value or
        // return the default value
        if (strpos($keys, $delimiter) === false) {
            return array_key_exists($keys, $this->configs)
                ? $this->configs[$keys]
                : $default;
        }

        // Split up the keys string into individual keys
        // And copy the config array to a local variable
        $keys = explode($delimiter, $keys);
        $configArray = $this->configs;

        // Traverse all the keys
        foreach ($keys as $key) {
            if ( ! array_key_exists($key, $configArray)) {
                return $default;
            }

            $configArray = $configArray[$key];
        }

        return $configArray;
    }

    /**
     * Sets a value in the loaded configuration
     *
     * @param string $key
     * @param mixed $value
     * @param string $delimiter
     *
     * @return $this
     */
    public function set($key, $value, $delimiter = '.')
    {
        // Checks that $key is a non-empty string
        // Otherwise throw an exception
        if ( ! is_string($key) || empty($key)) {
            throw new InvalidArgumentException('The given key is invalid. It must be a non-empty string');
        }

        // If the key does not contain the delimiter
        // Simply write the value to the key
        if (strpos($key, $delimiter) === false) {
            $this->configs[$key] = $value;

            return $this;
        }

        // Split the key string into separate keys based
        // on the delimiter. Makes sure to always keep
        // a reference to the correct position in
        // the configuration array structure
        $keys = explode($delimiter, $key);
        $currentArrayStructure = &$this->configs;

        foreach ($keys as $key) {
            $currentArrayStructure = &$currentArrayStructure[$key];
        }

        $currentArrayStructure = $value;

        return $this;
    }

    /**
     * Loads all configuration files. Defaults to .php files in the configuration path given
     *
     * @param string $pathToConfigFiles
     * @param string $fileExtension
     */
    protected function loadConfigurationFiles($pathToConfigFiles, $fileExtension = '.php')
    {
        $this->guardAgainstConfigurationFilePathNotExisting($pathToConfigFiles);

        // Scan the configuration for all files
        $files = scandir($pathToConfigFiles);

        // Filter files down to only php files
        $files = array_filter($files, function ($file) use ($fileExtension) {
            return strpos($file, $fileExtension) !== false;
        });

        // Resolve each configuration file into the configs array
        foreach ($files as $file) {
            list($name) = explode('.', $file);

            $this->configs[$name] = require $pathToConfigFiles . DIRECTORY_SEPARATOR . $file;
        }
    }

    /**
     * Guards against the path to configuration files not existing
     *
     * @param string $pathToConfigFiles
     */
    protected function guardAgainstConfigurationFilePathNotExisting($pathToConfigFiles): void
    {
        if ( ! file_exists($pathToConfigFiles)) {
            $message = sprintf('Path to configuration files does not exist: %s', $pathToConfigFiles);

            throw new InvalidArgumentException($message);
        }
    }
}
