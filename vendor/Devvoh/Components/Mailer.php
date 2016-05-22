<?php
/**
 * @package     Devvoh Components
 * @license     MIT
 * @author      Robin de Graaf <hello@devvoh.com>
 * @copyright   2015-2016, Robin de Graaf, devvoh webdevelopment
 */

namespace Devvoh\Components;

class Mailer {

    /** @var array */
    protected $to      = [];

    /** @var array */
    protected $headers = [];

    /** @var bool */
    protected $isHTML  = false;

    /** @var null|string */
    protected $from;

    /** @var null|string */
    protected $subject;

    /** @var null|string */
    protected $body;

    /**
     * Set the addresses we're sending to
     *
     * @param  $to
     * @return $this
     */
    public function setTo($to) {
        if (!is_array($to)) {
            $to = [$to];
        }
        $this->to = $to;
        return $this;
    }

    /**
     * Return the addresses we're sending to
     *
     * @return array
     */
    public function getTo() {
        return $this->to;
    }

    /**
     * Add an address to the addresses we're sending to
     *
     * @param  $to
     * @return $this
     */
    public function addTo($to) {
        $this->to[] = $to;
        return $this;
    }

    /**
     * Set the subject
     *
     * @param  $subject
     * @return $this
     */
    public function setSubject($subject) {
        $this->subject = $subject;
        return $this;
    }

    /**
     * Return the subject
     *
     * @return null|string
     */
    public function getSubject() {
        return $this->subject;
    }

    /**
     * Set the body
     *
     * @param  $body
     * @return $this
     */
    public function setBody($body) {
        $this->body = $body;
        return $this;
    }

    /**
     * Return the body
     *
     * @return null|string
     */
    public function getBody() {
        return $this->body;
    }

    /**
     * Set the headers
     *
     * @param  $headers
     * @return $this
     */
    public function setHeaders($headers) {
        if (!is_array($headers)) {
            $headers = [$headers];
        }
        $this->headers = $headers;
        return $this;
    }

    /**
     * Return the headers
     *
     * @return array
     */
    public function getHeaders() {
        return $this->headers;
    }

    /**
     * Add a header
     *
     * @param  $header
     * @return $this
     */
    public function addHeader($header) {
        $this->headers[] = $header;
        return $this;
    }

    /**
     * Set the address we're sending from
     *
     * @param  $from
     * @return $this
     */
    public function setFrom($from) {
        $this->from = $from;
        return $this;
    }

    /**
     * Return the address we're sending from
     *
     * @return null|string
     */
    public function getFrom() {
        return $this->from;
    }

    /**
     * Set whether we're sending HTML or not
     *
     * @param  $active
     * @return $this
     */
    public function setHTML($active) {
        $this->isHTML = (bool)$active;
        return $this;
    }

    /**
     * Return whether we're sending HTML or not
     *
     * @return bool
     */
    public function isHTML() {
        return $this->isHTML;
    }

    /**
     * Send the mail
     *
     * @return bool
     */
    public function send() {
        $to = $this->getTo();
        if ($to) {
            $to = implode(',', $this->getTo());
        }
        // Get From, and set Reply-To as well if it's set
        if ($this->getFrom()) {
            $this->addHeader('From: ' . $this->getFrom());
            $this->addHeader('Reply-To: ' . $this->getFrom());
        }
        if ($this->isHTML()) {
            $this->addHeader('MIME-Version: 1.0');
            $this->addHeader('Content-type: text/html; charset=utf-8');
        }
        $headers = $this->getHeaders();
        if (count($headers) > 0) {
            $headers = implode("\r\n", $this->getHeaders());
        }
        $subject = $this->getSubject();
        $body = $this->getBody();

        return mail($to, $subject, $body, $headers);
    }

}