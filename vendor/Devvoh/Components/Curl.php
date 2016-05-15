<?php
/**
 * @package     Devvoh Components
 * @license     MIT
 * @author      Robin de Graaf <hello@devvoh.com>
 * @copyright   2015-2016, Robin de Graaf, devvoh webdevelopment
 */

namespace Devvoh\Components;

class Curl {

    /** @var string */
    protected $userAgent = 'devvoh/components/curl';

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
     * @param string $userAgent
     * @return $this
     */
    public function setUserAgent($userAgent) {
        $this->userAgent = $userAgent;
        return $this;
    }

    /**
     * Returns the result from loading url
     *
     * @param null|string $url
     * @return string|null
     */
    public function getContent($url = null) {
        if (!$url) {
            return null;
        }

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
     * @return bool
     */
    public function download($url = null, $path = null, $filename = null) {
        if (!$url || !$path || !$filename || !is_dir($path) || !is_writable($path)) {
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