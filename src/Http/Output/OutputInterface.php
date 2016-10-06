<?php
/**
 * @package     Parable Http
 * @license     MIT
 * @author      Robin de Graaf <hello@devvoh.com>
 * @copyright   2015-2016, Robin de Graaf, devvoh webdevelopment
 */

namespace Parable\Http\Output;

interface OutputInterface
{
    /**
     * When the output is initialized, it's possible to set certain things.
     *
     * @param \Parable\Http\Response $response
     */
    public function init(\Parable\Http\Response $response);

    /**
     * Prepare the content for output according to the output type.
     *
     * @param \Parable\Http\Response $response
     */
    public function prepare(\Parable\Http\Response $response);
}
