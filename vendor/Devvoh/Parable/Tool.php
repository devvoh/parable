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
     * @param null $url
     *
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
        $this->end();
        return;
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
        $url = App::getRouter()->buildRoute($routeName, $params);
        return $this->redirect(App::getUrl($url));
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