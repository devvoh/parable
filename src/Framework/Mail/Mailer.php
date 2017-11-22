<?php

namespace Parable\Framework\Mail;

class Mailer extends \Parable\Mail\Mailer
{
    /** @var \Parable\Framework\Config */
    protected $config;

    /** @var \Parable\Framework\View */
    protected $view;

    /** @var \Parable\Framework\Mail\TemplateVariables */
    protected $templateVariables;

    /** @var \Parable\Filesystem\Path */
    protected $path;

    public function __construct(
        \Parable\Framework\Config $config,
        \Parable\Framework\View $view,
        \Parable\Framework\Mail\TemplateVariables $templateVariables,
        \Parable\Filesystem\Path $path
    ) {
        $this->config            = $config;
        $this->view              = $view;
        $this->templateVariables = $templateVariables;
        $this->path              = $path;

        if ($this->config->get("parable.mail.sender")) {
            try {
                $sender = \Parable\DI\Container::create($this->config->get("parable.mail.sender"));
                $this->setMailSender($sender);
            } catch (\Exception $e) {
                throw new \Parable\Framework\Exception("Invalid mail sender set in config.");
            }
        } else {
            // Use PhPMail sender by default
            $this->setMailSender(new \Parable\Mail\Sender\PhpMail());
        }
    }

    /**
     * Set template variables. These will be available in the template using ->templateVariables->get().
     *
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
     * Return all template variables.
     *
     * @return array
     */
    public function getTemplateVariables()
    {
        return $this->templateVariables->getAll();
    }

    /**
     * Set a single template variable.
     *
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
     * Return a single template variable by key.
     *
     * @param string $key
     *
     * @return mixed|null
     */
    public function getTemplateVariable($key)
    {
        return $this->templateVariables->get($key);
    }

    /**
     * Override the \Parable\Mail\Mailer's resetMailData to also remove all templateVariables.
     *
     * @return $this
     */
    public function resetMailData()
    {
        parent::resetMailData();
        $this->templateVariables->reset();
        return $this;
    }

    /**
     * Load template for this mail. Returns the interpreted output as string.
     *
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
