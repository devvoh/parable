<?php
/**
 * @package     Parable Framework
 * @license     MIT
 * @author      Robin de Graaf <hello@devvoh.com>
 * @copyright   2015-2016, Robin de Graaf, devvoh webdevelopment
 */

namespace Parable\Framework;

class Config {

    /** @var \Parable\Filesystem\Path */
    protected $path;

    /** @var array */
    protected $config = [];

    /**
     * @param \Parable\Filesystem\Path $path
     */
    public function __construct(
        \Parable\Filesystem\Path $path
    ) {
        $this->path   = $path;
    }

    public function getNested(&$data, $keys) {
        foreach ($keys as $key) {
            $data = &$data[$key];
        }
        return $data;
    }

    public function get($key) {
        if (strpos($key, '.') !== false) {
            return $this->getNested($this->config, explode('.', $key));
        }
        return $this->config[$key];
    }

    public function load() {
        $dirIterator = new \RecursiveDirectoryIterator($this->path->getDir('app/Config'), \RecursiveDirectoryIterator::SKIP_DOTS);
        $iteratorIterator = new \RecursiveIteratorIterator($dirIterator);

        $configClasses = [];
        foreach ($iteratorIterator as $file) {
            /** @var \SplFileInfo $file */
            $className = 'Config\\' . str_replace('.php', '', $file->getFilename());

            if ($className === 'App') {
                continue;
            }

            /** @var \Parable\Framework\Interfaces\Config $configClass */
            $configClass = \Parable\DI\Container::get($className);
            if ($configClass->getSortOrder() === null) {
                array_push($configClasses, $configClass);
            } else {
                $configClasses[$configClass->getSortOrder()] = $configClass;
            }
        }

        // Sort the array by key so the sortOrder is reflected in the actual order
        ksort($configClasses);

        // Now get all the values from the config classes
        foreach ($configClasses as $configClass) {
            $this->config = array_merge($this->config, $configClass->getValues());
        }
    }

}
