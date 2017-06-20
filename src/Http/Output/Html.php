<?php

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
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function prepare(\Parable\Http\Response $response)
    {
        if (!is_string($response->getContent()) && !is_null($response->getContent())) {
            throw new \Parable\Http\Exception('Can only work with string content');
        }
    }
}
