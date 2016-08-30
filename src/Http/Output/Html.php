<?php
/**
 * @package     Parable Http
 * @license     MIT
 * @author      Robin de Graaf <hello@devvoh.com>
 * @copyright   2015-2016, Robin de Graaf, devvoh webdevelopment
 */

namespace Parable\Http\Output;

class Html {

    /** @var string */
    protected $contentType = 'text/html';

    /**
     * @inheritdoc
     */
    public function prepare(\Parable\Http\Response $response) {
        $response->setContentType($this->contentType);
    }

}
