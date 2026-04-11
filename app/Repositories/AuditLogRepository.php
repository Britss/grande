<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Support\Database;

final class AuditLogRepository
{
    public function log(?int $actorUserId, string $action, string $entityType, ?int $entityId = null, array $details = []): void
    {
        $statement = Database::connection()->prepare(
            'INSERT INTO audit_logs (actor_user_id, action, entity_type, entity_id, details_json)
             VALUES (:actor_user_id, :action, :entity_type, :entity_id, :details_json)'
        );
        $statement->execute([
            'actor_user_id' => $actorUserId,
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'details_json' => $details === [] ? null : json_encode($details, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
        ]);
    }

    public function latest(int $limit = 20): array
    {
        $statement = Database::connection()->prepare(
            'SELECT
                al.id,
                al.action,
                al.entity_type,
                al.entity_id,
                al.details_json,
                al.created_at,
                u.first_name,
                u.last_name,
                u.role
             FROM audit_logs al
             LEFT JOIN users u ON u.id = al.actor_user_id
             ORDER BY al.created_at DESC, al.id DESC
             LIMIT :limit'
        );
        $statement->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $statement->execute();
        $rows = $statement->fetchAll();

        foreach ($rows as &$row) {
            $row['details'] = [];

            if (is_string($row['details_json']) && $row['details_json'] !== '') {
                $decoded = json_decode($row['details_json'], true);

                if (is_array($decoded)) {
                    $row['details'] = $decoded;
                }
            }
        }

        return $rows;
    }
}
