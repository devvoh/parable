<?php
/**
 * @package     Devvoh Components
 * @license     MIT
 * @author      Robin de Graaf <hello@devvoh.com>
 * @copyright   2015-2016, Robin de Graaf, devvoh webdevelopment
 */

namespace Devvoh\Parable;

use \Devvoh\Parable\App;

class Tool {

    /**
     * Redirect to $url
     *
     * @param null|string $url
     * @return false|void
     */
    public function redirect($url = null) {
        if (!$url) {
            return false;
        }
        if (strpos($url, 'http://') === false) {
            $url = App::getUrl($url);
        }
        header('location: ' . $url);
        return $this->end();
    }

    /**
     * Redirect to route
     *
     * @param null|string $routeName
     * @param array $params
     * @return false|void
     */
    public function redirectRoute($routeName = null, array $params = []) {
        if (!$routeName) {
            return false;
        }
        $url = App::Router()->buildRoute($routeName, $params);
        return $this->redirect(App::getUrl($url));
    }

    /**
     * End program execution immediately
     *
     * @param null|string $message
     */
    public static function end($message = null) {
        exit($message);
    }

}