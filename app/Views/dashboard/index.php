<div class="dashboard-container">
    <!-- Top KPI Cards Grid -->
    <div class="kpi-grid">
        <!-- 1. Jami mijozlar -->
        <div class="kpi-card">
            <div class="kpi-content">
                <span class="kpi-label">Jami mijozlar</span>
                <div class="kpi-value"><?= number_format($kpi['total_clients'] ?? 0) ?></div>
                <span class="kpi-subtext">Faol mijozlar bazasi</span>
            </div>
            <div class="kpi-icon icon-blue">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
            </div>
        </div>

        <!-- 2. Faol bitimlar -->
        <div class="kpi-card">
            <div class="kpi-content">
                <span class="kpi-label">Faol bitimlar</span>
                <div class="kpi-value"><?= number_format($kpi['active_deals'] ?? 0) ?></div>
                <span class="kpi-subtext">Voronkadagi jarayonlar</span>
            </div>
            <div class="kpi-icon icon-purple">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                </svg>
            </div>
        </div>

        <!-- 3. Umumiy savdo summasi -->
        <div class="kpi-card">
            <div class="kpi-content">
                <span class="kpi-label">Umumiy savdo summasi</span>
                <div class="kpi-value"><?= number_format($kpi['total_won_amount'] ?? 0, 0, '.', ' ') ?> <small>soʻm</small></div>
                <span class="kpi-subtext text-success">Muvaffaqiyatli yakunlangan</span>
            </div>
            <div class="kpi-icon icon-green">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="1" x2="12" y2="23"></line>
                    <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                </svg>
            </div>
        </div>

        <!-- 4. Konversiya foizi -->
        <div class="kpi-card">
            <div class="kpi-content">
                <span class="kpi-label">Konversiya foizi</span>
                <div class="kpi-value"><?= number_format($kpi['conversion_rate'] ?? 0, 1) ?>%</div>
                <span class="kpi-subtext"><?= ($kpi['won_count'] ?? 0) ?> yutuq / <?= ($kpi['finished_count'] ?? 0) ?> yakuniy</span>
            </div>
            <div class="kpi-icon icon-amber">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline>
                    <polyline points="17 6 23 6 23 12"></polyline>
                </svg>
            </div>
        </div>
    </div>

    <!-- Charts & Stages Section -->
    <div class="dashboard-grid-two">
        <!-- Monthly Sales Bar Chart -->
        <div class="card">
            <div class="card-header">
                <div>
                    <h2 class="card-title">Oylar kesimida savdo dinamikasi</h2>
                    <p class="card-subtitle">Muvaffaqiyatli yakunlangan bitimlar hajmi (soʻnggi 6 oy)</p>
                </div>
                <a href="/reports" class="btn btn-sm btn-outline">Batafsil</a>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <?php 
                    $maxAmount = 1;
                    foreach ($monthlySales as $m) {
                        if ($m['total_amount'] > $maxAmount) {
                            $maxAmount = $m['total_amount'];
                        }
                    }
                    ?>
                    <div class="custom-bar-chart">
                        <?php foreach ($monthlySales as $m): 
                            $heightPercent = $maxAmount > 0 ? round(($m['total_amount'] / $maxAmount) * 100) : 0;
                            $heightPercent = max(6, $heightPercent); // minimum visible height
                        ?>
                            <div class="bar-col">
                                <div class="bar-value-label"><?= $m['total_amount'] > 0 ? number_format($m['total_amount'] / 1000000, 1) . 'M' : '0' ?></div>
                                <div class="bar-track">
                                    <div class="bar-fill" style="height: <?= $heightPercent ?>%;" title="<?= $m['label'] ?>: <?= number_format($m['total_amount'], 0, '.', ' ') ?> soʻm (<?= $m['count'] ?> ta bitim)"></div>
                                </div>
                                <div class="bar-label"><?= htmlspecialchars($m['label']) ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lead Pipeline Stages Overview -->
        <div class="card">
            <div class="card-header">
                <div>
                    <h2 class="card-title">Savdo voronkasi (Bosqichlar)</h2>
                    <p class="card-subtitle">Joriy bitimlarning taqsimoti</p>
                </div>
                <a href="/leads" class="btn btn-sm btn-outline">Voronkani ochish</a>
            </div>
            <div class="card-body">
                <div class="pipeline-progress-list">
                    <?php 
                    $stageColors = [
                        'new' => 'var(--color-primary)',
                        'contact' => 'var(--color-warning)',
                        'proposal' => 'var(--color-purple)',
                        'won' => 'var(--color-success)',
                        'lost' => 'var(--color-danger)',
                    ];
                    foreach ($stageDistribution as $key => $st): 
                    ?>
                        <div class="pipeline-row">
                            <div class="pipeline-row-info">
                                <div class="pipeline-tag">
                                    <span class="status-dot dot-<?= $key ?>"></span>
                                    <span class="pipeline-name"><?= htmlspecialchars($st['label']) ?></span>
                                </div>
                                <div class="pipeline-meta">
                                    <strong class="pipeline-count"><?= $st['count'] ?> ta bitim</strong>
                                    <span class="pipeline-sum"><?= number_format($st['total_amount'], 0, '.', ' ') ?> soʻm</span>
                                </div>
                            </div>
                            <div class="progress-bar-wrap">
                                <div class="progress-bar-inner bg-stage-<?= $key ?>" style="width: <?= min(100, max(5, $st['count'] * 15)) ?>%;"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Tasks and Recent Activities Grid -->
    <div class="dashboard-grid-two">
        <!-- Upcoming Tasks -->
        <div class="card">
            <div class="card-header">
                <div>
                    <h2 class="card-title">Yaqinlashayotgan vazifalar</h2>
                    <p class="card-subtitle">Rejalashtirilgan eslatmalar va muddatlar</p>
                </div>
                <a href="/tasks/create" class="btn btn-sm btn-primary">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    Qoʻshish
                </a>
            </div>
            <div class="card-body p-0">
                <?php if (empty($upcomingTasks)): ?>
                    <div class="empty-state-box">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 14 14"></polyline>
                        </svg>
                        <p>Hozircha faol vazifalar yoʻq</p>
                        <a href="/tasks/create" class="btn btn-sm btn-outline">Vazifa yaratish</a>
                    </div>
                <?php else: ?>
                    <ul class="task-list-group">
                        <?php foreach ($upcomingTasks as $task): ?>
                            <li class="task-list-item <?= !empty($task['is_overdue']) ? 'overdue' : '' ?>">
                                <div class="task-left">
                                    <form action="/tasks/<?= (int)$task['id'] ?>/complete" method="POST" style="margin: 0;">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="task-check-circle" title="Bajarildi deb belgilash">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                                <polyline points="20 6 9 17 4 12"></polyline>
                                            </svg>
                                        </button>
                                    </form>
                                    <div class="task-info">
                                        <div class="task-title-line">
                                            <span class="task-title-text"><?= htmlspecialchars($task['title']) ?></span>
                                            <?php if (!empty($task['is_overdue'])): ?>
                                                <span class="badge badge-danger">Kechikkan</span>
                                            <?php endif; ?>
                                            <span class="badge badge-priority-<?= htmlspecialchars($task['priority']) ?>">
                                                <?= \App\Models\Task::PRIORITIES[$task['priority']] ?? $task['priority'] ?>
                                            </span>
                                        </div>
                                        <div class="task-sub-info">
                                            <?php if (!empty($task['client_name'])): ?>
                                                <span>Mijoz: <?= htmlspecialchars($task['client_name']) ?></span> &bull;
                                            <?php endif; ?>
                                            <span>Muddat: <?= date('d.m.Y', strtotime($task['due_date'])) ?></span>
                                        </div>
                                    </div>
                                </div>
                                <a href="/tasks/<?= (int)$task['id'] ?>/edit" class="btn-icon-link" title="Tahrirlash">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                    </svg>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="card">
            <div class="card-header">
                <div>
                    <h2 class="card-title">Soʻnggi murojaatlar va faoliyatlar</h2>
                    <p class="card-subtitle">Xodimlar tomonidan amalga oshirilgan oxirgi harakatlar</p>
                </div>
                <a href="/activities" class="btn btn-sm btn-outline">Barchasi</a>
            </div>
            <div class="card-body p-0">
                <?php if (empty($recentActivities)): ?>
                    <div class="empty-state-box">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                        </svg>
                        <p>Hozircha hech qanday faoliyat qayd etilmagan</p>
                    </div>
                <?php else: ?>
                    <div class="timeline-list">
                        <?php foreach ($recentActivities as $act): ?>
                            <div class="timeline-item">
                                <div class="timeline-dot dot-type-<?= htmlspecialchars($act['type']) ?>"></div>
                                <div class="timeline-content">
                                    <div class="timeline-header">
                                        <span class="timeline-user"><?= htmlspecialchars($act['user_name']) ?></span>
                                        <span class="badge badge-sm badge-type-<?= htmlspecialchars($act['type']) ?>">
                                            <?= \App\Models\Activity::TYPES[$act['type']] ?? $act['type'] ?>
                                        </span>
                                        <span class="timeline-time"><?= date('d.m.Y H:i', strtotime($act['created_at'])) ?></span>
                                    </div>
                                    <div class="timeline-text"><?= htmlspecialchars($act['note']) ?></div>
                                    <?php if (!empty($act['client_name']) || !empty($act['lead_title'])): ?>
                                        <div class="timeline-meta">
                                            <?php if (!empty($act['client_name'])): ?>
                                                <span class="meta-tag">Mijoz: <?= htmlspecialchars($act['client_name']) ?></span>
                                            <?php endif; ?>
                                            <?php if (!empty($act['lead_title'])): ?>
                                                <span class="meta-tag">Bitim: <?= htmlspecialchars($act['lead_title']) ?></span>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
