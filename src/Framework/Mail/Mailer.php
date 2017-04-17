<?php

namespace Parable\Framework\Mail;

class Mailer extends \Parable\Mail\Mailer
{
    /** @var \Parable\Framework\View */
    protected $view;

    /** @var \Parable\Framework\Mail\TemplateVariables */
    protected $templateVariables;

    /** @var \Parable\Filesystem\Path */
    protected $path;

    public function __construct(
        \Parable\Framework\View $view,
        \Parable\Framework\Mail\TemplateVariables $templateVariables,
        \Parable\Filesystem\Path $path
    ) {
        parent::__construct();

        $this->view              = $view;
        $this->templateVariables = $templateVariables;
        $this->path              = $path;
    }

    /**
     * @param array $data
     *
     * @return $this
     */
    public function setTemplateVariables(array $data)
    {
        $this->templateVariables->setAll($data);
        return $this;
    }

    /**
     * @return array
     */
    public function getTemplateVariables()
    {
        return $this->templateVariables->getAll();
    }

    /**
     * @param string $key
     * @param mixed  $value
     *
     * @return $this
     */
    public function setTemplateVariable($key, $value)
    {
        $this->templateVariables->set($key, $value);
        return $this;
    }

    /**
     * @param string $key
     *
     * @return mixed|null
     */
    public function getTemplateVariable($key)
    {
        return $this->templateVariables->get($key);
    }

    /**
     * @param string $path
     *
     * @return $this
     * @throws \Parable\Framework\Exception
     */
    public function loadTemplate($path)
    {
        $path = $this->path->getDir($path);

        if (!file_exists($path)) {
            throw new \Parable\Framework\Exception("Email template '{$path}' does not exist.");
        }

        $content = $this->view->partial($path);
        $this->setBody(trim($content));
        return $this;
    }
}
