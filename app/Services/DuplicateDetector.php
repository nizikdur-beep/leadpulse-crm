<?php

namespace App\Services;

use App\Models\Client;

class DuplicateDetector
{
    private Client $clientModel;

    public function __construct()
    {
        $this->clientModel = new Client();
    }

    public function check(?string $phone, ?string $email, ?int $excludeId = null): array
    {
        $duplicates = $this->clientModel->findDuplicates($phone, $email, $excludeId);

        if (empty($duplicates)) {
            return [
                'has_duplicate' => false,
                'duplicates' => []
            ];
        }

        return [
            'has_duplicate' => true,
            'duplicates' => $duplicates
        ];
    }
}
