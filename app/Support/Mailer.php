<?php
declare(strict_types=1);

namespace App\Support;

use Exception;

final class Mailer
{
    public function send(string $to, string $subject, string $htmlBody, string $plainText): array
    {
        if ((bool) Config::get('mail.smtp_enabled', false)) {
            $smtpSent = $this->sendViaSmtp($to, $subject, $htmlBody, $plainText);

            if ($smtpSent === true) {
                return ['ok' => true, 'channel' => 'smtp'];
            }
        }

        if ((bool) Config::get('mail.use_php_mail', false)) {
            $mailSent = @mail($to, $subject, $plainText, $this->plainHeaders());

            if ($mailSent) {
                return ['ok' => true, 'channel' => 'mail'];
            }
        }

        $logPath = (string) Config::get('mail.log_path');
        $this->logEmail($logPath, $to, $subject);

        return [
            'ok' => true,
            'channel' => 'log',
            'path' => $logPath,
        ];
    }

    private function sendViaSmtp(string $to, string $subject, string $htmlBody, string $plainText): bool
    {
        $host = (string) Config::get('mail.host', '');
        $port = (int) Config::get('mail.port', 587);
        $encryption = (string) Config::get('mail.encryption', 'tls');
        $username = (string) Config::get('mail.username', '');
        $password = (string) Config::get('mail.password', '');
        $fromEmail = (string) Config::get('mail.from_email', '');
        $fromName = (string) Config::get('mail.from_name', '');

        if ($host === '' || $username === '' || $password === '' || $fromEmail === '') {
            return false;
        }

        $transport = $encryption === 'ssl' ? 'ssl://' : '';
        $socket = @stream_socket_client(
            $transport . $host . ':' . $port,
            $errno,
            $errstr,
            15,
            STREAM_CLIENT_CONNECT
        );

        if (!is_resource($socket)) {
            error_log('SMTP connection failed: ' . $errstr . ' (' . $errno . ')');
            return false;
        }

        stream_set_timeout($socket, 15);

        try {
            $this->smtpExpect($socket, [220]);
            $this->smtpCommand($socket, 'EHLO localhost', [250]);

            if ($encryption === 'tls') {
                $this->smtpCommand($socket, 'STARTTLS', [220]);

                if (!@stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
                    throw new Exception('Unable to enable TLS encryption');
                }

                $this->smtpCommand($socket, 'EHLO localhost', [250]);
            }

            $this->smtpCommand($socket, 'AUTH LOGIN', [334]);
            $this->smtpCommand($socket, base64_encode($username), [334]);
            $this->smtpCommand($socket, base64_encode($password), [235]);
            $this->smtpCommand($socket, 'MAIL FROM:<' . $fromEmail . '>', [250]);
            $this->smtpCommand($socket, 'RCPT TO:<' . $to . '>', [250, 251]);
            $this->smtpCommand($socket, 'DATA', [354]);

            $boundary = 'b-' . bin2hex(random_bytes(12));
            $headers = [
                'Date: ' . date('r'),
                'From: ' . $this->formatAddress($fromEmail, $fromName),
                'To: ' . $this->formatAddress($to, $to),
                'Subject: ' . $subject,
                'MIME-Version: 1.0',
                'Content-Type: multipart/alternative; boundary="' . $boundary . '"',
            ];

            $body = implode("\r\n", $headers) . "\r\n\r\n"
                . '--' . $boundary . "\r\n"
                . "Content-Type: text/plain; charset=UTF-8\r\n\r\n"
                . $plainText . "\r\n\r\n"
                . '--' . $boundary . "\r\n"
                . "Content-Type: text/html; charset=UTF-8\r\n\r\n"
                . $htmlBody . "\r\n\r\n"
                . '--' . $boundary . "--\r\n.";

            fwrite($socket, $body . "\r\n");
            $this->smtpExpect($socket, [250]);
            $this->smtpCommand($socket, 'QUIT', [221]);
            fclose($socket);

            return true;
        } catch (Exception $exception) {
            error_log('SMTP send failed: ' . $exception->getMessage());

            if (is_resource($socket)) {
                @fwrite($socket, "QUIT\r\n");
                fclose($socket);
            }

            return false;
        }
    }

    private function smtpCommand($socket, string $command, array $expectedCodes): void
    {
        fwrite($socket, $command . "\r\n");
        $this->smtpExpect($socket, $expectedCodes);
    }

    private function smtpExpect($socket, array $expectedCodes): void
    {
        $response = '';

        while (($line = fgets($socket, 515)) !== false) {
            $response .= $line;

            if (strlen($line) < 4 || $line[3] !== '-') {
                break;
            }
        }

        $code = (int) substr($response, 0, 3);

        if ($response === '' || !in_array($code, $expectedCodes, true)) {
            throw new Exception(trim($response) ?: 'Empty SMTP response');
        }
    }

    private function plainHeaders(): string
    {
        $fromEmail = (string) Config::get('mail.from_email', '');
        $fromName = (string) Config::get('mail.from_name', '');

        return implode("\r\n", [
            'MIME-Version: 1.0',
            'Content-Type: text/plain; charset=UTF-8',
            'From: ' . $this->formatAddress($fromEmail, $fromName),
        ]);
    }

    private function formatAddress(string $email, string $name): string
    {
        return sprintf('"%s" <%s>', addcslashes($name, '"'), $email);
    }

    private function logEmail(string $path, string $to, string $subject): void
    {
        $directory = dirname($path);

        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        $entry = implode(PHP_EOL, [
            str_repeat('=', 72),
            '[' . date('Y-m-d H:i:s') . ']',
            'TO: ' . $to,
            'SUBJECT: ' . $subject,
            '',
            'Email body omitted from logs because it may contain private verification or reset credentials.',
            '',
        ]);

        file_put_contents($path, $entry, FILE_APPEND);
    }
}
