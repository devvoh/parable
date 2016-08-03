<?php
/**
 * @package     Parable Events
 * @license     MIT
 * @author      Robin de Graaf <hello@devvoh.com>
 * @copyright   2015-2016, Robin de Graaf, devvoh webdevelopment
 */

namespace Parable\Command;

class Cli {

    public function write($message) {
        echo $message;
        return $this;
    }

    public function br() {
        echo PHP_EOL;
        return $this;
    }

    public function writeLine($message) {
        $this->write($message)->br();
        return $this;
    }

    public function read() {
        $handle = fopen ("php://stdin", "r");
        $value = fgets($handle);
        return trim($value);
    }

    public function waitForKey() {
        $handle = fopen ("php://stdin", "r");
        $value = fgetc($handle);
        return trim($value);
    }



}