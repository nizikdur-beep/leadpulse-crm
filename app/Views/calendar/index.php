<div class="page-container">
    <div class="page-header-actions">
        <div>
            <h2 class="page-heading">Rejalar va Vazifalar Kalendari</h2>
            <p class="page-subheading">Oylik reja va muhim uchrashuvlar</p>
        </div>
        <div class="calendar-navigation">
            <a href="?year=<?= $prevYear ?>&month=<?= $prevMonth ?>" class="btn btn-outline btn-sm">
                &laquo; Oldingi oy
            </a>
            <span class="calendar-current-month">
                <strong><?= $monthName ?> <?= $year ?></strong>
            </span>
            <a href="?year=<?= $nextYear ?>&month=<?= $nextMonth ?>" class="btn btn-outline btn-sm">
                Keyingi oy &raquo;
            </a>
            <a href="/calendar" class="btn btn-secondary btn-sm ml-2">Bugun</a>
        </div>
    </div>

    <!-- Calendar Grid Card -->
    <div class="card calendar-card">
        <div class="card-body p-0">
            <div class="calendar-grid-table">
                <!-- Day of week headers -->
                <div class="calendar-days-header">
                    <div class="day-header-cell">Dushanba</div>
                    <div class="day-header-cell">Seshanba</div>
                    <div class="day-header-cell">Chorshanba</div>
                    <div class="day-header-cell">Payshanba</div>
                    <div class="day-header-cell">Juma</div>
                    <div class="day-header-cell weekend">Shanba</div>
                    <div class="day-header-cell weekend">Yakshanba</div>
                </div>

                <div class="calendar-month-cells">
                    <?php
                    // Blank leading cells before day 1
                    for ($b = 1; $b < $firstDayOfWeek; $b++): ?>
                        <div class="calendar-day-cell empty-cell"></div>
                    <?php endfor; ?>

                    <?php
                    $todayStr = date('Y-m-d');
                    for ($d = 1; $d <= $daysInMonth; $d++):
                        $currentDateStr = sprintf('%04d-%02d-%02d', $year, $month, $d);
                        $isToday = ($currentDateStr === $todayStr);
                        $dayTasks = $tasksByDate[$currentDateStr] ?? [];
                    ?>
                        <div class="calendar-day-cell <?= $isToday ? 'is-today' : '' ?>">
                            <div class="day-number-row">
                                <span class="day-num"><?= $d ?></span>
                                <?php if ($isToday): ?>
                                    <span class="today-badge">Bugun</span>
                                <?php endif; ?>
                            </div>

                            <div class="day-tasks-container">
                                <?php foreach ($dayTasks as $t): ?>
                                    <div class="cal-task-pill priority-<?= htmlspecialchars($t['priority']) ?> status-<?= htmlspecialchars($t['status']) ?>" title="<?= htmlspecialchars($t['title']) ?> (<?= htmlspecialchars($t['user_name']) ?>)">
                                        <a href="/tasks/<?= $t['id'] ?>/edit" class="cal-task-title">
                                            <?= htmlspecialchars($t['title']) ?>
                                        </a>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endfor; ?>
                </div>
            </div>
        </div>
    </div>
</div>
