<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Support\Database;

final class FeedbackRepository
{
    public function create(array $data): int
    {
        $statement = Database::connection()->prepare(
            'INSERT INTO feedback (user_id, name, email, rating, category, message, status)
             VALUES (:user_id, :name, :email, :rating, :category, :message, :status)'
        );
        $statement->execute([
            'user_id' => $data['user_id'] ?? null,
            'name' => $data['name'],
            'email' => $data['email'],
            'rating' => $data['rating'],
            'category' => $data['category'],
            'message' => $data['message'],
            'status' => $data['status'] ?? 'new',
        ]);

        return (int) Database::connection()->lastInsertId();
    }

    public function getManagementStats(): array
    {
        $statement = Database::connection()->query(
            'SELECT
                COUNT(*) AS total_feedback,
                SUM(CASE WHEN status = \'new\' THEN 1 ELSE 0 END) AS new_feedback,
                SUM(CASE WHEN status = \'in_review\' THEN 1 ELSE 0 END) AS in_review_feedback,
                SUM(CASE WHEN status = \'resolved\' THEN 1 ELSE 0 END) AS resolved_feedback,
                ROUND(AVG(rating), 1) AS average_rating
             FROM feedback'
        );

        $stats = $statement->fetch();

        return is_array($stats) ? $stats : [
            'total_feedback' => 0,
            'new_feedback' => 0,
            'in_review_feedback' => 0,
            'resolved_feedback' => 0,
            'average_rating' => 0,
        ];
    }

    public function getManageableFeedback(int $limit = 50): array
    {
        $statement = Database::connection()->prepare(
            'SELECT
                f.id,
                f.user_id,
                f.name,
                f.email,
                f.rating,
                f.category,
                f.message,
                f.status,
                f.created_at,
                f.updated_at,
                u.first_name AS user_first_name,
                u.last_name AS user_last_name
             FROM feedback f
             LEFT JOIN users u ON u.id = f.user_id
             ORDER BY
                FIELD(f.status, \'new\', \'in_review\', \'resolved\', \'archived\'),
                f.created_at DESC
             LIMIT :limit'
        );
        $statement->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll();
    }

    public function updateStatus(int $feedbackId, string $status): array
    {
        $allowed = ['new', 'in_review', 'resolved', 'archived'];

        if (!in_array($status, $allowed, true)) {
            throw new \RuntimeException('Invalid feedback status.');
        }

        $statement = Database::connection()->prepare(
            'UPDATE feedback
             SET status = :status
             WHERE id = :id
             LIMIT 1'
        );
        $statement->execute([
            'status' => $status,
            'id' => $feedbackId,
        ]);

        if ($statement->rowCount() < 1) {
            $existing = $this->findById($feedbackId);

            if ($existing === null) {
                throw new \RuntimeException('Feedback record was not found.');
            }
        }

        $feedback = $this->findById($feedbackId);

        if ($feedback === null) {
            throw new \RuntimeException('Feedback record was not found.');
        }

        return $feedback;
    }

    public function getRecentForUser(int $userId, int $limit = 10): array
    {
        $statement = Database::connection()->prepare(
            'SELECT id, rating, category, message, status, created_at
             FROM feedback
             WHERE user_id = :user_id
             ORDER BY created_at DESC
             LIMIT :limit'
        );
        $statement->bindValue(':user_id', $userId, \PDO::PARAM_INT);
        $statement->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll();
    }

    public function findById(int $feedbackId): ?array
    {
        $statement = Database::connection()->prepare(
            'SELECT
                f.id,
                f.user_id,
                f.name,
                f.email,
                f.rating,
                f.category,
                f.message,
                f.status,
                f.created_at,
                f.updated_at,
                u.first_name AS user_first_name,
                u.last_name AS user_last_name
             FROM feedback f
             LEFT JOIN users u ON u.id = f.user_id
             WHERE f.id = :id
             LIMIT 1'
        );
        $statement->execute(['id' => $feedbackId]);
        $feedback = $statement->fetch();

        return is_array($feedback) ? $feedback : null;
    }
}
