<?php
/**
 * @package     Parable Framework
 * @license     MIT
 * @author      Robin de Graaf <hello@devvoh.com>
 * @copyright   2015-2016, Robin de Graaf, devvoh webdevelopment
 */

namespace Parable\Framework;

class Debug {

    /**
     * @param $message
     * @param bool $exitCode
     *
     * @return $this
     */
    public function d($message, $exitCode = false) {
        echo '<pre>';
        var_dump($message);
        echo '</pre>';
        $this->endMaybe($exitCode);
        return $this;
    }

    /**
     * @param $message
     * @param bool $exitCode
     *
     * @return $this
     */
    public function p($message, $exitCode = false) {
        echo '<pre>';
        print_r($message);
        echo '</pre>';
        $this->endMaybe($exitCode);
        return $this;
    }

    /**
     * @param bool $exitCode
     *
     * @return $this
     */
    public function endMaybe($exitCode = false) {
        if ($exitCode !== false) {
            die($exitCode);
        }
        return $this;
    }

}
