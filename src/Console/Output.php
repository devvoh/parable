<?php

namespace Parable\Console;

class Output
{
    /**
     * @param string $string
     *
     * @return $this
     */
    public function write($string)
    {
        echo $string;
        return $this;
    }

    /**
     * @param array|string $lines
     *
     * @return $this
     */
    public function writeln($lines)
    {
        if (!is_array($lines)) {
            $lines = [$lines];
        }

        foreach ($lines as $line) {
            $this->write($line);
            $this->newline();
        }
        return $this;
    }

    /**
     * @param int $count
     *
     * @return $this
     */
    public function newline($count = 1)
    {
        echo str_repeat(PHP_EOL, $count);
        return $this;
    }
}
