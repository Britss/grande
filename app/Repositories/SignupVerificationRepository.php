<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Support\Database;

final class SignupVerificationRepository
{
    public function upsert(array $data): void
    {
        $statement = Database::connection()->prepare(
            'INSERT INTO signup_verifications (email, first_name, last_name, phone, password, verification_code, expires_at)
             VALUES (:email, :first_name, :last_name, :phone, :password, :verification_code, :expires_at)
             ON DUPLICATE KEY UPDATE
                first_name = VALUES(first_name),
                last_name = VALUES(last_name),
                phone = VALUES(phone),
                password = VALUES(password),
                verification_code = VALUES(verification_code),
                expires_at = VALUES(expires_at),
                updated_at = CURRENT_TIMESTAMP'
        );

        $statement->execute([
            'email' => $data['email'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'phone' => $data['phone'],
            'password' => $data['password'],
            'verification_code' => $data['verification_code'],
            'expires_at' => $data['expires_at'],
        ]);
    }

    public function findByEmail(string $email): ?array
    {
        $statement = Database::connection()->prepare(
            'SELECT id, email, first_name, last_name, phone, password, verification_code, expires_at
             FROM signup_verifications
             WHERE email = :email
             LIMIT 1'
        );
        $statement->execute(['email' => $email]);
        $record = $statement->fetch();

        return is_array($record) ? $record : null;
    }

    public function deleteByEmail(string $email): void
    {
        $statement = Database::connection()->prepare('DELETE FROM signup_verifications WHERE email = :email');
        $statement->execute(['email' => $email]);
    }
}
