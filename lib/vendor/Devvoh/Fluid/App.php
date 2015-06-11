<?php
namespace Devvoh\Fluid;

class App {

    protected $router;

    public function __construct() {
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