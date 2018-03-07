<?php

namespace Parable\Http\Output;

class Json implements \Parable\Http\Output\OutputInterface
{
    /** @var string */
    protected $contentType = 'application/json';

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
        $content = json_encode($response->getContent());
        $response->setContent($content);
        return $this;
    }
}
