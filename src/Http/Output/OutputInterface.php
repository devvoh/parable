<?php

namespace Parable\Http\Output;

use Parable\Http\Response;

interface OutputInterface
{
    /**
     * Check whether the output implementation accepts the type of content.
     *
     * @param mixed $content
     *
     * @return bool
     */
    public function acceptsContent($content);

    /**
     * When the output is initialized, it's possible to set certain values to set up the proper output values.
     *
     * @param Response $response
     *
     * @return $this
     */
    public function init(Response $response);

    /**
     * Prepare and return the content for output according to the output type.
     *
     * @param Response $response
     *
     * @return string|null
     */
    public function prepare(Response $response);
}
