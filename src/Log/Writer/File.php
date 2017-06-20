<?php

namespace Parable\Log\Writer;

class File implements \Parable\Log\Writer
{
    /** @var  */
    protected $logFile;

    /**
     * @inheritdoc
     */
    public function write($message)
    {
        if (!$this->logFile) {
            throw new \Parable\Log\Exception("No log file set. \Log\Writer\File requires a valid target file.");
        }
        if (false === $this->writeToFile($message)) {
            throw new \Parable\Log\Exception("Could not write to log file.");
        }
        return $this;
    }

    /**
     * @param string $logFile
     *
     * @return $this
     * @throws \Parable\Log\Exception
     */
    public function setLogFile($logFile)
    {
        $this->logFile = $logFile;
        return $this;
    }

    /**
     * @param string $message
     *
     * @return bool|int
     *
     * @codeCoverageIgnore
     */
    protected function writeToFile($message)
    {
        return @file_put_contents($this->logFile, $message, FILE_APPEND);
    }
}
