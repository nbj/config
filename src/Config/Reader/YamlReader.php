<?php

namespace Nbj\Config\Reader;

use Nbj\Config\Contract\ConfigReader;

class YamlReader extends ArrayReader implements ConfigReader
{
    /**
     * Loads all configuration files. Defaults to .php files in the configuration path given
     *
     * @param string $pathToConfigFiles
     * @param string $fileExtension
     */
    protected function loadConfigurationFiles($pathToConfigFiles, $fileExtension = '.yml|.yaml')
    {
        $this->guardAgainstConfigurationFilePathNotExisting($pathToConfigFiles);

        // Scan the configuration for all files
        $files = scandir($pathToConfigFiles);

        // Holds all the file extensions to check for
        $fileExtensions = [];

        if (strpos($fileExtension, '|') !== false) {
            $fileExtensions = explode('|', $fileExtension);
        }

        if (empty($fileExtensions)) {
            $fileExtensions[] = $fileExtension;
        }

        // Filter files down to only files with extensions equal to $fileExtension
        $files = array_filter($files, function ($file) use ($fileExtensions) {
            foreach ($fileExtensions as $extension) {
                if (strpos($file, $extension) !== false) {
                    return true;
                }
            }

            return false;
        });

        // Resolve each configuration file into the configs array
        foreach ($files as $file) {
            list($name) = explode('.', $file);

            $fileContent = file_get_contents($pathToConfigFiles . DIRECTORY_SEPARATOR . $file);

            $this->configs[$name] = yaml_parse($fileContent);
        }
    }
}
