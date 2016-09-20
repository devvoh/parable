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

    protected $httpCodes = [
        100 => 'Continue',
        101 => 'Switching Protocols',

        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',

        300 => 'Multiple Choice',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',

        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Payload Too Large',
        414 => 'URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        421 => 'Misdirected Request',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        451 => 'Unavailable For Legal Reasons',

        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates',
        507 => 'Variant Also Negotiates',
        511 => 'Network Authentication Required',
    ];

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
        $this->setOutput(new \Parable\Http\Output\Html);
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
     * @return string
     */
    public function getHttpCodeText() {
        return $this->httpCodes[$this->httpCode];
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
        $this->output->init($this);
        return $this;
    }

    /**
     * Send the response
     *
     * @return $this
     */
    public function send() {
        $this->output->prepare($this);

        header("HTTP/1.1 " . $this->getHttpCode() . " " . $this->getHttpCodeText());
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
