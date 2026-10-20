<?php

namespace App\Models;

use App\Core\Model;

class LeadStageHistory extends Model
{
    protected string $table = 'lead_stage_history';
    protected array $fillable = [
        'lead_id',
        'user_id',
        'old_stage',
        'new_stage',
    ];

    public function forLead(int $leadId): array
    {
        $db = self::db();
        $stmt = $db->prepare("
            SELECT h.*, u.name AS user_name
            FROM {$this->getTable()} h
            JOIN users u ON h.user_id = u.id
            WHERE h.lead_id = :lead_id
            ORDER BY h.created_at DESC
        ");
        $stmt->execute(['lead_id' => $leadId]);
        return $stmt->fetchAll();
    }
}
