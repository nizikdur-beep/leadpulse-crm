<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Models\Activity;
use App\Models\Lead;
use App\Models\Task;
use App\Services\ReportService;

class DashboardController extends Controller
{
    private ReportService $reportService;
    private Activity $activityModel;
    private Task $taskModel;
    private Lead $leadModel;

    public function __construct()
    {
        $this->reportService = new ReportService();
        $this->activityModel = new Activity();
        $this->taskModel = new Task();
        $this->leadModel = new Lead();
    }

    public function index(Request $request): Response
    {
        $userId = $this->isAdmin() ? null : $this->userId();

        // 1. KPI Cards
        $kpi = $this->reportService->getKpiStats($userId);

        // 2. Stage distribution for pipeline overview
        $stageDistribution = $this->reportService->getStageDistribution($userId);

        // 3. Recent activities
        $recentActivities = $this->activityModel->getRecent(6, $userId);

        // 4. Upcoming / today tasks
        $upcomingTasks = $this->taskModel->getUpcoming(6, $userId);

        // 5. Monthly sales trend (for chart)
        $monthlySales = $this->reportService->getMonthlySales($userId, 6);

        // 6. Pipeline leads grouped for quick glance
        $pipeline = $this->leadModel->getPipelineGrouped($userId);

        return $this->render('dashboard.index', [
            'pageTitle' => 'Bosh sahifa (Dashboard)',
            'kpi' => $kpi,
            'stageDistribution' => $stageDistribution,
            'recentActivities' => $recentActivities,
            'upcomingTasks' => $upcomingTasks,
            'monthlySales' => $monthlySales,
            'pipeline' => $pipeline,
        ]);
    }
}
