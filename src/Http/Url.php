<?php

namespace Parable\Http;

class Url
{
    /** @var \Parable\Http\Response */
    protected $response;

    /** @var string */
    protected $baseUrl;

    public function __construct(
        \Parable\Http\Response $response
    ) {
        $this->response = $response;
    }

    /**
     * Initialize the correct baseUrl
     *
     * @return $this
     */
    public function buildBaseUrl()
    {
        $domain = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'];

        $url = str_replace('/public/index.php', '', $_SERVER['SCRIPT_NAME']);
        $this->baseUrl = $domain . '/' . ltrim($url, '/');
        return $this;
    }

    /**
     * @return string
     */
    public function getBaseUrl()
    {
        if (!$this->baseUrl) {
            $this->buildBaseUrl();
        }
        return $this->baseUrl;
    }

    /**
     * @param string $url
     *
     * @return string
     */
    public function getUrl($url = '')
    {
        return rtrim($this->getBaseUrl(), '/') . '/' . ltrim($url, '/');
    }
}
