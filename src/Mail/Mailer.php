<?php

namespace Parable\Mail;

class Mailer
{
    /** @var \Parable\Framework\View */
    protected $view;

    /** @var \Parable\Http\Values\Internal */
    protected $internal;

    /** @var array */
    protected $templateVariables = [];

    /** @var array */
    protected $addresses = [
        'to'  => [],
        'cc'  => [],
        'bcc' => [],
        'from' => [
            [
                'email' => 'noreply@authorly.io',
                'name'  => 'Authorly',
            ],
        ],
    ];

    /** @var string */
    protected $subject;

    /** @var string */
    protected $body;

    /** @var array */
    protected $headers = [];

    public function __construct(
        \Parable\Framework\View $view,
        \Parable\Http\Values\Internal $internal
    ) {
        $this->view     = $view;
        $this->internal = $internal;

        $this->headers = [
            "MIME-Version: 1.0",
            "Content-type: text/html; charset=UTF-8",
        ];
    }

    /**
     * @param string      $email
     * @param null|string $name
     *
     * @return $this
     * @throws \Parable\Mail\Exception
     */
    public function setFrom($email, $name = null)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \Parable\Mail\Exception("Email provided is invalid: {$email}");
        }
        $this->addresses['from'] = [
            [
                'email' => $email,
                'name'  => $name,
            ]
        ];
        return $this;
    }

    /**
     * @param string      $type
     * @param string      $email
     * @param null|string $name
     *
     * @throws \Parable\Mail\Exception
     */
    protected function addAddress($type, $email, $name = null)
    {
        if (!isset($this->addresses[$type])) {
            throw new \Parable\Mail\Exception('Only to, cc, bcc addresses are allowed.');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \Parable\Mail\Exception("Email provided is invalid: {$email}");
        }
        $this->addresses[$type][] = [
            'email' => $email,
            'name'  => $name,
        ];
    }

    /**
     * @param string $type
     *
     * @return string
     * @throws \Parable\Mail\Exception
     */
    protected function getAddresses($type)
    {
        if (!isset($this->addresses[$type])) {
            throw new \Parable\Mail\Exception('Only to, cc, bcc addresses are allowed.');
        }

        $addresses = [];
        foreach ($this->addresses[$type] as $address) {
            if (!empty($address['name'])) {
                $addresses[] = $address['name'] . ' <' . $address['email'] . '>';
            } else {
                $addresses[] = $address['email'];
            }
        }
        return implode(', ', $addresses);
    }

    /**
     * @param string $email
     * @param string $name
     *
     * @return $this
     */
    public function addTo($email, $name = '')
    {
        $this->addAddress('to', $email, $name);
        return $this;
    }

    /**
     * @param string $email
     * @param string $name
     *
     * @return $this
     */
    public function addCc($email, $name = '')
    {
        $this->addAddress('cc', $email, $name);
        return $this;
    }

    /**
     * @param string $email
     * @param string $name
     *
     * @return $this
     */
    public function addBcc($email, $name = '')
    {
        $this->addAddress('bcc', $email, $name);
        return $this;
    }

    /**
     * @param string $subject
     *
     * @return $this
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
        return $this;
    }

    /**
     * @param string $body
     *
     * @return $this
     */
    public function setBody($body)
    {
        $this->body = $body;
        return $this;
    }

    /**
     * @param string $header
     *
     * @return $this
     */
    public function addHeader($header)
    {
        $this->headers[] = $header;
        return $this;
    }

    /**
     * @param array $data
     *
     * @return $this
     */
    public function setTemplateVariables(array $data)
    {
        $this->templateVariables = $data;
        return $this;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function loadTemplate($name)
    {
        // Temporarily set the template variables as internal values
        foreach ($this->templateVariables as $key => $value) {
            $this->internal->set($key, $value);
        }

        $content = $this->view->partial("app/View/Email/{$name}.phtml");
        $this->setBody($content);

        // And remove the values again, to not clutter internal storage
        foreach ($this->templateVariables as $key => $value) {
            $this->internal->remove($key);
        }
        return $this;
    }

    /**
     * @return $this
     * @throws \Parable\Mail\Exception
     */
    public function send()
    {
        // Check the basics
        if (count($this->addresses['to']) == 0) {
            throw new \Parable\Mail\Exception('No to addresses provided.');
        }
        if (empty($this->subject)) {
            throw new \Parable\Mail\Exception('No subject provided.');
        }
        if (empty($this->body)) {
            throw new \Parable\Mail\Exception('No body provided.');
        }

        // Handle the to, cc and bcc addresses
        $to  = $this->getAddresses('to');
        $cc  = $this->getAddresses('cc');
        $bcc = $this->getAddresses('bcc');

        if (!empty($cc)) {
            $this->addHeader("Cc: {$cc}");
        }
        if (!empty($bcc)) {
            $this->addHeader("Bcc: {$bcc}");
        }

        // Handle from
        $from = $this->getAddresses('from');
        $this->addHeader("From: {$from}");

        mail(
            $to,
            $this->subject,
            $this->body,
            implode("\r\n", $this->headers)
        );
        return $this;
    }
}