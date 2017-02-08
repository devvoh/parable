<?php

namespace Parable\Framework;

class Debug
{
    /**
     * @param string   $message
     * @param bool|int $exitCode
     *
     * @return $this
     */
    public function d($message, $exitCode = false)
    {
        echo '<pre>';
        var_dump($message);
        echo '</pre>';
        $this->endMaybe($exitCode);
        return $this;
    }

    /**
     * @param string   $message
     * @param bool|int $exitCode
     *
     * @return $this
     */
    public function p($message, $exitCode = false)
    {
        echo '<pre>';
        print_r($message);
        echo '</pre>';
        $this->endMaybe($exitCode);
        return $this;
    }

    /**
     * @param bool|int $exitCode
     *
     * @return $this
     */
    public function endMaybe($exitCode = false)
    {
        if ($exitCode !== false) {
            die($exitCode);
        }
        return $this;
    }
}
