<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Support\Database;

final class PasswordResetRepository
{
    public function deleteActiveForUser(int $userId): void
    {
        $statement = Database::connection()->prepare(
            'UPDATE password_resets
             SET used_at = CURRENT_TIMESTAMP
             WHERE user_id = :user_id
               AND used_at IS NULL'
        );
        $statement->execute(['user_id' => $userId]);
    }

    public function create(array $data): void
    {
        $statement = Database::connection()->prepare(
            'INSERT INTO password_resets (user_id, email, token_hash, expires_at)
             VALUES (:user_id, :email, :token_hash, :expires_at)'
        );
        $statement->execute([
            'user_id' => $data['user_id'],
            'email' => $data['email'],
            'token_hash' => $data['token_hash'],
            'expires_at' => $data['expires_at'],
        ]);
    }

    public function findValidByTokenHash(string $tokenHash): ?array
    {
        $statement = Database::connection()->prepare(
            'SELECT id, user_id, email, token_hash, expires_at, used_at
             FROM password_resets
             WHERE token_hash = :token_hash
               AND used_at IS NULL
             LIMIT 1'
        );
        $statement->execute(['token_hash' => $tokenHash]);
        $reset = $statement->fetch();

        return is_array($reset) ? $reset : null;
    }

    public function markUsed(int $resetId): void
    {
        $statement = Database::connection()->prepare(
            'UPDATE password_resets
             SET used_at = CURRENT_TIMESTAMP
             WHERE id = :id
             LIMIT 1'
        );
        $statement->execute(['id' => $resetId]);
    }
}
