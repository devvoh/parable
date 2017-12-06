<?php

namespace Parable\Filesystem;

class Path
{
    /** @var string|null */
    protected $baseDir;

    /**
     * Set the base dir to base all further paths on.
     *
     * @param string $baseDir
     *
     * @return $this
     */
    public function setBaseDir($baseDir)
    {
        $this->baseDir = rtrim($baseDir, DIRECTORY_SEPARATOR);
        return $this;
    }

    /**
     * Return the base dir.
     *
     * @return string
     */
    public function getBaseDir()
    {
        return $this->baseDir;
    }

    /**
     * Return dir based on the base dir.
     *
     * @param string $directory
     *
     * @return string
     */
    public function getDir($directory)
    {
        $directory = str_replace('/', DIRECTORY_SEPARATOR, $directory);
        if (strpos($directory, $this->getBaseDir()) === false || !file_exists($directory)) {
            $directory = $this->getBaseDir() . DIRECTORY_SEPARATOR . ltrim($directory, DIRECTORY_SEPARATOR);
        }
        return $directory;
    }
}
