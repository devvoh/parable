<?php
/**
 * @package     Devvoh Components
 * @license     MIT
 * @author      Robin de Graaf <hello@devvoh.com>
 * @copyright   2015-2016, Robin de Graaf, devvoh webdevelopment
 */

namespace Devvoh\Components;

class Mailer {

    protected $to = [];
    protected $from = null;
    protected $subject = null;
    protected $body = null;
    protected $headers = [];
    protected $isHTML = false;

    public function setTo($to) {
        if (!is_array($to)) {
            $to = [$to];
        }
        $this->to = $to;
        return $this;
    }

    public function getTo() {
        return $this->to;
    }

    public function addTo($to) {
        $this->to[] = $to;
        return $this;
    }

    public function setSubject($subject) {
        $this->subject = $subject;
        return $this;
    }

    public function getSubject() {
        return $this->subject;
    }

    public function setBody($body) {
        $this->body = $body;
        return $this;
    }

    public function getBody() {
        return $this->body;
    }

    public function setHeaders($headers) {
        if (!is_array($headers)) {
            $headers = [$headers];
        }
        $this->headers = $headers;
        return $this;
    }

    public function getHeaders() {
        return $this->headers;
    }

    public function addHeader($header) {
        $this->headers[] = $header;
        return $this;
    }

    public function setFrom($from) {
        $this->from = $from;
    }

    public function getFrom() {
        return $this->from;
    }

    public function setHTML($active) {
        $this->isHTML = (bool)$active;
    }

    public function isHTML() {
        return $this->isHTML;
    }

    public function send() {
        $to = $this->getTo();
        if ($to) {
            $to = implode(',', $this->getTo());
        }
        // Get From, and set Reply-To as well if it's set
        // @todo add setReplyTo & getReplyTo, as well as CC/BCC
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