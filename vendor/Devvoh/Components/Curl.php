<?php
/**
 * @package     Devvoh
 * @subpackage  Components
 * @subpackage  Curl
 * @license     MIT
 * @author      Robin de Graaf <hello@devvoh.com>
 * @copyright   2015 Robin de Graaf, devvoh webdevelopment
 */

namespace Devvoh\Components;

class Curl {
    use \Devvoh\Components\Traits\GetClassName;

    protected $userAgent = 'devvoh/curl';

    /**
     * Return the user agent
     *
     * @return string
     */
    public function getUserAgent() {
        return $this->userAgent;
    }

    /**
     * Sets the user agent
     *
     * @param $userAgent
     *
     * @return \Devvoh\Components\Curl
     */
    public function setUserAgent($userAgent) {
        $this->userAgent = $userAgent;
        return $this;
    }

    /**
     * Returns the result from loading url
     *
     * @param string $url
     *
     * @return mixed
     */
    public function getContent($url) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, $this->getUserAgent());
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    /**
     * Downloads $url to $filename
     *
     * @param string $url
     * @param string $path
     * @param string $filename
     *
     * @return bool
     */
    public function download($url, $path, $filename) {
        if (!is_dir($path) || !is_writable($path)) {
            return false;
        }
        $filename = $path . DS . $filename;
        $fp = fopen($filename, 'w+');

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_USERAGENT, $this->getUserAgent());
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_exec($ch);
        curl_close($ch);

        fclose($fp);
        return true;
    }

}