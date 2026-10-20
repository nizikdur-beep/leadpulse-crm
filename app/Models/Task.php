<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class Task extends Model
{
    protected string $table = 'tasks';
    protected array $fillable = [
        'lead_id',
        'client_id',
        'user_id',
        'title',
        'description',
        'due_date',
        'priority',
        'status',
    ];

    public const STATUSES = [
        'open' => 'Ochiq',
        'in_progress' => 'Jarayonda',
        'done' => 'Bajarildi',
    ];

    public const PRIORITIES = [
        'low' => 'Past',
        'medium' => 'Oʻrta',
        'high' => 'Yuqori',
    ];

    public function getWithRelations(
        int $page = 1,
        int $perPage = 15,
        ?string $search = null,
        ?string $status = null,
        ?string $priority = null,
        ?int $userId = null,
        string $sort = 'due_date',
        string $order = 'ASC'
    ): array {
        $db = self::db();
        $conditions = [];
        $params = [];

        if (!empty($search)) {
            $conditions[] = "(t.title LIKE :search OR t.description LIKE :search)";
            $params['search'] = "%{$search}%";
        }

        if (!empty($status)) {
            $conditions[] = "t.status = :status";
            $params['status'] = $status;
        }

        if (!empty($priority)) {
            $conditions[] = "t.priority = :priority";
            $params['priority'] = $priority;
        }

        if (!empty($userId)) {
            $conditions[] = "t.user_id = :user_id";
            $params['user_id'] = $userId;
        }

        $whereSql = !empty($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';

        $countStmt = $db->prepare("SELECT COUNT(*) FROM {$this->getTable()} t {$whereSql}");
        $countStmt->execute($params);
        $total = (int)$countStmt->fetchColumn();

        $allowedSort = ['due_date', 'priority', 'status', 'created_at'];
        $sortColumn = in_array($sort, $allowedSort, true) ? "t.{$sort}" : "t.due_date";
        $orderDir = strtoupper($order) === 'DESC' ? 'DESC' : 'ASC';

        $offset = ($page - 1) * $perPage;

        $sql = "
            SELECT 
                t.*, 
                u.name AS user_name,
                c.name AS client_name,
                l.title AS lead_title,
                CASE 
                    WHEN t.status != 'done' AND t.due_date < CURDATE() THEN 1 
                    ELSE 0 
                END AS is_overdue
            FROM {$this->getTable()} t
            JOIN users u ON t.user_id = u.id
            LEFT JOIN clients c ON t.client_id = c.id
            LEFT JOIN leads l ON t.lead_id = l.id
            {$whereSql}
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

    public function getUpcoming(int $limit = 5, ?int $userId = null): array
    {
        $db = self::db();
        $conditions = ["t.status != 'done'"];
        $params = [];

        if (!empty($userId)) {
            $conditions[] = "t.user_id = :user_id";
            $params['user_id'] = $userId;
        }

        $whereSql = implode(' AND ', $conditions);

        $sql = "
            SELECT 
                t.*, 
                u.name AS user_name,
                c.name AS client_name,
                l.title AS lead_title,
                CASE 
                    WHEN t.due_date < CURDATE() THEN 1 
                    ELSE 0 
                END AS is_overdue
            FROM {$this->getTable()} t
            JOIN users u ON t.user_id = u.id
            LEFT JOIN clients c ON t.client_id = c.id
            LEFT JOIN leads l ON t.lead_id = l.id
            WHERE {$whereSql}
            ORDER BY t.due_date ASC
            LIMIT :limit
        ";

        $stmt = $db->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function getForCalendar(int $year, int $month, ?int $userId = null): array
    {
        $db = self::db();
        $startDate = sprintf('%04d-%02d-01', $year, $month);
        $endDate = date('Y-m-t', strtotime($startDate));

        $conditions = [
            "t.due_date >= :start_date",
            "t.due_date <= :end_date"
        ];
        $params = [
            'start_date' => $startDate,
            'end_date' => $endDate,
        ];

        if (!empty($userId)) {
            $conditions[] = "t.user_id = :user_id";
            $params['user_id'] = $userId;
        }

        $whereSql = implode(' AND ', $conditions);

        $sql = "
            SELECT 
                t.id, t.title, t.due_date, t.priority, t.status,
                u.name AS user_name,
                c.name AS client_name,
                l.title AS lead_title
            FROM {$this->getTable()} t
            JOIN users u ON t.user_id = u.id
            LEFT JOIN clients c ON t.client_id = c.id
            LEFT JOIN leads l ON t.lead_id = l.id
            WHERE {$whereSql}
            ORDER BY t.due_date ASC, t.id ASC
        ";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
