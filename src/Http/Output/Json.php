<?php

namespace Parable\Http\Output;

use Parable\Http\Exception;
use Parable\Http\Response;

class Json implements OutputInterface
{
    /** @var string */
    protected $contentType = 'application/json';

    /**
     * A lot of things can be turned into json, so we're going
     * to assume we can handle it. Once we actually prepare, we'll
     * find out for real, but we don't want to incur the cost of
     * attempting it already.
     *
     * @inheritdoc
     */
    public function acceptsContent($content)
    {
        return true;
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
        $content = $response->getContent();

        // Encode if it isn't already a valid json string
        if (!$this->isJsonString($content)) {
            $content = json_encode($content);
        }

        // Final check, if it's still not a valid json string, we give up.
        if (!$this->isJsonString($content)) {
            throw new Exception("Json encode error: '" . json_last_error_msg() . "'"); // @codeCoverageIgnore
        }

        return $content ?: null;
    }

    /**
     * Attempt to check whether the provided content is json or not.
     *
     * @param mixed $content
     *
     * @return bool
     */
    protected function isJsonString($content)
    {
        if (!is_string($content) && !is_null($content)) {
            return false;
        }

        // We json_encode here to see if there might be a json_last_error
        json_decode($content, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return false;
        }
        return true;
    }
}
