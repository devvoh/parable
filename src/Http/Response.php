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
    protected $content;

    /** @var \Parable\Http\Output\OutputInterface */
    protected $output;

    /** @var array */
    protected $headers = [];

    /**
     * By default we're going to set the Html Output type.
     */
    public function __construct() {
        $this->output = new \Parable\Http\Output\Html;
    }

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
        $this->setHeader('Content-type', $contentType);
        return $this;
    }

    /**
     * @param Output\OutputInterface $output
     *
     * @return $this
     */
    public function setOutput(\Parable\Http\Output\OutputInterface $output) {
        $this->output = $output;
        return $this;
    }

    /**
     * Send the response
     *
     * @return $this
     */
    public function send() {
        $this->output->prepare($this);

        header("HTTP/1.1 " . $this->getHttpCode() . " OK");
        foreach ($this->headers as $key => $value) {
            header($key . ': ' . $value);
        }

        echo $this->getContent();
        exit();
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

    /**
     * @param $key
     * @param $value
     */
    public function setHeader($key, $value) {
        $this->headers[$key] = $value;
    }

    /**
     * @param $key
     *
     * @return null
     */
    public function getHeader($key) {
        if (!isset($this->headers[$key])) {
            return null;
        }
        return $this->headers[$key];
    }

    /**
     * @return array
     */
    public function getHeaders() {
        return $this->headers;
    }

}
