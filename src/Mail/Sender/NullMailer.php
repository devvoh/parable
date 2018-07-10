<?php

namespace Parable\Mail\Sender;

class NullMailer implements SenderInterface
{
    /**
     * Send it nowhere at all.
     *
     * @inheritdoc
     *
     * @codeCoverageIgnore
     */
    public function send($to, $subject, $body, $headers)
    {
        return true;
    }
}
