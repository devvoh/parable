<?php

namespace Parable\Log\Writer;

class NullLogger implements WriterInterface
{
    /**
     * Log it nowhere at all.
     *
     * @inheritdoc
     *
     * @codeCoverageIgnore
     */
    public function write($message)
    {
        return $this;
    }
}
