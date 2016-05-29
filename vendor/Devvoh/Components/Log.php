<?php
/**
 * @package     Devvoh Components
 * @license     MIT
 * @author      Robin de Graaf <hello@devvoh.com>
 * @copyright   2015-2016, Robin de Graaf, devvoh webdevelopment
 */

namespace Devvoh\Components;

class Log {

    /** @var string */
    protected $path           = 'var/log';

    /** @var string */
    protected $defaultLogFile = 'parable.log';

    /** @var int */
    protected $mode           = 0777;

    /**
     * Sets the mode (octal)
     *
     * @param int $mode
     *
     * @return $this
     */
    public function setMode($mode) {
        if (decoct(octdec($mode)) == $mode) {
            $this->mode = $mode;
        }
        return $this;
    }

    /**
     * Return the mode (octal)
     *
     * @return int
     */
    public function getMode() {
        return $this->mode;
    }

    /**
     * Set the default log file
     *
     * @param string $defaultLogFile
     *
     * @return $this
     */
    public function setDefaultLogFile($defaultLogFile) {
        $this->defaultLogFile = $defaultLogFile;
        return $this;
    }

    /**
     * Return the log file name
     *
     * @return string
     */
    public function getDefaultLogFile() {
        return $this->defaultLogFile;
    }

    /**
     * Set the path to write to
     *
     * @param string $path
     *
     * @return $this
     */
    public function setPath($path) {
        $this->path = $path;
        return $this;
    }

    /**
     * Returns the path, and if it doesn't exist, attempts to create it
     *
     * @return string
     * @throws \Devvoh\Components\Exception
     */
    public function getPath() {
        if (!is_dir($this->path)) {
            // Create directory
            $created = @mkdir($this->path, $this->getMode(), true);
            if (!$created) {
                throw new \Devvoh\Components\Exception('Could not create log directory: ' . $this->path);
            }
        }
        if (!is_writable($this->path)) {
            $chmod = chmod($this->path, $this->getMode());
            if (!$chmod) {
                throw new \Devvoh\Components\Exception('Log directory is not writable: ' . $this->path);
            }
        }
        return $this->path;
    }

    /**
     * Write message to the log file
     *
     * @param mixed $message
     * @param null  $logFile
     * @param bool  $showTimezone
     *
     * @return $this
     * @throws \Devvoh\Components\Exception
     */
    public function write($message, $logFile = null, $showTimezone = false) {
        if (!$logFile) {
            $logFile = $this->getDefaultLogFile();
        }
        $logPath = rtrim($this->getPath(), DS) . DS . $logFile;

        // Prepare message
        $message = rtrim($message, PHP_EOL) . PHP_EOL;
        $now = new \DateTime();
        $timeString = $now->format('Y-m-d H:i:s');
        if ($showTimezone) {
            $timeString .= ' ' . $now->getTimezone()->getName();
        }
        $message = '[' .$timeString . '] ' . $message;

        // And append it to the log
        file_put_contents($logPath, $message, FILE_APPEND | LOCK_EX);

        return $this;
    }

}
