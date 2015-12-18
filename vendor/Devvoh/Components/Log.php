<?php
/**
 * @package     Devvoh
 * @subpackage  Components
 * @subpackage  Log
 * @license     MIT
 * @author      Robin de Graaf <hello@devvoh.com>
 * @copyright   2015 Robin de Graaf, devvoh webdevelopment
 */

namespace Devvoh\Components;

class Log {

    protected $path             = './';
    protected $defaultLogFile   = 'app.log';
    protected $mode             = 0777;

    /**
     * Sets the mode (0ctal)
     *
     * @param $mode
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
     * @param $defaultLogFile
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
     * @param $path
     * @return $this
     */
    public function setPath($path) {
        $this->path = $path;
        return $this;
    }

    /**
     * Returns the path, and if it doesn't exist, attempts to create it
     *
     * @throws \Exception
     * @return string
     */
    public function getPath() {
        if (!is_dir($this->path)) {
            // Create directory
            $created = @mkdir($this->path, $this->getMode(), true);
            if (!$created) {
                throw new \Exception('Could not create log directory: ' . $this->path);
            }
        }
        if (!is_writable($this->path)) {
            $chmod = chmod($this->path, $this->getMode());
            if (!$chmod) {
                throw new \Exception('Log directory is not writable: ' . $this->path);
            }
        }
        return $this->path;
    }

    /**
     * Write message to the log file
     *
     * @param      $message
     * @param null $logFile
     * @param bool $forceWrite
     * @throws \Exception
     * @return $this
     */
    public function write($message, $logFile = null, $showTimezone = false, $forceWrite = false) {
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