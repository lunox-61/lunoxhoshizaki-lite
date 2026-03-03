<?php

namespace LunoxHoshizaki\Mail;

class Mail
{
    protected array $to = [];

    public function __construct(array $to)
    {
        $this->to = $to;
    }

    /**
     * Set the recipient(s).
     */
    public static function to(string|array $emails): self
    {
        return new self(is_array($emails) ? $emails : [$emails]);
    }

    /**
     * Send the mailable.
     */
    public function send(Mailable $mailable): bool
    {
        $mailable->build();
        
        $to = implode(', ', $this->to);
        $subject = $mailable->subject;
        $message = $mailable->render();
        
        $from = $_ENV['MAIL_FROM_ADDRESS'] ?? 'hello@example.com';
        $headers = [
            'MIME-Version: 1.0',
            'Content-type: text/html; charset=utf-8',
            'From: ' . $from,
        ];

        // Using PHP's native mail function
        return mail($to, $subject, $message, implode("\r\n", $headers));
    }
}
