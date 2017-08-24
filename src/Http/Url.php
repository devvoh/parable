<?php

namespace Parable\Http;

class Url
{
    /** @var \Parable\Http\Request */
    protected $request;

    /** @var string */
    protected $baseUrl;

    /** @var null|string */
    protected $basePath = "/public/index.php";

    public function __construct(
        \Parable\Http\Request $request
    ) {
        $this->request = $request;
    }

    /**
     * @param string $basePath
     *
     * @return $this
     */
    public function setBasePath($basePath)
    {
        $this->basePath = "/" . trim($basePath, "/");
        return $this;
    }

    /**
     * @return null|string
     */
    public function getBasePath()
    {
        return $this->basePath;
    }

    /**
     * Initialize the correct baseUrl
     *
     * @return $this
     */
    public function buildBaseUrl()
    {
        $domain = $this->request->getScheme() . '://' . $this->request->getHttpHost();

        $url = str_replace($this->getBasePath(), '', $this->request->getScriptName());
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
