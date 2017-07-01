<?php

namespace Parable\Log;

interface Writer
{
    /**
     * @param string $message
     *
     * @return $this
     */
    public function write($message);
}
