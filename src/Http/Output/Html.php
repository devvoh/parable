<?php
/**
 * @package     Parable Http
 * @license     MIT
 * @author      Robin de Graaf <hello@devvoh.com>
 * @copyright   2015-2016, Robin de Graaf, devvoh webdevelopment
 */

namespace Parable\Http\Output;

class Html implements \Parable\Http\Output\OutputInterface
{
    /** @var string */
    protected $contentType = 'text/html';

    /**
     * @inheritdoc
     */
    public function init(\Parable\Http\Response $response)
    {
        $response->setContentType($this->contentType);
    }

    /**
     * @inheritdoc
     */
    public function prepare(\Parable\Http\Response $response)
    {
    }
}
