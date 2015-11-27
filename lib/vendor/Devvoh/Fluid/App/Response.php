<?php
/**
 * @package     Fluid
 * @subpackage  App
 * @subpackage  Response
 * @copyright   2015 Robin de Graaf, devvoh webdevelopment
 * @license     MIT
 * @author      Robin de Graaf (hello@devvoh.com)
 */

namespace Devvoh\Fluid\App;

use \Devvoh\Fluid\App as App;

class Response {
    use \Devvoh\Components\Traits\GetClassName;

    protected $contentTypes = array(
        'json' => 'application/json',
        'html' => 'text/html',
        'xml' => 'text/xml',
    );
    protected $charset = 'utf-8';
    protected $contentType = 'html';
    protected $content = null;

    /**
     * Set the response header configured on Response class
     *
     * @return $this
     */
    public function sendResponse() {
        header('Content-Type: ' . $this->getContentType() . '; charset=' . $this->getCharset());
        if ($this->content) {
            echo $this->content;
        }
        return $this;
    }

    /**
     * Return the character set
     *
     * @return string
     */
    public function getCharset() {
        return $this->charset;
    }

    /**
     * Set the character set
     *
     * @TODO Add validation of character set
     *
     * @param $charset
     *
     * @return $this
     */
    public function setCharset($charset) {
        $this->charset = $charset;
        return $this;
    }

    /**
     * Set the content type
     *
     * @param $type
     *
     * @return $this
     */
    public function setContentType($type) {
        if (array_key_exists($type, $this->contentTypes)) {
            $this->contentType = $type;
        }
        return $this;
    }

    /**
     * Return the content type
     *
     * @return mixed
     */
    public function getContentType() {
        return $this->contentTypes[$this->contentType];
    }

    /**
     * Set content from 0.
     *
     * @param $content
     *
     * @return $this
     */
    public function setContent($content) {
        $this->content = $content;
        return $this;
    }

    /**
     * Prepend data to content
     *
     * @param $content
     *
     * @return $this
     */
    public function prependContent($content) {
        $this->content = $content . $this->content;
        return $this;
    }

    /**
     * Append data to content
     *
     * @param $content
     *
     * @return $this
     */
    public function appendContent($content) {
        $this->content = $this->content . $content;
        return $this;
    }

}