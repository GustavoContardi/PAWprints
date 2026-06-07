<?php

namespace Core;

use Monolog\Logger;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception as PHPMailerException;

class Mailer
{
    private Logger $logger;
    private string $from;

    public function __construct(Logger $logger, string $from = 'noreply@pawprints.com')
    {
        $this->logger = $logger;
        $this->from = $from;
    }

    public function send(string $to, string $subject, string $body, ?string $replyTo = null): bool
    {
        $driver = $_ENV['MAIL_DRIVER'] ?? 'log';

        if ($driver === 'smtp') {
            return $this->sendSmtp($to, $subject, $body, $replyTo);
        }

        return $this->simulateSend($to, $subject, $body, $replyTo);
    }

    private function sendSmtp(string $to, string $subject, string $body, ?string $replyTo): bool
    {
        $host     = $_ENV['MAIL_HOST'] ?? '';
        $port     = (int)($_ENV['MAIL_PORT'] ?? 587);
        $username = $_ENV['MAIL_USERNAME'] ?? '';
        $password = $_ENV['MAIL_PASSWORD'] ?? '';
        $enc      = $_ENV['MAIL_ENCRYPTION'] ?? 'tls';

        if ($host === '' || $username === '' || $password === '') {
            $this->logger->warning('Mail driver SMTP configurado pero faltan credenciales. Cayendo a simulación.', [
                'host' => $host,
                'user' => $username ? '***' : '',
            ]);
            return $this->simulateSend($to, $subject, $body, $replyTo);
        }

        try {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host       = $host;
            $mail->Port       = $port;
            $mail->SMTPAuth   = true;
            $mail->Username   = $username;
            $mail->Password   = $password;
            $mail->CharSet    = 'UTF-8';

            if ($enc === 'tls') {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            } elseif ($enc === 'ssl') {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            }

            $fromName = $_ENV['MAIL_FROM_NAME'] ?? 'PAWprints';
            $mail->setFrom($this->from, $fromName);
            $mail->addAddress($to);

            if ($replyTo) {
                $mail->addReplyTo($replyTo);
            }

            $mail->isHTML(false);
            $mail->Subject = $subject;
            $mail->Body    = $body;

            $mail->send();

            $this->logger->info("Email enviado correctamente vía SMTP", [
                'to' => $to,
                'subject' => $subject,
            ]);
            return true;

        } catch (PHPMailerException $e) {
            $this->logger->error("Error al enviar email vía SMTP", [
                'to'      => $to,
                'subject' => $subject,
                'error'   => $mail->ErrorInfo,
            ]);
            return false;
        }
    }

    private function simulateSend(string $to, string $subject, string $body, ?string $replyTo): bool
    {
        $logPath = __DIR__ . '/../../log/emails.log';
        $timestamp = date('Y-m-d H:i:s');

        $content = "================================================================\n";
        $content .= "FECHA: $timestamp\n";
        $content .= "PARA: $to\n";
        $content .= "DE: {$this->from}\n";
        if ($replyTo) $content .= "REPLY-TO: $replyTo\n";
        $content .= "ASUNTO: $subject\n";
        $content .= "----------------------------------------------------------------\n";
        $content .= "$body\n";
        $content .= "================================================================\n\n";

        if (!is_dir(dirname($logPath))) {
            mkdir(dirname($logPath), 0777, true);
        }

        $result = file_put_contents($logPath, $content, FILE_APPEND);

        if ($result !== false) {
            $this->logger->info("Email simulado guardado en log/emails.log", ['to' => $to]);
            return true;
        }

        $this->logger->error("Error al escribir en log/emails.log");
        return false;
    }
}
