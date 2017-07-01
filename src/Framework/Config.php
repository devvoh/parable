<?php

namespace Parable\Framework;

class Config
{
    /** @var \Parable\Filesystem\Path */
    protected $path;

    /** @var array */
    protected $config = [];

    public function __construct(
        \Parable\Filesystem\Path $path
    ) {
        $this->path = $path;
    }

    /**
     * @param array $data
     * @param array $keys
     *
     * @return mixed
     */
    public function getNested(array &$data, array $keys)
    {
        foreach ($keys as $key) {
            $data = &$data[$key];
        }
        return $data;
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function get($key)
    {
        $config = $this->getConfig();
        if (strpos($key, '.') !== false) {
            return $this->getNested($config, explode('.', $key));
        }
        if (isset($config[$key])) {
            return $config[$key];
        }
        return null;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @return $this
     */
    public function load()
    {
        $configPath = $this->path->getDir("app/Config");

        if (!file_exists($configPath)) {
            // Quietly fail, since we don't require the path to exist
            return $this;
        }

        $dirIterator = new \RecursiveDirectoryIterator(
            $configPath,
            \RecursiveDirectoryIterator::SKIP_DOTS
        );
        $iteratorIterator = new \RecursiveIteratorIterator($dirIterator);

        $configClasses = [];
        foreach ($iteratorIterator as $file) {
            /** @var \SplFileInfo $file */
            $className = 'Config\\' . str_replace('.php', '', $file->getFilename());

            /** @var \Parable\Framework\Config\Base $configClass */
            try {
                $configClass = \Parable\DI\Container::get($className);

                // @codeCoverageIgnoreStart
                $configClasses[] = $configClass;
                // @codeCoverageIgnoreEnd
            } catch (\Exception $e) {
                // Just continue, this isn't a major problem.
            }
        }

        $this->addConfigs($configClasses);

        return $this;
    }

    /**
     * Add a Config file to the stack, ignoring sort order since the moment this is called decides where it fits.
     *
     * @param \Parable\Framework\Config\Base $config
     *
     * @return $this
     */
    public function addConfig(\Parable\Framework\Config\Base $config)
    {
        $this->config = array_merge($this->config, $config->getValues());
        return $this;
    }

    /**
     * @param \Parable\Framework\Config\Base[] $configClasses
     *
     * @return $this
     * @throws \Parable\Framework\Exception
     */
    public function addConfigs(array $configClasses)
    {
        $configs = [];
        foreach ($configClasses as $configClass) {
            $configClassName = get_class($configClass);
            if (!$configClass instanceof \Parable\Framework\Config\Base) {
                throw new \Parable\Framework\Exception(
                    "'{$configClassName}' does not implement '\Parable\Framework\Config\Base'"
                );
            }
            if (isset($configs[$configClass->getSortOrder()])) {
                throw new \Parable\Framework\Exception("Sort order duplication by '{$configClassName}'");
            }
            $configs[$configClass->getSortOrder()] = $configClass;
        }

        // Sort the array by key so the sortOrder is reflected in the actual order
        ksort($configs);

        // Now get all the values from the config classes
        foreach ($configs as $configClass) {
            $this->config = array_merge($this->config, $configClass->getValues());
        }

        return $this;
    }
}
