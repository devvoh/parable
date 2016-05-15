<?php
/**
 * @package     Devvoh Parable
 * @license     MIT
 * @author      Robin de Graaf <hello@devvoh.com>
 * @copyright   2015-2016, Robin de Graaf, devvoh webdevelopment
 */

namespace Devvoh\Parable;

use \Devvoh\Parable\App;

class Config extends \Devvoh\Components\GetSet {

    /**
     * Set the resource to config
     */
    public function __construct() {
        $this->setResource('config');
    }

    /**
     * Shim to allow App to proceed without config code existing
     *
     * @throws \Exception
     * @return $this
     */
    public function load() {
        $configFile = App::getDir('app/config/config.ini');
        $customFile = App::getDir('app/config/custom.ini');

        if (file_exists($configFile)) {
            $configData = parse_ini_file($configFile, true);
        } else {
            throw new \Exception('config.ini not found');
        }
        if (file_exists($customFile)) {
            $configData = parse_ini_file($customFile) + $configData;
        }
        $this->setAll($configData);
        return $this;
    }

    /**
     * Return the value in $key as a boolean
     *
     * @param $key
     * @return bool
     */
    public function getBool($key) {
        return (bool)$this->get($key);
    }

}