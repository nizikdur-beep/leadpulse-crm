<div class="page-container">
    <div class="page-header-actions">
        <div>
            <h2 class="page-heading">Savdo va Samaradorlik Hisobotlari</h2>
            <p class="page-subheading">CRM koʻrsatkichlari, konversiya va menejerlar statistikasi</p>
        </div>
        <a href="/reports/export" class="btn btn-primary" title="CSV formatida yuklab olish">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                <polyline points="7 10 12 15 17 10"></polyline>
                <line x1="12" y1="15" x2="12" y2="3"></line>
            </svg>
            <span>Bitimlarni CSV eksport qilish</span>
        </a>
    </div>

    <!-- Summary KPI Stats -->
    <div class="kpi-grid mb-4">
        <div class="kpi-card">
            <div class="kpi-content">
                <span class="kpi-label">Yutuq (Won) bitimlar</span>
                <div class="kpi-value text-success"><?= $kpi['won_count'] ?? 0 ?> ta</div>
                <span class="kpi-subtext">Muvaffaqiyatli yakun</span>
            </div>
            <div class="kpi-icon icon-green">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
            </div>
        </div>

        <div class="kpi-card">
            <div class="kpi-content">
                <span class="kpi-label">Yoʻqotilgan (Lost) bitimlar</span>
                <div class="kpi-value text-danger"><?= $kpi['lost_count'] ?? 0 ?> ta</div>
                <span class="kpi-subtext">Rad etilgan yoki bekor boʻlgan</span>
            </div>
            <div class="kpi-icon icon-red">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </div>
        </div>

        <div class="kpi-card">
            <div class="kpi-content">
                <span class="kpi-label">Umumiy tushum</span>
                <div class="kpi-value"><?= number_format($kpi['total_won_amount'] ?? 0, 0, '.', ' ') ?> <small>soʻm</small></div>
                <span class="kpi-subtext">Won bitimlar yigʻindisi</span>
            </div>
            <div class="kpi-icon icon-blue">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="1" x2="12" y2="23"></line>
                    <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                </svg>
            </div>
        </div>

        <div class="kpi-card">
            <div class="kpi-content">
                <span class="kpi-label">Umumiy konversiya</span>
                <div class="kpi-value"><?= number_format($kpi['conversion_rate'] ?? 0, 1) ?>%</div>
                <span class="kpi-subtext">Won / (Won + Lost) * 100</span>
            </div>
            <div class="kpi-icon icon-amber">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline>
                </svg>
            </div>
        </div>
    </div>

    <!-- Monthly Trend & Distribution -->
    <div class="dashboard-grid-two mb-4">
        <!-- Monthly Sales -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Oylik tushum grafigi (12 oy)</h3>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <?php 
                    $maxYearAmount = 1;
                    foreach ($monthlySales as $m) {
                        if ($m['total_amount'] > $maxYearAmount) {
                            $maxYearAmount = $m['total_amount'];
                        }
                    }
                    ?>
                    <div class="custom-bar-chart">
                        <?php foreach ($monthlySales as $m): 
                            $hPercent = $maxYearAmount > 0 ? round(($m['total_amount'] / $maxYearAmount) * 100) : 0;
                            $hPercent = max(5, $hPercent);
                        ?>
                            <div class="bar-col">
                                <div class="bar-value-label"><?= $m['total_amount'] > 0 ? number_format($m['total_amount'] / 1000000, 1) . 'M' : '0' ?></div>
                                <div class="bar-track">
                                    <div class="bar-fill" style="height: <?= $hPercent ?>%;" title="<?= $m['label'] ?>: <?= number_format($m['total_amount'], 0, '.', ' ') ?> soʻm"></div>
                                </div>
                                <div class="bar-label"><?= htmlspecialchars($m['label']) ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stage Distribution Table -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Bosqichlar boʻyicha taqsimot</h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Bosqich</th>
                                <th>Bitimlar soni</th>
                                <th>Jami summa</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($stageDistribution as $k => $st): ?>
                                <tr>
                                    <td>
                                        <span class="status-dot dot-<?= $k ?>"></span>
                                        <strong><?= htmlspecialchars($st['label']) ?></strong>
                                    </td>
                                    <td><?= $st['count'] ?> ta</td>
                                    <td><strong><?= number_format($st['total_amount'], 0, '.', ' ') ?> soʻm</strong></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Manager Performance Table (Admin only or manager section) -->
    <?php if (!empty($managerPerformance)): ?>
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Menejerlar kesimida savdo samaradorligi</h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table custom-table">
                        <thead>
                            <tr>
                                <th>Menejer</th>
                                <th>Email</th>
                                <th>Jami bitimlar</th>
                                <th>Faol bitimlar</th>
                                <th>Yutuq (Won)</th>
                                <th>Yoʻqotilgan (Lost)</th>
                                <th>Konversiya</th>
                                <th>Yakuniy tushum</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($managerPerformance as $mp): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($mp['name']) ?></strong></td>
                                    <td><?= htmlspecialchars($mp['email']) ?></td>
                                    <td><?= (int)$mp['total_leads'] ?> ta</td>
                                    <td><?= (int)$mp['active_leads'] ?> ta</td>
                                    <td><span class="badge badge-success"><?= (int)$mp['won_leads'] ?> ta</span></td>
                                    <td><span class="badge badge-danger"><?= (int)$mp['lost_leads'] ?> ta</span></td>
                                    <td><strong><?= number_format((float)$mp['conversion_rate'], 1) ?>%</strong></td>
                                    <td><strong class="text-success"><?= number_format((float)$mp['total_won_amount'], 0, '.', ' ') ?> soʻm</strong></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
