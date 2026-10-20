<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class Activity extends Model
{
    protected string $table = 'activities';
    protected array $fillable = [
        'lead_id',
        'client_id',
        'user_id',
        'type',
        'note',
    ];

    public const TYPES = [
        'call' => 'Qoʻngʻiroq',
        'meeting' => 'Uchrashuv',
        'email' => 'Email',
        'message' => 'Xabar',
        'note' => 'Eslatma / Izoh',
    ];

    public function getRecent(int $limit = 10, ?int $userId = null): array
    {
        $db = self::db();
        $where = '';
        $params = [];

        if (!empty($userId)) {
            $where = 'WHERE a.user_id = :user_id';
            $params['user_id'] = $userId;
        }

        $sql = "
            SELECT 
                a.*, 
                u.name AS user_name,
                c.name AS client_name,
                l.title AS lead_title
            FROM {$this->getTable()} a
            JOIN users u ON a.user_id = u.id
            LEFT JOIN clients c ON a.client_id = c.id
            LEFT JOIN leads l ON a.lead_id = l.id
            {$where}
            ORDER BY a.created_at DESC
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

    public function forClient(int $clientId): array
    {
        $db = self::db();
        $stmt = $db->prepare("
            SELECT a.*, u.name AS user_name, l.title AS lead_title
            FROM {$this->getTable()} a
            JOIN users u ON a.user_id = u.id
            LEFT JOIN leads l ON a.lead_id = l.id
            WHERE a.client_id = :client_id
            ORDER BY a.created_at DESC
        ");
        $stmt->execute(['client_id' => $clientId]);
        return $stmt->fetchAll();
    }

    public function forLead(int $leadId): array
    {
        $db = self::db();
        $stmt = $db->prepare("
            SELECT a.*, u.name AS user_name, c.name AS client_name
            FROM {$this->getTable()} a
            JOIN users u ON a.user_id = u.id
            LEFT JOIN clients c ON a.client_id = c.id
            WHERE a.lead_id = :lead_id
            ORDER BY a.created_at DESC
        ");
        $stmt->execute(['lead_id' => $leadId]);
        return $stmt->fetchAll();
    }
}
