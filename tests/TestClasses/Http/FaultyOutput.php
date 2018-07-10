<?php

namespace Parable\Tests\TestClasses\Http;

use Parable\Http\Output\OutputInterface;

class FaultyOutput implements OutputInterface
{
    /** @var string */
    protected $contentType = 'application/json';

    /**
     * @inheritdoc
     */
    public function acceptsContent($content)
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function init(\Parable\Http\Response $response)
    {
        $response->setContentType($this->contentType);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function prepare(\Parable\Http\Response $response)
    {
        // We always set the content to be an array, even IF
        return ["this is an array and that's invalid"];
    }
}
