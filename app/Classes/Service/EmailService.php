<?php
declare(strict_types=1);
namespace App\Service;

/**
 * @author Henrik Gebauer <mensa@henrik-gebauer.de>
 * @license https://creativecommons.org/publicdomain/zero/1.0/ CC0 1.0
 */

use PHPMailer\PHPMailer\PHPMailer;

/**
 * send emails
 */
class EmailService
{
    private $mailer = null;

    public function __construct(
        private string $host,
        private string $user,
        private string $password,
        private string $secure,
        private string $port,
        private string $fromAddress,
        private string $domain,
    ) {
        if (!$host || $host === 'log') {
            return;
        }

        $this->mailer = new PHPMailer(true);

        $this->mailer->isSMTP();
        $this->mailer->Host = $host;
        $this->mailer->SMTPAuth = true;
        $this->mailer->Username = $user;
        $this->mailer->Password = $password;
        switch ($secure) {
            case "ssl":
            case "smtps":
                $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                break;
            case "tls":
            case "starttls":
                $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                break;
            default:
                throw new \Exception('unexpected value for SMTP_SECURE');
                break;
        }
        $this->mailer->Port = $port;
        $this->mailer->setFrom($fromAddress, 'MHN-Aufnahmetool');
        $this->mailer->addReplyTo('aufnahmekommission@' . $domain, 'MHN-Aufnahmekommission');
        $this->mailer->CharSet = 'utf-8';
    }

    public function send(string $address, string $subject, string $body): bool
    {
        if ($this->mailer === null) {
            error_log("
--------------------------------------------------------------------------------
SMTP_HOST is not set in .env
Mail to: $address
Subject: $subject

$body
--------------------------------------------------------------------------------
");
            return true;
        }

        $this->mailer->ClearAddresses();
        $this->mailer->ClearCCs();
        $this->mailer->ClearBCCs();

        $this->mailer->Subject = $subject;
        $this->mailer->Body = $body;

        try {
            $this->mailer->addAddress($address);
            return $this->mailer->send();
        } catch (\Exception $e) {
            return false;
        }
    }
}
