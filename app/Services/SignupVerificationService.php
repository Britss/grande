<?php
declare(strict_types=1);

namespace App\Services;

use App\Repositories\SignupVerificationRepository;
use App\Repositories\UserRepository;
use App\Support\Config;
use App\Support\Database;
use App\Support\Mailer;
use DateTimeImmutable;
use Throwable;

final class SignupVerificationService
{
    public function __construct(
        private readonly SignupVerificationRepository $verifications = new SignupVerificationRepository(),
        private readonly UserRepository $users = new UserRepository(),
        private readonly Mailer $mailer = new Mailer(),
    ) {
    }

    public function begin(array $input): array
    {
        $code = (string) random_int(100000, 999999);
        $expiresAt = (new DateTimeImmutable('+15 minutes'))->format('Y-m-d H:i:s');
        $password = !empty($input['is_hashed_password'])
            ? (string) $input['password']
            : password_hash((string) $input['password'], PASSWORD_DEFAULT);

        $this->verifications->upsert([
            'email' => $input['email'],
            'first_name' => $input['first_name'],
            'last_name' => $input['last_name'],
            'phone' => $input['phone'],
            'password' => $password,
            'verification_code' => $code,
            'expires_at' => $expiresAt,
        ]);

        $subject = 'Your Grande verification code';
        $htmlBody = $this->verificationHtml($input['first_name'], $code);
        $plainText = $this->verificationText($input['first_name'], $code);

        $delivery = $this->mailer->send($input['email'], $subject, $htmlBody, $plainText);

        if (!(bool) Config::get('mail.smtp_enabled', false) && !(bool) Config::get('mail.use_php_mail', false)) {
            $delivery['channel'] = 'local_preview';
            $delivery['preview_code'] = $code;
            $delivery['path'] = (string) Config::get('mail.log_path', '');
        }

        return $delivery;
    }

    public function resend(string $email): ?array
    {
        $pending = $this->verifications->findByEmail($email);

        if ($pending === null) {
            return null;
        }

        return $this->begin([
            'first_name' => $pending['first_name'],
            'last_name' => $pending['last_name'],
            'email' => $pending['email'],
            'phone' => $pending['phone'],
            // keep existing hash by bypassing begin hash? handled below?
            'password' => $pending['password'],
            'is_hashed_password' => true,
        ]);
    }

    public function previewCodeFor(string $email): ?string
    {
        $pending = $this->verifications->findByEmail($email);

        return is_array($pending) ? (string) ($pending['verification_code'] ?? '') : null;
    }

    public function verify(string $email, string $code): array
    {
        $pending = $this->verifications->findByEmail($email);

        if ($pending === null) {
            return ['success' => false, 'message' => 'No pending verification was found for that email.'];
        }

        if (new DateTimeImmutable() > new DateTimeImmutable((string) $pending['expires_at'])) {
            $this->verifications->deleteByEmail($email);

            return ['success' => false, 'message' => 'That verification code has expired. Please request a new one.'];
        }

        if (!hash_equals((string) $pending['verification_code'], $code)) {
            return ['success' => false, 'message' => 'The verification code is invalid.'];
        }

        if ($this->users->emailExists($email)) {
            $this->verifications->deleteByEmail($email);

            return ['success' => false, 'message' => 'This email address is already registered.'];
        }

        if ($this->users->phoneExists((string) $pending['phone'])) {
            $this->verifications->deleteByEmail($email);

            return ['success' => false, 'message' => 'This phone number is already registered.'];
        }

        $connection = Database::connection();

        try {
            $connection->beginTransaction();

            $this->users->create([
                'first_name' => $pending['first_name'],
                'last_name' => $pending['last_name'],
                'email' => $pending['email'],
                'phone' => $pending['phone'],
                'password' => $pending['password'],
                'role' => 'customer',
                'is_active' => 1,
            ]);

            $this->verifications->deleteByEmail($email);
            $connection->commit();

            return ['success' => true];
        } catch (Throwable $throwable) {
            if ($connection->inTransaction()) {
                $connection->rollBack();
            }

            error_log('Signup verification finalize failed: ' . $throwable->getMessage());

            return ['success' => false, 'message' => 'Account creation failed after verification. Please try again.'];
        }
    }

    private function verificationHtml(string $firstName, string $code): string
    {
        $siteName = (string) Config::get('app.name', 'Grande.');

        return sprintf(
            '<div style="font-family:Montserrat,Arial,sans-serif;line-height:1.6;color:#2b221c">
                <h2 style="margin-bottom:8px">%s account verification</h2>
                <p>Hello %s,</p>
                <p>Use the verification code below to finish creating your account:</p>
                <p style="font-size:32px;font-weight:700;letter-spacing:6px;color:#4b3e34">%s</p>
                <p>This code will expire in 15 minutes.</p>
                <p>If you did not request this, you can ignore this email.</p>
            </div>',
            htmlspecialchars($siteName, ENT_QUOTES, 'UTF-8'),
            htmlspecialchars($firstName, ENT_QUOTES, 'UTF-8'),
            htmlspecialchars($code, ENT_QUOTES, 'UTF-8')
        );
    }

    private function verificationText(string $firstName, string $code): string
    {
        return "Hello {$firstName},\n\nUse this verification code to finish creating your Grande account:\n\n{$code}\n\nThis code expires in 15 minutes.\n";
    }
}
