<?php
/**
 * @package     Devvoh
 * @subpackage  Components
 * @subpackage  Response
 * @license     MIT
 * @author      Robin de Graaf <hello@devvoh.com>
 * @copyright   2015-2016, Robin de Graaf, devvoh webdevelopment
 */

namespace Devvoh\Components;

class Response {

    /**
     * @var array
     */
    protected $contentTypes = [
        'json'  => 'application/json',
        'html'  => 'text/html',
        'xml'   => 'text/xml',
        'js'    => 'application/javascript',
    ];

    /**
     * @var string
     */
    protected $charset      = 'utf-8';

    /**
     * @var string
     */
    protected $contentType  = 'html';

    /**
     * @var null
     */
    protected $content      = null;

    /**
     * @var bool
     */
    protected $onlyContent  = false;

    /**
     * Set the response header configured on Response class
     *
     * @param bool $onlyContent
     *
     * @return $this
     */
    public function sendResponse($onlyContent = false) {
        header('Content-Type: ' . $this->getContentType() . '; charset=' . $this->getCharset());
        if ($this->useOnlyContent() || $onlyContent) {
            $this->endOB();
            if ($this->content) {
                echo $this->content;
            }
        } else {
            echo $this->endOB();
        }
        return $this;
    }

    /**
     * Make sure we always call sendResponse, even if we died in the meantime
     */
    public function __destruct() {
        $this->sendResponse();
    }

    /**
     * Enable output buffering
     */
    public function startOB() {
        // Start by enabling output buffering
        ob_start();
        return $this;
    }

    /**
     * End output buffering and return the buffer contents
     *
     * @return string
     */
    public function endOB() {
        return ob_get_clean();
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
     * Set the content type, based on either the short-hand (array key) or the full string (value/in_array)
     *
     * @param $type
     *
     * @return $this
     */
    public function setContentType($type) {
        if (
            array_key_exists($type, $this->contentTypes)
            || in_array($type, $this->contentTypes)
        ) {
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

    /**
     * Set json and the correct content type. If onlyContent is true, the output buffer will be cleared and only
     * the content in \Devvoh\Components\Response->content is used for output.
     *
     * If $data is an array, it's json_encoded. If not, we're going to assume it's json.
     *
     * @param null $data
     * @param bool|false $onlyContent
     *
     * @return $this
     */
    public function setJson($data = null, $onlyContent = false) {
        if ($data) {
            if (is_array($data)) {
                $data = json_encode($data, JSON_PRETTY_PRINT);
            }
            $this->setContent($data);
        }
        // And set the correct content-type
        $this->setContentType('json');
        $this->setOnlyContent($onlyContent);
        return $this;
    }

    /**
     * Sets only content to (bool)$active
     *
     * @param $active
     *
     * @return $this
     */
    public function setOnlyContent($active) {
        $this->onlyContent = (bool)$active;
        return $this;
    }

    /**
     * Returns whether or not only content should be used or also whatever's already in the output buffer
     *
     * @return bool
     */
    public function useOnlyContent() {
        return $this->onlyContent;
    }

}