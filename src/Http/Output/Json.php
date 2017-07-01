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
        $content = $response->getContent();

        if (is_object($content) || is_array($content) || !json_decode($content)) {
            $content = json_encode($content);
        }

        $response->setContent($content);
        return $this;
    }
}
