<?php
/**
 * @package     Parable Http
 * @license     MIT
 * @author      Robin de Graaf <hello@devvoh.com>
 * @copyright   2015-2016, Robin de Graaf, devvoh webdevelopment
 */

namespace Parable\Http;

class Response {

    /** @var int */
    protected $httpCode = 200;

    /** @var string */
    protected $contentType = 'text/html';

    /** @var string */
    protected $content;

    /**
     * @param int $httpCode
     *
     * @return $this
     */
    public function setHttpCode($httpCode) {
        $this->httpCode = $httpCode;
        return $this;
    }

    /**
     * @return int
     */
    public function getHttpCode() {
        return $this->httpCode;
    }

    /**
     * @param string $contentType
     *
     * @return $this
     */
    public function setContentType($contentType) {
        $this->contentType = $contentType;
        return $this;
    }

    /**
     * @return string
     */
    public function getContentType() {
        return $this->contentType;
    }

    /**
     * Send the response
     *
     * @return $this
     */
    public function send() {
        header("HTTP/1.1 " . $this->getHttpCode() . " OK");
        header("Content-type: " . $this->getContentType());

        echo $this->getContent();
        return $this;
    }

    /**
     * @param string $content
     *
     * @return $this
     */
    public function setContent($content) {
        $this->content = $content;
        return $this;
    }

    /**
     * @return string
     */
    public function getContent() {
        return $this->content;
    }

    /**
     * @param string $content
     *
     * @return $this
     */
    public function appendContent($content) {
        $this->content .= $content;
        return $this;
    }

    /**
     * @param string $content
     *
     * @return $this
     */
    public function prependContent($content) {
        $this->content = $content . $this->content;
        return $this;
    }

    /**
     * Start the output buffer
     *
     * @return $this
     */
    public function startOutputBuffer() {
        ob_start();
        return $this;
    }

    /**
     * Return and end the current output buffer
     *
     * @return string
     */
    public function returnOutputBuffer() {
        return ob_get_clean();
    }

}
