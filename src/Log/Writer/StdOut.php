<?php

namespace Parable\Log\Writer;

class StdOut implements \Parable\Log\Writer
{
    /** @var  */
    protected $file;

    /**
     * @inheritdoc
     */
    public function write($message)
    {
        echo $message;

        return $this;
    }
}
