<?php
/**
 * @package     Devvoh
 * @subpackage  Fluid
 * @subpackage  App
 * @subpackage  Config
 * @license     MIT
 * @author      Robin de Graaf <hello@devvoh.com>
 * @copyright   2015 Robin de Graaf, devvoh webdevelopment
 */

namespace Devvoh\Fluid\App;

use \Devvoh\Fluid\App;

class Config extends \Devvoh\Components\GetSet {
    use \Devvoh\Components\Traits\GetClassName;

    /**
     * Set the resource to config
     *
     * @return \Devvoh\Fluid\App\Config
     */
    public function __construct() {
        $this->setResource('config');
        return $this;
    }

    /**
     * Shim to allow App to proceed without config code existing
     *
     * @throws Exception
     * @return \Devvoh\Fluid\App\Config
     */
    public function load() {
        $configFile = App::getDir('app/config/config.ini');
        $customFile = App::getDir('app/config/custom.ini');

        if (file_exists($configFile)) {
            $configData = parse_ini_file($configFile, true);
        } else {
            throw new Exception('config.ini not found');
        }
        if (file_exists($customFile)) {
            $configData = parse_ini_file($customFile) + $configData;
        }
        $this->setAll($configData);
        return;
    }

}