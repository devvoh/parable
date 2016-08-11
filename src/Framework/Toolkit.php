<?php
/**
 * @package     Parable Framework
 * @license     MIT
 * @author      Robin de Graaf <hello@devvoh.com>
 * @copyright   2015-2016, Robin de Graaf, devvoh webdevelopment
 */

namespace Parable\Framework;

class Toolkit {

    /** @var \Parable\Filesystem\Path */
    protected $path;

    /** @var array */
    protected $resourceMap;

    /**
     * @param \Parable\Filesystem\Path $path
     */
    public function __construct(
        \Parable\Filesystem\Path $path
    ) {
        $this->path = $path;
    }

    /**
     * Load the resource map
     *
     * @return $this
     */
    public function loadResourceMap() {
        $dirIterator = new \RecursiveDirectoryIterator($this->path->getDir('vendor/devvoh/parable/src'), \RecursiveDirectoryIterator::SKIP_DOTS);
        $iteratorIterator = new \RecursiveIteratorIterator($dirIterator);

        foreach ($iteratorIterator as $path => $file) {
            /** @var \SplFileInfo $file */

            /*
             * Specifically exclude all non-php files and Bootstrap, since it will attempt to register everything again
             * and isn't a class anyway.
             */
            if (
                $file->getFilename() === 'Bootstrap.php'
                || $file->getFilename() === 'able.php'
                || $file->getExtension() !== 'php'
                || strpos($file->getRealPath(), '/Cli/') !== false
            ) {
                continue;
            }

            $className = str_replace('.' . $file->getExtension(), '', $file->getFilename());

            $fullClassName = str_replace($this->path->getDir('vendor/devvoh/parable/src'), '', $file->getRealPath());
            $fullClassName = str_replace('.' . $file->getExtension(), '', $fullClassName);
            $fullClassName = str_replace('/', '\\', 'Parable' . $fullClassName);

            $reflectionClass = new \ReflectionClass($fullClassName);
            if (!$reflectionClass->isInstantiable()) {
                continue;
            }

            $this->resourceMap[$className] = $fullClassName;
        }
        return $this;
    }

    /**
     * Return a mapping
     *
     * @param string $index
     *
     * @return null|string
     */
    public function getResourceMapping($index) {
        if (!$this->resourceMap) {
            $this->loadResourceMap();
        }
        if (isset($this->resourceMap[$index])) {
            return $this->resourceMap[$index];
        }
        return null;
    }

    /**
     * Create a repository to work with model of type $modelName (full namespaced name)
     *
     * @param string $modelName
     *
     * @return \Parable\ORM\Repository
     */
    public function getRepository($modelName) {
        $model = \Parable\DI\Container::create($modelName);

        /** @var \Parable\ORM\Repository $repository */
        $repository = \Parable\DI\Container::create(\Parable\ORM\Repository::class);

        $repository->setModel($model);
        return $repository;
    }

}
