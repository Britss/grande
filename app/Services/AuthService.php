<?php
declare(strict_types=1);

namespace App\Services;

use App\Repositories\UserRepository;

final class AuthService
{
    public function __construct(private readonly UserRepository $users = new UserRepository())
    {
    }

    public function attemptLogin(string $email, string $password): array
    {
        $user = $this->users->findByEmail($email);

        if ($user === null || !password_verify($password, (string) $user['password'])) {
            return [
                'success' => false,
                'message' => 'The provided credentials do not match our records.',
            ];
        }

        if (!(bool) ($user['is_active'] ?? true)) {
            return [
                'success' => false,
                'message' => 'This account is currently inactive.',
            ];
        }

        return [
            'success' => true,
            'user' => $user,
        ];
    }

    public function emailExists(string $email): bool
    {
        return $this->users->emailExists($email);
    }

    public function phoneExists(string $phone): bool
    {
        return $this->users->phoneExists($phone);
    }

    public function registerCustomer(array $data): int
    {
        return $this->users->create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
            'role' => 'customer',
            'is_active' => 1,
        ]);
    }
}
