<?php

namespace Parable\Console;

class Input
{
    /**
     * @return string
     */
    public function get()
    {
        return trim(fgets(STDIN));
    }
}
