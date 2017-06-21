<?php

namespace Parable\Framework;

/**
 * The following magic properties are available in template (.phtml) files through $this->...property
 *
 * @property \Parable\Event\Dock                       $dock
 * @property \Parable\Event\Hook                       $hook
 * @property \Parable\Filesystem\Path                  $path
 * @property \Parable\Framework\App                    $app
 * @property \Parable\Framework\Authentication         $authentication
 * @property \Parable\Framework\Config                 $config
 * @property \Parable\Framework\Dispatcher             $dispatcher
 * @property \Parable\Framework\Toolkit                $toolkit
 * @property \Parable\Framework\View                   $view
 * @property \Parable\Framework\Mail\Mailer            $mailer
 * @property \Parable\Framework\Mail\TemplateVariables $templateVariables
 * @property \Parable\Http\Request                     $request
 * @property \Parable\Http\Response                    $response
 * @property \Parable\Http\Url                         $url
 * @property \Parable\GetSet\Cookie                    $cookie
 * @property \Parable\GetSet\Env                       $env
 * @property \Parable\GetSet\Files                     $files
 * @property \Parable\GetSet\Get                       $get
 * @property \Parable\GetSet\Internal                  $internal
 * @property \Parable\GetSet\Post                      $post
 * @property \Parable\GetSet\Server                    $server
 * @property \Parable\GetSet\Session                   $session
 * @property \Parable\GetSet\SessionMessage            $sessionMessage
 * @property \Parable\Log\Logger                       $logger
 * @property \Parable\ORM\Query                        $query
 * @property \Parable\ORM\Database                     $database
 * @property \Parable\Routing\Router                   $router
 * @property \Parable\Tool\Rights                      $rights
 */
class View
{
    /** @var \Parable\Framework\Toolkit */
    protected $toolkit;

    /** @var \Parable\Filesystem\Path */
    protected $path;

    /** @var \Parable\Http\Response */
    protected $response;

    /** @var string */
    protected $templatePath;

    /** @var array */
    protected $magicProperties = [];

    public function __construct(
        \Parable\Framework\Toolkit $toolkit,
        \Parable\Filesystem\Path $path,
        \Parable\Http\Response $response
    ) {
        $this->toolkit  = $toolkit;
        $this->path     = $path;
        $this->response = $response;

        $this->initializeMagicProperties();
    }

    protected function initializeMagicProperties()
    {
        $reflection = new \ReflectionClass(static::class);
        $magicPropertiesBlock = explode(PHP_EOL, $reflection->getDocComment());

        foreach ($magicPropertiesBlock as $magicPropertiesLine) {
            if (strpos($magicPropertiesLine, "@property") === false) {
                continue;
            }

            $partsString = trim(str_replace("* @property", "", $magicPropertiesLine));
            $parts       = explode('$', $partsString);

            $this->magicProperties[trim($parts[1])] = trim($parts[0]);
        }
    }

    /**
     * @param string $templatePath
     *
     * @return $this
     */
    public function setTemplatePath($templatePath)
    {
        $this->templatePath = $templatePath;
        return $this;
    }

    /**
     * @param string $templatePath
     *
     * @return string
     */
    public function partial($templatePath)
    {
        $this->response->startOutputBuffer();
        $this->loadTemplatePath($templatePath);
        return $this->response->returnOutputBuffer();
    }

    /**
     * Render the template from the configured templatePath
     *
     * @return $this
     */
    public function render()
    {
        $this->loadTemplatePath($this->templatePath);
        return $this;
    }

    /**
     * Attempt to load the templatePath
     *
     * @param string $templatePath
     *
     * @return $this
     */
    protected function loadTemplatePath($templatePath)
    {
        if (!file_exists($templatePath)) {
            $templatePath = $this->path->getDir($templatePath);
        }
        if (file_exists($templatePath)) {
            require($templatePath);
        }
        return $this;
    }

    /**
     * Magic get function to pass through all calls to DI-able classes.
     *
     * @param string $property
     *
     * @return null|object
     */
    public function __get($property)
    {
        if (isset($this->magicProperties[$property])) {
            return \Parable\DI\Container::get($this->magicProperties[$property]);
        }
        return null;
    }
}
