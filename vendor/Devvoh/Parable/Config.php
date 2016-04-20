<?php
/**
 * @package     Devvoh Parable
 * @license     MIT
 * @author      Robin de Graaf <hello@devvoh.com>
 * @copyright   2015-2016, Robin de Graaf, devvoh webdevelopment
 */

namespace Devvoh\Parable;

class Config extends \Devvoh\Components\GetSet {
    use \Devvoh\Parable\AppTrait;

    /**
     * Set the resource to config
     */
    public function __construct() {
        $this->initApp();

        $this->app = \Devvoh\Parable\App::getInstance();
        $this->setResource('config');
    }

    /**
     * Shim to allow App to proceed without config code existing
     *
     * @throws \Exception
     *
     * @return $this
     */
    public function load() {
        $configFile = $this->app->getDir('app/config/config.ini');
        $customFile = $this->app->getDir('app/config/custom.ini');

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

    public function getBool($key) {
        return (bool)$this->get($key);
    }

}