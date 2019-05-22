<?php

namespace Nbj\Config\Reader;

use Nbj\Config\Contract\ConfigReader;

class JsonReader extends ArrayReader implements ConfigReader
{
    /**
     * Loads all configuration files. Defaults to .php files in the configuration path given
     *
     * @param string $pathToConfigFiles
     * @param string $fileExtension
     */
    protected function loadConfigurationFiles($pathToConfigFiles, $fileExtension = '.json')
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

            $fileContent = file_get_contents($pathToConfigFiles . DIRECTORY_SEPARATOR . $file);

            $this->configs[$name] = json_decode($fileContent, true);
        }
    }
}
