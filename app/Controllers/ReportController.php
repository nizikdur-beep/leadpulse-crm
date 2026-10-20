<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Services\ReportService;

class ReportController extends Controller
{
    private ReportService $reportService;

    public function __construct()
    {
        $this->reportService = new ReportService();
    }

    public function index(Request $request): Response
    {
        $userId = $this->isAdmin() ? null : $this->userId();

        $kpi = $this->reportService->getKpiStats($userId);
        $stageDistribution = $this->reportService->getStageDistribution($userId);
        $monthlySales = $this->reportService->getMonthlySales($userId, 12);
        $managerPerformance = $this->isAdmin() ? $this->reportService->getManagerPerformance() : [];

        return $this->render('reports.index', [
            'pageTitle' => 'Savdo va samaradorlik hisobotlari',
            'kpi' => $kpi,
            'stageDistribution' => $stageDistribution,
            'monthlySales' => $monthlySales,
            'managerPerformance' => $managerPerformance,
        ]);
    }

    public function export(Request $request): Response
    {
        $userId = $this->isAdmin() ? null : $this->userId();
        $csvData = $this->reportService->exportLeadsCsv($userId);

        $filename = 'leadpulse_leads_' . date('Y-m-d_His') . '.csv';

        return Response::make($csvData, 200, [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }
}
