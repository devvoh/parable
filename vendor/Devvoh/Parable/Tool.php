<?php
/**
 * @package     Devvoh Components
 * @license     MIT
 * @author      Robin de Graaf <hello@devvoh.com>
 * @copyright   2015-2016, Robin de Graaf, devvoh webdevelopment
 */

namespace Devvoh\Parable;

class Tool {
    use \Devvoh\Parable\AppTrait;

    public function __construct() {
        $this->initApp();
    }

    /**
     * Redirect to $url
     *
     * @param null $url
     *
     * @return false
     */
    public function redirect($url = null) {
        if (!$url) {
            return false;
        }
        if (strpos($url, 'http://') === false) {
            $url = $this->app->getUrl($url);
        }
        header('location: ' . $url);
        $this->end();
    }

    /**
     * Redirect to route
     *
     * @param null $routeName
     * @param null $params
     *
     * @return bool|false
     */
    public function redirectRoute($routeName = null, $params = null) {
        if (!$routeName) {
            return false;
        }
        if ($params && !is_array($params)) {
            $params = [$params];
        }
        $url = $this->app->getRouter()->buildRoute($routeName, $params);
        return $this->redirect($this->app->getUrl($url));
    }

    /**
     * End program execution immediately
     *
     * @param null|mixed $message
     */
    public static function end($message = null) {
        exit($message);
    }

}