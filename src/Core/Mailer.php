<?php

namespace Core;

use Monolog\Logger;

class Mailer
{
    private Logger $logger;
    private string $from;

    public function __construct(Logger $logger, string $from = 'noreply@pawprints.com')
    {
        $this->logger = $logger;
        $this->from = $from;
    }

    /**
     * Envía un correo electrónico.
     * En desarrollo, lo guarda en un archivo de log separado (log/emails.log).
     */
    public function send(string $to, string $subject, string $body, ?string $replyTo = null): bool
    {
        // ── Simulación en Desarrollo ─────────────────────────────────────────
        if (($_ENV['APP_ENV'] ?? 'development') !== 'production') {
            return $this->simulateSend($to, $subject, $body, $replyTo);
        }

        // ── Envío Real en Producción ──────────────────────────────────────────
        $headers = "From: {$this->from}\r\n";
        if ($replyTo) {
            $headers .= "Reply-To: {$replyTo}\r\n";
        }

        $enviado = mail($to, $subject, $body, $headers);

        if (!$enviado) {
            $lastError = error_get_last();
            $msg = $lastError['message'] ?? 'Error desconocido al ejecutar mail()';
            
            $this->logger->error("Error al enviar email real", [
                'to' => $to,
                'subject' => $subject,
                'php_error' => $msg
            ]);

            return false;
        }

        $this->logger->info("Email real enviado correctamente", ['to' => $to]);
        return true;
    }

    /**
     * Simula el envío escribiendo en log/emails.log
     */
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

        // Asegurarse de que el directorio existe
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
