<?php

namespace Parable\Http;

class Url
{
    /** @var \Parable\Http\Response */
    protected $response;

    /** @var \Parable\Http\Values\Get */
    protected $get;

    /** @var string */
    protected $baseUrl;

    public function __construct(
        \Parable\Http\Response $response,
        \Parable\Http\Values\Get $get
    ) {
        $this->response = $response;
        $this->get      = $get;
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

    /**
     * @return string
     */
    public function getCurrentUrl()
    {
        if ($this->get->get('url')) {
            return $this->get->get('url');
        }
        return '/';
    }

    /**
     * @return string
     */
    public function getCurrentUrlFull()
    {
        return $this->getUrl($this->getCurrentUrl());
    }
}
