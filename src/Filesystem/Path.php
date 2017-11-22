<?php

namespace Parable\Filesystem;

class Path
{
    /** @var string */
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
        $this->baseDir = rtrim($baseDir, DS);
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
     * Get dir based on the base dir.
     *
     * @param string $directory
     *
     * @return string
     */
    public function getDir($directory)
    {
        $directory = str_replace('/', DS, $directory);
        if (!file_exists($directory)) {
            $directory = $this->getBaseDir() . DS . ltrim($directory, DS);
        }
        return $directory;
    }
}
