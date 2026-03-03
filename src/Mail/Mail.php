<?php

namespace LunoxHoshizaki\Mail;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

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

        $mail = new PHPMailer(true);

        try {
            if (($_ENV['MAIL_MAILER'] ?? 'mail') === 'smtp') {
                $mail->isSMTP();
                $mail->Host = $_ENV['MAIL_HOST'] ?? 'localhost';

                $username = $_ENV['MAIL_USERNAME'] ?? 'null';
                $password = $_ENV['MAIL_PASSWORD'] ?? 'null';

                if ($username !== 'null' && $username !== '') {
                    $mail->SMTPAuth = true;
                    $mail->Username = $username;
                    $mail->Password = $password !== 'null' ? $password : '';
                } else {
                    $mail->SMTPAuth = false;
                }

                $encryption = $_ENV['MAIL_ENCRYPTION'] ?? 'null';
                if ($encryption !== 'null' && $encryption !== '') {
                    $mail->SMTPSecure = strtolower($encryption) === 'tls' ? PHPMailer::ENCRYPTION_STARTTLS : PHPMailer::ENCRYPTION_SMTPS;
                }
                $mail->Port = $_ENV['MAIL_PORT'] ?? 2525;
            } else {
                $mail->isMail();
            }

            $fromAddress = $_ENV['MAIL_FROM_ADDRESS'] ?? 'hello@example.com';
            $fromName = $_ENV['MAIL_FROM_NAME'] ?? 'Example';

            $mail->setFrom($fromAddress, $fromName);

            foreach ($this->to as $address) {
                $mail->addAddress(trim($address));
            }

            $mail->isHTML(true);
            $mail->Subject = $mailable->subject;
            $mail->Body = $mailable->render();
            $mail->AltBody = strip_tags($mail->Body);

            return $mail->send();
        } catch (Exception $e) {
            error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
            return false;
        }
    }
}
