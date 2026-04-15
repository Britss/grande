<?php
declare(strict_types=1);

namespace App\Services;

use App\Repositories\AuditLogRepository;
use App\Repositories\PasswordResetRepository;
use App\Repositories\UserRepository;
use App\Support\Config;
use App\Support\Database;
use App\Support\Mailer;
use DateTimeImmutable;
use Throwable;

final class PasswordResetService
{
    public function __construct(
        private readonly PasswordResetRepository $passwordResets = new PasswordResetRepository(),
        private readonly UserRepository $users = new UserRepository(),
        private readonly AuditLogRepository $auditLogs = new AuditLogRepository(),
        private readonly Mailer $mailer = new Mailer(),
    ) {
    }

    public function request(string $email): array
    {
        $user = $this->users->findByEmail($email);

        if ($user === null) {
            return ['sent' => false];
        }

        $token = bin2hex(random_bytes(32));
        $tokenHash = hash('sha256', $token);
        $expiresAt = (new DateTimeImmutable('+30 minutes'))->format('Y-m-d H:i:s');

        $this->passwordResets->deleteActiveForUser((int) $user['id']);
        $this->passwordResets->create([
            'user_id' => (int) $user['id'],
            'email' => $email,
            'token_hash' => $tokenHash,
            'expires_at' => $expiresAt,
        ]);

        $resetUrl = $this->absoluteUrl('password/reset?token=' . urlencode($token));
        $delivery = $this->mailer->send(
            $email,
            'Reset your Grande password',
            $this->resetHtml((string) $user['first_name'], $resetUrl),
            $this->resetText((string) $user['first_name'], $resetUrl)
        );

        if (!(bool) Config::get('mail.smtp_enabled', false) && !(bool) Config::get('mail.use_php_mail', false)) {
            $delivery['channel'] = 'local_preview';
            $delivery['path'] = (string) Config::get('mail.log_path', '');
        }

        $this->auditLogs->log((int) $user['id'], 'password_reset_requested', 'user', (int) $user['id'], [
            'email' => $email,
            'delivery_channel' => $delivery['channel'] ?? 'unknown',
            'account_active' => (bool) ($user['is_active'] ?? true),
        ]);

        return ['sent' => true, 'delivery' => $delivery];
    }

    public function validateToken(string $token): ?array
    {
        if ($token === '') {
            return null;
        }

        $reset = $this->passwordResets->findValidByTokenHash(hash('sha256', $token));

        if ($reset === null || new DateTimeImmutable() > new DateTimeImmutable((string) $reset['expires_at'])) {
            return null;
        }

        return $reset;
    }

    public function reset(string $token, string $password): array
    {
        $reset = $this->validateToken($token);

        if ($reset === null) {
            return ['success' => false, 'message' => 'This password reset link is invalid or expired.'];
        }

        $user = $this->users->findById((int) $reset['user_id']);

        if ($user === null) {
            return ['success' => false, 'message' => 'This password reset link is invalid or expired.'];
        }

        $connection = Database::connection();

        try {
            $connection->beginTransaction();
            $this->users->updatePassword((int) $user['id'], password_hash($password, PASSWORD_DEFAULT));
            $this->passwordResets->markUsed((int) $reset['id']);
            $this->auditLogs->log((int) $user['id'], 'password_reset_completed', 'user', (int) $user['id'], [
                'email' => $user['email'],
                'account_active' => (bool) ($user['is_active'] ?? true),
            ]);
            $connection->commit();

            return ['success' => true];
        } catch (Throwable $throwable) {
            if ($connection->inTransaction()) {
                $connection->rollBack();
            }

            error_log('Password reset failed: ' . $throwable->getMessage());

            return ['success' => false, 'message' => 'Password reset failed. Please request a new link.'];
        }
    }

    private function absoluteUrl(string $path): string
    {
        $baseUrl = rtrim((string) Config::get('app.url', ''), '/');

        if ($baseUrl !== '') {
            return $baseUrl . url($path);
        }

        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';

        return $scheme . '://' . $host . url($path);
    }

    private function resetHtml(string $firstName, string $resetUrl): string
    {
        return sprintf(
            '<div style="font-family:Montserrat,Arial,sans-serif;line-height:1.6;color:#2b221c">
                <h2 style="margin-bottom:8px">Reset your Grande password</h2>
                <p>Hello %s,</p>
                <p>Use this link to set a new password:</p>
                <p><a href="%s">%s</a></p>
                <p>This link will expire in 30 minutes. If you did not request this, you can ignore this email.</p>
            </div>',
            htmlspecialchars($firstName, ENT_QUOTES, 'UTF-8'),
            htmlspecialchars($resetUrl, ENT_QUOTES, 'UTF-8'),
            htmlspecialchars($resetUrl, ENT_QUOTES, 'UTF-8')
        );
    }

    private function resetText(string $firstName, string $resetUrl): string
    {
        return "Hello {$firstName},\n\nUse this link to reset your Grande password:\n\n{$resetUrl}\n\nThis link expires in 30 minutes.\n";
    }
}
