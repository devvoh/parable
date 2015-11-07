<?php
/**
 * @package     Fluid
 * @subpackage  App/Response
 * @copyright   2015 Robin de Graaf, devvoh webdevelopment
 * @license     MIT
 * @author      Robin de Graaf (hello@devvoh.com)
 */

namespace Devvoh\Fluid\App;

use Devvoh\Fluid\App as App;

class Response {

    protected $contentTypes = array(
        'json' => 'application/json',
        'html' => 'text/html',
        'xml' => 'text/xml',
    );
    protected $charset = 'utf-8';
    protected $contentType = 'html';

    public function sendResponse() {
        header('Content-Type: ' . $this->getContentType() . '; charset=' . $this->getCharset());
    }

    public function getCharset() {
        return $this->charset;
    }

    public function setCharset($charset) {
        $this->charset = $charset;
        return $this;
    }

    public function setContentType($type) {
        if (array_key_exists($type, $this->contentTypes)) {
            $this->contentType = $type;
        }
        return $this;
    }

    public function getContentType() {
        return $this->contentTypes[$this->contentType];
    }

    public function clean() {
        $tmp = ob_clean();
    }

}