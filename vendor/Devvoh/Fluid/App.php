<?php
/**
 * Fluid - App.php
 *
 * Main class
 *
 * @copyright   2015 Robin de Graaf, devvoh webdevelopment
 * @license     MIT
 * @author      Robin de Graaf (hello@devvoh.com)
 */

namespace Devvoh\Fluid;

class App {

    protected $router;

    public function __construct() {
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        
        $this->setRouter(new \Devvoh\Fluid\Router());
    }

    public function setRouter($router) {
        $this->router = $router;
        return $this;
    }

    public function getRouter() {
        return $this->router;
    }

    public function run() {
        return $this->getRouter()->match();
    }

}