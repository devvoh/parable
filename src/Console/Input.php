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

    /**
     * @return string
     *
     * @throws \Parable\Console\Exception
     */
    public function getHidden()
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            throw new \Parable\Console\Exception('Parable on Windows does not support hidden input.');
        }
        system('stty -echo');
        $input = trim(fgets(STDIN));
        system('stty echo');
        return $input;
    }

    /**
     * @return bool
     */
    public function getYesNo($default = true)
    {
        $value = strtolower($this->get());

        // Y/N values are ALWAYS directly returned as true/false
        if ($value == 'y') {
            return true;
        } elseif ($value == 'n') {
            return false;
        }

        // If no value, we return the default value
        if ($value == '') {
            return (bool)$default;
        }

        // Anything else should be considered false
        return false;
    }
}
