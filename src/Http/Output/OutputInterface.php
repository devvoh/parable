<?php

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
