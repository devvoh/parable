<?php

namespace Parable\Http\Output;

use Parable\Http\Response;

class Html implements OutputInterface
{
    /** @var string */
    protected $contentType = 'text/html';

    /**
     * We accept all string-able values, so no arrays, objects or bools.
     *
     * @inheritdoc
     */
    public function acceptsContent($content)
    {
        return (is_array($content) || is_object($content) || is_bool($content)) === false;
    }

    /**
     * @inheritdoc
     */
    public function init(Response $response)
    {
        $response->setContentType($this->contentType);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function prepare(Response $response)
    {
        if (!$this->acceptsContent($response->getContent())) {
            throw new \Parable\Http\Exception('Can only work with string or null content');
        }
        return $response->getContent() ?: null;
    }
}
