<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class Lead extends Model
{
    protected string $table = 'leads';
    protected bool $useSoftDelete = true;
    protected array $fillable = [
        'client_id',
        'user_id',
        'title',
        'amount',
        'stage',
        'source',
        'notes',
        'next_contact_at',
        'lost_reason',
    ];

    public const STAGES = [
        'new' => 'Yangi',
        'contact' => 'Aloqa',
        'proposal' => 'Taklif',
        'won' => 'Yakun (Yutuq)',
        'lost' => 'Yoʻqotilgan',
    ];

    public function getWithRelations(
        int $page = 1,
        int $perPage = 15,
        ?string $search = null,
        ?string $stage = null,
        ?int $userId = null,
        ?string $minAmount = null,
        ?string $maxAmount = null,
        string $sort = 'created_at',
        string $order = 'DESC'
    ): array {
        $db = self::db();
        $conditions = ['l.deleted_at IS NULL'];
        $params = [];

        if (!empty($search)) {
            $conditions[] = "(l.title LIKE :search OR c.name LIKE :search OR c.company LIKE :search)";
            $params['search'] = "%{$search}%";
        }

        if (!empty($stage)) {
            $conditions[] = "l.stage = :stage";
            $params['stage'] = $stage;
        }

        if (!empty($userId)) {
            $conditions[] = "l.user_id = :user_id";
            $params['user_id'] = $userId;
        }

        if ($minAmount !== null && $minAmount !== '') {
            $conditions[] = "l.amount >= :min_amount";
            $params['min_amount'] = (float)$minAmount;
        }

        if ($maxAmount !== null && $maxAmount !== '') {
            $conditions[] = "l.amount <= :max_amount";
            $params['max_amount'] = (float)$maxAmount;
        }

        $whereSql = implode(' AND ', $conditions);

        $countStmt = $db->prepare("
            SELECT COUNT(*) 
            FROM {$this->getTable()} l 
            JOIN clients c ON l.client_id = c.id 
            WHERE {$whereSql}
        ");
        $countStmt->execute($params);
        $total = (int)$countStmt->fetchColumn();

        $allowedSort = ['title', 'amount', 'stage', 'created_at', 'next_contact_at'];
        $sortColumn = in_array($sort, $allowedSort, true) ? "l.{$sort}" : "l.created_at";
        $orderDir = strtoupper($order) === 'ASC' ? 'ASC' : 'DESC';

        $offset = ($page - 1) * $perPage;

        $sql = "
            SELECT 
                l.*, 
                c.name AS client_name, 
                c.company AS client_company,
                c.phone AS client_phone,
                u.name AS manager_name
            FROM {$this->getTable()} l
            JOIN clients c ON l.client_id = c.id
            JOIN users u ON l.user_id = u.id
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
                l.*, 
                c.name AS client_name, 
                c.company AS client_company,
                c.phone AS client_phone,
                c.email AS client_email,
                u.name AS manager_name
            FROM {$this->getTable()} l
            JOIN clients c ON l.client_id = c.id
            JOIN users u ON l.user_id = u.id
            WHERE l.id = :id AND l.deleted_at IS NULL
            LIMIT 1
        ";
        $stmt = $db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function getPipelineGrouped(?int $userId = null): array
    {
        $db = self::db();
        $conditions = ['l.deleted_at IS NULL'];
        $params = [];

        if (!empty($userId)) {
            $conditions[] = 'l.user_id = :user_id';
            $params['user_id'] = $userId;
        }

        $whereSql = implode(' AND ', $conditions);

        $sql = "
            SELECT 
                l.*, 
                c.name AS client_name, 
                c.company AS client_company,
                u.name AS manager_name
            FROM {$this->getTable()} l
            JOIN clients c ON l.client_id = c.id
            JOIN users u ON l.user_id = u.id
            WHERE {$whereSql}
            ORDER BY l.created_at DESC
        ";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $all = $stmt->fetchAll();

        $grouped = [
            'new' => [],
            'contact' => [],
            'proposal' => [],
            'won' => [],
            'lost' => [],
        ];

        foreach ($all as $lead) {
            $stage = $lead['stage'];
            if (isset($grouped[$stage])) {
                $grouped[$stage][] = $lead;
            }
        }

        return $grouped;
    }
}
