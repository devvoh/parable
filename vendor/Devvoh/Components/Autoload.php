<?php
/**
 * @package     Devvoh
 * @subpackage  Components
 * @subpackage  Autoload
 * @license     MIT
 * @author      Robin de Graaf <hello@devvoh.com>
 * @copyright   2015-2016, Robin de Graaf, devvoh webdevelopment
 */

namespace Devvoh\Components;

class Autoload {

    /**
     * @var array
     */
    protected $locations = [];

    /**
     * Register the autoloader
     *
     * @return $this
     */
    public function register() {
        spl_autoload_register([$this, 'load']);
        return $this;
    }

    /**
     * Add a location to the stack
     *
     * @param $location
     *
     * @return $this
     */
    public function addLocation($location) {
        $this->locations[] = $location;
        return $this;
    }

    /**
     * Returns the locations to look in
     *
     * @return array
     */
    public function getLocations() {
        return $this->locations;
    }

    /**
     * Attempts to load the class if it exists
     *
     * @param $class
     *
     * @return bool|void
     */
    public function load($class) {
        $path = str_replace('\\', DS, $class);
        $path = '../##replace##/' . trim($path, DS) . '.php';
        $path = str_replace('/', DS, $path);

        foreach ($this->getLocations() as $subPath) {
            $actualPath = str_replace('##replace##', $subPath, $path);
            if (file_exists($actualPath)) {
                require_once($actualPath);
                return true;
            }
        }
        return;
    }

}