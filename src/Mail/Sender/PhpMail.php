<?php

namespace Parable\Mail\Sender;

class PhpMail implements SenderInterface
{
    /**
     * Send using PHP's built-in Mail interface
     *
     * @inheritdoc
     *
     * @codeCoverageIgnore
     */
    public function send($to, $subject, $body, $headers)
    {
        return mail(
            $to,
            $subject,
            $body,
            $headers
        );
    }
}
