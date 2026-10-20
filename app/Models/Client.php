<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class Client extends Model
{
    protected string $table = 'clients';
    protected bool $useSoftDelete = true;
    protected array $fillable = [
        'name',
        'phone',
        'email',
        'company',
        'address',
        'notes',
        'created_by',
        'assigned_to',
    ];

    public function getWithRelations(
        int $page = 1,
        int $perPage = 10,
        ?string $search = null,
        ?int $assignedTo = null,
        string $sort = 'created_at',
        string $order = 'DESC'
    ): array {
        $db = self::db();
        $conditions = ['c.deleted_at IS NULL'];
        $params = [];

        if (!empty($search)) {
            $conditions[] = "(c.name LIKE :search OR c.company LIKE :search OR c.email LIKE :search OR c.phone LIKE :search)";
            $params['search'] = "%{$search}%";
        }

        if (!empty($assignedTo)) {
            $conditions[] = "c.assigned_to = :assigned_to";
            $params['assigned_to'] = $assignedTo;
        }

        $whereSql = implode(' AND ', $conditions);

        // Count
        $countStmt = $db->prepare("SELECT COUNT(*) FROM {$this->getTable()} c WHERE {$whereSql}");
        $countStmt->execute($params);
        $total = (int)$countStmt->fetchColumn();

        // Allowed sort columns
        $allowedSort = ['name', 'company', 'created_at', 'id'];
        $sortColumn = in_array($sort, $allowedSort, true) ? "c.{$sort}" : "c.created_at";
        $orderDir = strtoupper($order) === 'ASC' ? 'ASC' : 'DESC';

        $offset = ($page - 1) * $perPage;

        $sql = "
            SELECT 
                c.*, 
                u.name AS assigned_user_name,
                creator.name AS created_by_name,
                (SELECT COUNT(*) FROM leads l WHERE l.client_id = c.id AND l.deleted_at IS NULL) AS leads_count,
                (SELECT COALESCE(SUM(amount), 0) FROM leads l WHERE l.client_id = c.id AND l.stage = 'won' AND l.deleted_at IS NULL) AS total_won_amount
            FROM {$this->getTable()} c
            LEFT JOIN users u ON c.assigned_to = u.id
            LEFT JOIN users creator ON c.created_by = creator.id
            WHERE {$whereSql}
            ORDER BY {$sortColumn} {$orderDir}
            LIMIT :limit OFFSET :offset
        ";

        $stmt = $db->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $totalPages = (int)ceil($total / $perPage);

        return [
            'data' => $stmt->fetchAll(),
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => max(1, $totalPages),
            'has_prev' => $page > 1,
            'has_next' => $page < $totalPages,
        ];
    }

    public function findWithDetails(int $id): ?array
    {
        $db = self::db();
        $sql = "
            SELECT 
                c.*, 
                u.name AS assigned_user_name,
                creator.name AS created_by_name
            FROM {$this->getTable()} c
            LEFT JOIN users u ON c.assigned_to = u.id
            LEFT JOIN users creator ON c.created_by = creator.id
            WHERE c.id = :id AND c.deleted_at IS NULL
            LIMIT 1
        ";
        $stmt = $db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $client = $stmt->fetch();
        return $client ?: null;
    }

    public function findDuplicates(?string $phone, ?string $email, ?int $excludeId = null): array
    {
        if (empty($phone) && empty($email)) {
            return [];
        }

        $db = self::db();
        $conditions = [];
        $params = [];

        if (!empty($phone)) {
            $conditions[] = "phone = :phone";
            $params['phone'] = $phone;
        }

        if (!empty($email)) {
            $conditions[] = "email = :email";
            $params['email'] = $email;
        }

        $whereClause = '(' . implode(' OR ', $conditions) . ') AND deleted_at IS NULL';
        if ($excludeId !== null) {
            $whereClause .= ' AND id != :exclude_id';
            $params['exclude_id'] = $excludeId;
        }

        $stmt = $db->prepare("SELECT id, name, phone, email, company FROM {$this->getTable()} WHERE {$whereClause}");
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
