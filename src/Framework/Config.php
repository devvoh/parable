<?php

namespace Parable\Framework;

class Config
{
    /** @var string */
    protected $mainConfigClass = \Config\App::class;

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
     * @param string $className
     *
     * @return $this
     * @throws Exception
     */
    public function setMainConfigClassName($className)
    {
        if (!class_exists($className)) {
            throw new \Parable\Framework\Exception("Main Config class '{$className}' does not exist.");
        }
        $this->mainConfigClass = $className;
        return $this;
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
        $config = $this->getAll();
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
    public function getAll()
    {
        return $this->config;
    }

    /**
     * @return $this
     */
    public function load()
    {
        try {
            $this->addConfig(\Parable\DI\Container::get($this->mainConfigClass));
        } catch (\Exception $e) {
            // We don't throw here since the file isn't required to exist, but we do stop here
            return $this;
        }

        if ($this->get("parable.configs")) {
            foreach ($this->get("parable.configs") as $configClass) {
                $this->addConfig(\Parable\DI\Container::get($configClass));
            }
        }

        return $this;
    }

    /**
     * @param \Parable\Framework\Interfaces\Config $config
     *
     * @return $this
     */
    public function addConfig(\Parable\Framework\Interfaces\Config $config)
    {
        $this->config = array_merge($this->config, $config->get());
        return $this;
    }
}
