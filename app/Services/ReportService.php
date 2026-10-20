<?php

namespace App\Services;

use App\Core\Database;
use App\Models\Lead;
use PDO;

class ReportService
{
    public function getKpiStats(?int $userId = null): array
    {
        $db = Database::connect();

        // 1. Total clients
        $clientWhere = "WHERE deleted_at IS NULL";
        $clientParams = [];
        if (!empty($userId)) {
            $clientWhere .= " AND assigned_to = :uid";
            $clientParams['uid'] = $userId;
        }
        $clientStmt = $db->prepare("SELECT COUNT(*) FROM clients {$clientWhere}");
        $clientStmt->execute($clientParams);
        $totalClients = (int)$clientStmt->fetchColumn();

        // 2. Active deals (new, contact, proposal)
        $leadWhere = "WHERE deleted_at IS NULL";
        $leadParams = [];
        if (!empty($userId)) {
            $leadWhere .= " AND user_id = :uid";
            $leadParams['uid'] = $userId;
        }

        $activeStmt = $db->prepare("SELECT COUNT(*) FROM leads {$leadWhere} AND stage IN ('new', 'contact', 'proposal')");
        $activeStmt->execute($leadParams);
        $activeDeals = (int)$activeStmt->fetchColumn();

        // 3. Total won amount
        $wonAmountStmt = $db->prepare("SELECT COALESCE(SUM(amount), 0) FROM leads {$leadWhere} AND stage = 'won'");
        $wonAmountStmt->execute($leadParams);
        $totalWonAmount = (float)$wonAmountStmt->fetchColumn();

        // 4. Won & Lost counts for conversion rate
        // Conversion formula: Won / (Won + Lost) * 100
        $wonCountStmt = $db->prepare("SELECT COUNT(*) FROM leads {$leadWhere} AND stage = 'won'");
        $wonCountStmt->execute($leadParams);
        $wonCount = (int)$wonCountStmt->fetchColumn();

        $lostCountStmt = $db->prepare("SELECT COUNT(*) FROM leads {$leadWhere} AND stage = 'lost'");
        $lostCountStmt->execute($leadParams);
        $lostCount = (int)$lostCountStmt->fetchColumn();

        $finishedCount = $wonCount + $lostCount;
        $conversionRate = $finishedCount > 0 ? round(($wonCount / $finishedCount) * 100, 1) : 0.0;

        return [
            'total_clients' => $totalClients,
            'active_deals' => $activeDeals,
            'total_won_amount' => $totalWonAmount,
            'conversion_rate' => $conversionRate,
            'won_count' => $wonCount,
            'lost_count' => $lostCount,
            'finished_count' => $finishedCount,
        ];
    }

    public function getStageDistribution(?int $userId = null): array
    {
        $db = Database::connect();
        $where = "WHERE deleted_at IS NULL";
        $params = [];
        if (!empty($userId)) {
            $where .= " AND user_id = :uid";
            $params['uid'] = $userId;
        }

        $sql = "
            SELECT stage, COUNT(*) AS count, COALESCE(SUM(amount), 0) AS total_amount
            FROM leads
            {$where}
            GROUP BY stage
        ";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll();

        $distribution = [];
        foreach (Lead::STAGES as $key => $label) {
            $distribution[$key] = [
                'stage' => $key,
                'label' => $label,
                'count' => 0,
                'total_amount' => 0.0,
            ];
        }

        foreach ($rows as $r) {
            $st = $r['stage'];
            if (isset($distribution[$st])) {
                $distribution[$st]['count'] = (int)$r['count'];
                $distribution[$st]['total_amount'] = (float)$r['total_amount'];
            }
        }

        return $distribution;
    }

    public function getMonthlySales(?int $userId = null, int $months = 6): array
    {
        $db = Database::connect();
        $where = "WHERE deleted_at IS NULL AND stage = 'won'";
        $params = [];
        if (!empty($userId)) {
            $where .= " AND user_id = :uid";
            $params['uid'] = $userId;
        }

        $isSqlite = Database::getDriver() === 'sqlite';
        $monthExpr = $isSqlite ? "strftime('%Y-%m', created_at)" : "DATE_FORMAT(created_at, '%Y-%m')";

        $sql = "
            SELECT 
                {$monthExpr} AS month_key,
                COUNT(*) AS count,
                COALESCE(SUM(amount), 0) AS total_amount
            FROM leads
            {$where}
            GROUP BY {$monthExpr}
            ORDER BY month_key DESC
            LIMIT {$months}
        ";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $rows = array_reverse($stmt->fetchAll());

        // Fill current 6 months if missing
        $result = [];
        for ($i = $months - 1; $i >= 0; $i--) {
            $mKey = date('Y-m', strtotime("-{$i} month"));
            $mLabel = date('M Y', strtotime("-{$i} month"));
            $found = null;
            foreach ($rows as $r) {
                if ($r['month_key'] === $mKey) {
                    $found = $r;
                    break;
                }
            }
            $result[] = [
                'month' => $mKey,
                'label' => $mLabel,
                'count' => $found ? (int)$found['count'] : 0,
                'total_amount' => $found ? (float)$found['total_amount'] : 0.0,
            ];
        }

        return $result;
    }

    public function getManagerPerformance(): array
    {
        $db = Database::connect();
        $sql = "
            SELECT 
                u.id, 
                u.name, 
                u.email,
                COUNT(l.id) AS total_leads,
                SUM(CASE WHEN l.stage = 'won' THEN 1 ELSE 0 END) AS won_leads,
                SUM(CASE WHEN l.stage = 'lost' THEN 1 ELSE 0 END) AS lost_leads,
                SUM(CASE WHEN l.stage IN ('new', 'contact', 'proposal') THEN 1 ELSE 0 END) AS active_leads,
                COALESCE(SUM(CASE WHEN l.stage = 'won' THEN l.amount ELSE 0 END), 0) AS total_won_amount
            FROM users u
            LEFT JOIN leads l ON u.id = l.user_id AND l.deleted_at IS NULL
            WHERE u.is_active = 1
            GROUP BY u.id, u.name, u.email
            ORDER BY total_won_amount DESC
        ";
        $stmt = $db->query($sql);
        $rows = $stmt->fetchAll();

        foreach ($rows as &$row) {
            $finished = (int)$row['won_leads'] + (int)$row['lost_leads'];
            $row['conversion_rate'] = $finished > 0 ? round(((int)$row['won_leads'] / $finished) * 100, 1) : 0.0;
        }
        unset($row);

        return $rows;
    }

    /**
     * CSV Export with CSV Formula Injection protection.
     */
    public function exportLeadsCsv(?int $userId = null): string
    {
        $db = Database::connect();
        $where = "WHERE l.deleted_at IS NULL";
        $params = [];
        if (!empty($userId)) {
            $where .= " AND l.user_id = :uid";
            $params['uid'] = $userId;
        }

        $sql = "
            SELECT 
                l.id,
                l.title,
                c.name AS client_name,
                c.phone AS client_phone,
                c.email AS client_email,
                u.name AS manager_name,
                l.amount,
                l.stage,
                l.source,
                l.created_at
            FROM leads l
            JOIN clients c ON l.client_id = c.id
            JOIN users u ON l.user_id = u.id
            {$where}
            ORDER BY l.id DESC
        ";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll();

        $output = fopen('php://temp', 'r+');

        // UTF-8 BOM for Excel compatibility
        fputs($output, "\xEF\xBB\xBF");

        // Headers in Uzbek
        fputcsv($output, [
            'ID',
            'Bitim sarlavhasi',
            'Mijoz',
            'Telefon',
            'Email',
            'Masʼul xodim',
            'Summa (soʻm)',
            'Bosqich',
            'Manba',
            'Yaratilgan sana'
        ]);

        foreach ($rows as $r) {
            $stageText = Lead::STAGES[$r['stage']] ?? $r['stage'];
            $cleanRow = [
                $r['id'],
                $this->sanitizeCsvCell($r['title']),
                $this->sanitizeCsvCell($r['client_name']),
                $this->sanitizeCsvCell($r['client_phone']),
                $this->sanitizeCsvCell($r['client_email']),
                $this->sanitizeCsvCell($r['manager_name']),
                number_format((float)$r['amount'], 2, '.', ''),
                $this->sanitizeCsvCell($stageText),
                $this->sanitizeCsvCell($r['source'] ?? ''),
                $r['created_at'],
            ];
            fputcsv($output, $cleanRow);
        }

        rewind($output);
        $csvContent = stream_get_contents($output);
        fclose($output);

        return $csvContent;
    }

    /**
     * Prevents CSV Formula Injection (=, +, -, @, \t, \r at the start of cell)
     */
    private function sanitizeCsvCell(?string $value): string
    {
        if ($value === null || $value === '') {
            return '';
        }

        $firstChar = substr($value, 0, 1);
        if (in_array($firstChar, ['=', '+', '-', '@', "\t", "\r"], true)) {
            return "'" . $value;
        }

        return $value;
    }
}
