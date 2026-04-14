<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Support\Database;

final class UserRepository
{
    public function findById(int $userId): ?array
    {
        $statement = Database::connection()->prepare(
            'SELECT id, first_name, last_name, email, phone, password, role, is_active, profile_picture, created_at
             FROM users
             WHERE id = :id
             LIMIT 1'
        );
        $statement->execute(['id' => $userId]);
        $user = $statement->fetch();

        return is_array($user) ? $user : null;
    }

    public function findByEmail(string $email): ?array
    {
        $statement = Database::connection()->prepare(
            'SELECT id, first_name, last_name, email, phone, password, role, is_active, profile_picture, created_at
             FROM users
             WHERE email = :email
             LIMIT 1'
        );
        $statement->execute(['email' => $email]);
        $user = $statement->fetch();

        return is_array($user) ? $user : null;
    }

    public function emailExists(string $email): bool
    {
        $statement = Database::connection()->prepare('SELECT COUNT(*) FROM users WHERE email = :email');
        $statement->execute(['email' => $email]);

        return (int) $statement->fetchColumn() > 0;
    }

    public function phoneExists(string $phone): bool
    {
        $statement = Database::connection()->prepare('SELECT COUNT(*) FROM users WHERE phone = :phone');
        $statement->execute(['phone' => $phone]);

        return (int) $statement->fetchColumn() > 0;
    }

    public function emailExistsExcept(string $email, int $userId): bool
    {
        $statement = Database::connection()->prepare(
            'SELECT COUNT(*) FROM users WHERE email = :email AND id <> :id'
        );
        $statement->execute([
            'email' => $email,
            'id' => $userId,
        ]);

        return (int) $statement->fetchColumn() > 0;
    }

    public function phoneExistsExcept(string $phone, int $userId): bool
    {
        $statement = Database::connection()->prepare(
            'SELECT COUNT(*) FROM users WHERE phone = :phone AND id <> :id'
        );
        $statement->execute([
            'phone' => $phone,
            'id' => $userId,
        ]);

        return (int) $statement->fetchColumn() > 0;
    }

    public function create(array $data): int
    {
        $statement = Database::connection()->prepare(
            'INSERT INTO users (first_name, last_name, email, phone, password, role, is_active)
             VALUES (:first_name, :last_name, :email, :phone, :password, :role, :is_active)'
        );
        $statement->execute([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password' => $data['password'],
            'role' => $data['role'] ?? 'customer',
            'is_active' => $data['is_active'] ?? 1,
        ]);

        return (int) Database::connection()->lastInsertId();
    }

    public function listForManagement(): array
    {
        $statement = Database::connection()->query(
            'SELECT id, first_name, last_name, email, phone, role, is_active, profile_picture, created_at
             FROM users
             ORDER BY FIELD(role, \'admin\', \'employee\', \'customer\'), created_at DESC, id DESC'
        );

        return $statement->fetchAll();
    }

    public function getManagementStats(): array
    {
        $statement = Database::connection()->query(
            'SELECT
                SUM(CASE WHEN role = \'admin\' THEN 1 ELSE 0 END) AS admin_count,
                SUM(CASE WHEN role = \'employee\' THEN 1 ELSE 0 END) AS employee_count,
                SUM(CASE WHEN role = \'customer\' THEN 1 ELSE 0 END) AS customer_count,
                SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) AS active_count
             FROM users'
        );
        $stats = $statement->fetch();

        return is_array($stats) ? $stats : [
            'admin_count' => 0,
            'employee_count' => 0,
            'customer_count' => 0,
            'active_count' => 0,
        ];
    }

    public function customersForJson(string $status = 'active'): array
    {
        $where = match ($status) {
            'inactive' => "WHERE u.role = 'customer' AND u.is_active = 0",
            'all' => "WHERE u.role = 'customer'",
            default => "WHERE u.role = 'customer' AND u.is_active = 1",
        };

        $statement = Database::connection()->query(
            "SELECT
                u.id,
                u.first_name,
                u.last_name,
                CONCAT(u.first_name, ' ', u.last_name) AS full_name,
                u.email,
                u.phone,
                u.created_at,
                u.is_active,
                COUNT(DISTINCT o.id) AS order_count,
                COALESCE(SUM(CASE WHEN o.status <> 'cancelled' THEN o.total_amount ELSE 0 END), 0) AS total_spent
             FROM users u
             LEFT JOIN orders o ON o.user_id = u.id
             {$where}
             GROUP BY u.id, u.first_name, u.last_name, u.email, u.phone, u.created_at, u.is_active
             ORDER BY u.created_at DESC"
        );

        return array_map(static fn (array $customer): array => [
            'id' => (int) ($customer['id'] ?? 0),
            'first_name' => (string) ($customer['first_name'] ?? ''),
            'last_name' => (string) ($customer['last_name'] ?? ''),
            'full_name' => (string) ($customer['full_name'] ?? ''),
            'email' => (string) ($customer['email'] ?? ''),
            'phone' => (string) ($customer['phone'] ?? ''),
            'created_at' => (string) ($customer['created_at'] ?? ''),
            'is_active' => (int) ($customer['is_active'] ?? 0),
            'order_count' => (int) ($customer['order_count'] ?? 0),
            'total_spent' => (float) ($customer['total_spent'] ?? 0),
        ], $statement->fetchAll() ?: []);
    }

    public function updateManagement(int $userId, array $data): array
    {
        $statement = Database::connection()->prepare(
            'UPDATE users
             SET
                first_name = :first_name,
                last_name = :last_name,
                email = :email,
                phone = :phone,
                role = :role,
                is_active = :is_active
             WHERE id = :id
             LIMIT 1'
        );
        $statement->execute([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'role' => $data['role'],
            'is_active' => $data['is_active'],
            'id' => $userId,
        ]);

        $user = $this->findById($userId);

        if ($user === null) {
            throw new \RuntimeException('User not found.');
        }

        return $user;
    }

    public function deactivateCustomerForCompatibility(int $userId): array
    {
        $user = $this->findById($userId);

        if ($user === null) {
            throw new \RuntimeException('Customer not found.');
        }

        if (($user['role'] ?? null) !== 'customer') {
            throw new \RuntimeException('Cannot delete non-customer accounts.');
        }

        $connection = Database::connection();
        $connection->beginTransaction();

        try {
            $deactivate = $connection->prepare(
                'UPDATE users
                 SET is_active = 0
                 WHERE id = :id
                   AND role = \'customer\'
                 LIMIT 1'
            );
            $deactivate->execute(['id' => $userId]);

            $clearCart = $connection->prepare('DELETE FROM cart_items WHERE user_id = :user_id');
            $clearCart->execute(['user_id' => $userId]);

            $connection->commit();
        } catch (\Throwable $exception) {
            $connection->rollBack();
            throw $exception;
        }

        $updatedUser = $this->findById($userId);

        if ($updatedUser === null) {
            throw new \RuntimeException('Customer not found.');
        }

        return [
            'before' => $user,
            'after' => $updatedUser,
        ];
    }

    public function updateCustomerProfile(int $userId, array $data): array
    {
        $statement = Database::connection()->prepare(
            'UPDATE users
             SET
                first_name = :first_name,
                last_name = :last_name,
                email = :email,
                phone = :phone
             WHERE id = :id
               AND role = \'customer\'
             LIMIT 1'
        );
        $statement->execute([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'id' => $userId,
        ]);

        $user = $this->findById($userId);

        if ($user === null || ($user['role'] ?? null) !== 'customer') {
            throw new \RuntimeException('Customer profile not found.');
        }

        return $user;
    }

    public function updatePassword(int $userId, string $passwordHash): void
    {
        $statement = Database::connection()->prepare(
            'UPDATE users
             SET password = :password
             WHERE id = :id
             LIMIT 1'
        );
        $statement->execute([
            'password' => $passwordHash,
            'id' => $userId,
        ]);
    }

    public function updateCustomerProfilePicture(int $userId, string $profilePicture): array
    {
        $statement = Database::connection()->prepare(
            'UPDATE users
             SET profile_picture = :profile_picture
             WHERE id = :id
               AND role = \'customer\'
             LIMIT 1'
        );
        $statement->execute([
            'profile_picture' => $profilePicture,
            'id' => $userId,
        ]);

        $user = $this->findById($userId);

        if ($user === null || ($user['role'] ?? null) !== 'customer') {
            throw new \RuntimeException('Customer profile not found.');
        }

        return $user;
    }
}
