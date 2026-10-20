<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Models\Task;

class CalendarController extends Controller
{
    private Task $taskModel;

    public function __construct()
    {
        $this->taskModel = new Task();
    }

    public function index(Request $request): Response
    {
        $year = (int)$request->query('year', (int)date('Y'));
        $month = (int)$request->query('month', (int)date('n'));

        // Clamp year and month safely
        if ($month < 1) {
            $month = 12;
            $year--;
        } elseif ($month > 12) {
            $month = 1;
            $year++;
        }

        $userId = $this->isAdmin() ? null : $this->userId();

        // Fetch tasks for this month
        $tasks = $this->taskModel->getForCalendar($year, $month, $userId);

        // Group tasks by date
        $tasksByDate = [];
        foreach ($tasks as $t) {
            $dateKey = $t['due_date'];
            $tasksByDate[$dateKey][] = $t;
        }

        // Calendar metadata
        $firstDayOfMonth = mktime(0, 0, 0, $month, 1, $year);
        $daysInMonth = (int)date('t', $firstDayOfMonth);
        $firstDayOfWeek = (int)date('N', $firstDayOfMonth); // 1 (Mon) to 7 (Sun)

        $monthNamesUz = [
            1 => 'Yanvar', 2 => 'Fevral', 3 => 'Mart', 4 => 'Aprel',
            5 => 'May', 6 => 'Iyun', 7 => 'Iyul', 8 => 'Avgust',
            9 => 'Sentabr', 10 => 'Oktabr', 11 => 'Noyabr', 12 => 'Dekabr'
        ];

        return $this->render('calendar.index', [
            'pageTitle' => 'Kalendar',
            'year' => $year,
            'month' => $month,
            'monthName' => $monthNamesUz[$month] ?? date('F', $firstDayOfMonth),
            'daysInMonth' => $daysInMonth,
            'firstDayOfWeek' => $firstDayOfWeek,
            'tasksByDate' => $tasksByDate,
            'prevMonth' => $month == 1 ? 12 : $month - 1,
            'prevYear' => $month == 1 ? $year - 1 : $year,
            'nextMonth' => $month == 12 ? 1 : $month + 1,
            'nextYear' => $month == 12 ? $year + 1 : $year,
        ]);
    }
}
