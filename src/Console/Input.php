<?php

namespace Parable\Console;

class Input
{
    /**
     * Request input from the user.
     *
     * @return string
     */
    public function get()
    {
        return trim(fgets(STDIN));
    }

    /**
     * Request input from the user, while hiding the actual input. Use this to request passwords, for example.
     *
     * @return string
     *
     * @throws \Parable\Console\Exception
     */
    public function getHidden()
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            throw new \Parable\Console\Exception(
                "Hidden input is not supported on windows."
            );
        }
        system('stty -echo');
        $input = trim(fgets(STDIN));
        system('stty echo');
        return $input;
    }

    /**
     * Request a y/n input from the user, with a default value highlighted as uppercase ([Y/n], for example).
     *
     * @param bool $default
     *
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
