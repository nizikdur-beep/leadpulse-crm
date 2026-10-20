<?php

namespace App\Services;

use App\Models\Lead;
use App\Models\LeadStageHistory;
use App\Models\Activity;
use App\Core\Database;

class LeadService
{
    private Lead $leadModel;
    private LeadStageHistory $historyModel;
    private Activity $activityModel;

    public function __construct()
    {
        $this->leadModel = new Lead();
        $this->historyModel = new LeadStageHistory();
        $this->activityModel = new Activity();
    }

    public function createLead(array $data, int $userId): int
    {
        $db = Database::connect();
        $db->beginTransaction();

        try {
            $data['stage'] = $data['stage'] ?? 'new';
            $leadId = $this->leadModel->create($data);

            // History
            $this->historyModel->create([
                'lead_id' => $leadId,
                'user_id' => $userId,
                'old_stage' => 'new',
                'new_stage' => $data['stage'],
            ]);

            // Auto activity record
            $this->activityModel->create([
                'lead_id' => $leadId,
                'client_id' => $data['client_id'] ?? null,
                'user_id' => $userId,
                'type' => 'note',
                'note' => 'Yangi bitim yaratildi: ' . $data['title'],
            ]);

            $db->commit();
            return $leadId;
        } catch (\Throwable $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public function updateStage(int $leadId, string $newStage, int $userId, ?string $lostReason = null): bool
    {
        $validStages = array_keys(Lead::STAGES);
        if (!in_array($newStage, $validStages, true)) {
            throw new \InvalidArgumentException('Notoʻgʻri bitim bosqichi tanlandi.');
        }

        $lead = $this->leadModel->find($leadId);
        if (!$lead) {
            throw new \RuntimeException('Bitim topilmadi.');
        }

        $oldStage = $lead['stage'];
        if ($oldStage === $newStage) {
            return true;
        }

        $db = Database::connect();
        $db->beginTransaction();

        try {
            $updateData = ['stage' => $newStage];
            if ($newStage === 'lost' && !empty($lostReason)) {
                $updateData['lost_reason'] = $lostReason;
            } elseif ($newStage !== 'lost') {
                $updateData['lost_reason'] = null;
            }

            $this->leadModel->update($leadId, $updateData);

            // History log
            $this->historyModel->create([
                'lead_id' => $leadId,
                'user_id' => $userId,
                'old_stage' => $oldStage,
                'new_stage' => $newStage,
            ]);

            // Activity log
            $oldStageName = Lead::STAGES[$oldStage] ?? $oldStage;
            $newStageName = Lead::STAGES[$newStage] ?? $newStage;
            $note = "Bitim bosqichi oʻzgartirildi: «{$oldStageName}» → «{$newStageName}»";
            if ($newStage === 'lost' && !empty($lostReason)) {
                $note .= " (Sababi: {$lostReason})";
            }

            $this->activityModel->create([
                'lead_id' => $leadId,
                'client_id' => $lead['client_id'],
                'user_id' => $userId,
                'type' => 'note',
                'note' => $note,
            ]);

            $db->commit();
            return true;
        } catch (\Throwable $e) {
            $db->rollBack();
            throw $e;
        }
    }
}
