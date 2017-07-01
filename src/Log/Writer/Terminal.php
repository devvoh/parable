<?php

namespace Parable\Log\Writer;

class Terminal implements \Parable\Log\Writer
{
    /** @var  */
    protected $file;

    /**
     * @inheritdoc
     */
    public function write($message)
    {
        echo $message . PHP_EOL;
        return $this;
    }
}
