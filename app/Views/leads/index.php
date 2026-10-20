<div class="page-container">
    <div class="page-header-actions">
        <div>
            <h2 class="page-heading">Bitimlar va Savdo Voronkasi</h2>
            <p class="page-subheading">Barcha savdo jarayonlari va potentsial shartnomalar</p>
        </div>
        <div class="header-action-buttons">
            <div class="view-switcher-buttons">
                <a href="/leads?view=pipeline" class="btn <?= ($viewType === 'pipeline') ? 'btn-secondary' : 'btn-outline' ?>" title="Voronka (Kanban) koʻrinishi">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="3" width="7" height="18"></rect>
                        <rect x="14" y="3" width="7" height="11"></rect>
                    </svg>
                    <span>Voronka</span>
                </a>
                <a href="/leads?view=table" class="btn <?= ($viewType === 'table') ? 'btn-secondary' : 'btn-outline' ?>" title="Jadval koʻrinishi">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="8" y1="6" x2="21" y2="6"></line>
                        <line x1="8" y1="12" x2="21" y2="12"></line>
                        <line x1="8" y1="18" x2="21" y2="18"></line>
                        <line x1="3" y1="6" x2="3.01" y2="6"></line>
                        <line x1="3" y1="12" x2="3.01" y2="12"></line>
                        <line x1="3" y1="18" x2="3.01" y2="18"></line>
                    </svg>
                    <span>Jadval</span>
                </a>
            </div>
            <a href="/leads/create" class="btn btn-primary">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                <span>Yangi bitim</span>
            </a>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="card toolbar-card">
        <form action="/leads" method="GET" class="filter-form">
            <input type="hidden" name="view" value="<?= htmlspecialchars($viewType) ?>">
            <div class="filter-grid">
                <?php if ($viewType === 'table'): ?>
                    <div class="filter-col search-col">
                        <input 
                            type="text" 
                            name="search" 
                            class="form-control" 
                            placeholder="Sarlavha yoki mijoz boʻyicha qidirish..." 
                            value="<?= htmlspecialchars($search ?? '') ?>"
                        >
                    </div>

                    <div class="filter-col">
                        <select name="stage" class="form-control">
                            <option value="">Barcha bosqichlar</option>
                            <?php foreach ($stages as $k => $label): ?>
                                <option value="<?= $k ?>" <?= ($stage === $k) ? 'selected' : '' ?>><?= $label ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endif; ?>

                <?php if (\App\Core\Auth::isAdmin() && !empty($managers)): ?>
                    <div class="filter-col">
                        <select name="user_id" class="form-control">
                            <option value="">Barcha masʼul xodimlar</option>
                            <?php foreach ($managers as $mgr): ?>
                                <option value="<?= $mgr['id'] ?>" <?= ($selectedUser == $mgr['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($mgr['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endif; ?>

                <div class="filter-actions">
                    <button type="submit" class="btn btn-secondary">Qoʻllash</button>
                    <?php if (!empty($search) || !empty($stage) || !empty($selectedUser)): ?>
                        <a href="/leads?view=<?= $viewType ?>" class="btn btn-outline">Tozalash</a>
                    <?php endif; ?>
                </div>
            </div>
        </form>
    </div>

    <?php if ($viewType === 'pipeline'): ?>
        <!-- Pipeline Board View -->
        <div class="pipeline-board">
            <?php foreach ($stages as $stageKey => $stageTitle): 
                $columnLeads = $pipeline[$stageKey] ?? [];
                $columnSum = array_reduce($columnLeads, fn($carry, $item) => $carry + (float)$item['amount'], 0);
            ?>
                <div class="pipeline-column column-stage-<?= $stageKey ?>">
                    <div class="pipeline-column-header">
                        <div class="column-title-group">
                            <span class="status-dot dot-<?= $stageKey ?>"></span>
                            <span class="column-name"><?= $stageTitle ?></span>
                            <span class="column-counter"><?= count($columnLeads) ?></span>
                        </div>
                        <div class="column-total-amount"><?= number_format($columnSum, 0, '.', ' ') ?> soʻm</div>
                    </div>

                    <div class="pipeline-cards-list">
                        <?php if (empty($columnLeads)): ?>
                            <div class="pipeline-empty-column">Bitimlar yoʻq</div>
                        <?php else: ?>
                            <?php foreach ($columnLeads as $lead): ?>
                                <div class="pipeline-lead-card">
                                    <div class="lead-card-header">
                                        <a href="/leads/<?= $lead['id'] ?>" class="lead-card-title">
                                            <?= htmlspecialchars($lead['title']) ?>
                                        </a>
                                    </div>
                                    <div class="lead-card-company">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                            <circle cx="9" cy="7" r="4"></circle>
                                        </svg>
                                        <?= htmlspecialchars($lead['client_name']) ?>
                                    </div>
                                    <div class="lead-card-footer">
                                        <div class="lead-card-amount">
                                            <?= number_format((float)$lead['amount'], 0, '.', ' ') ?> soʻm
                                        </div>
                                        <div class="lead-card-meta">
                                            <span class="lead-manager-avatar" title="<?= htmlspecialchars($lead['manager_name']) ?>">
                                                <?= strtoupper(substr($lead['manager_name'] ?? 'U', 0, 1)) ?>
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Quick Stage Change Select -->
                                    <div class="quick-stage-select-box">
                                        <form action="/leads/<?= $lead['id'] ?>/stage" method="POST" class="inline-form">
                                            <?= csrf_field() ?>
                                            <select name="stage" class="form-control-sm quick-stage-dropdown" onchange="this.form.submit()">
                                                <?php foreach ($stages as $sK => $sV): ?>
                                                    <option value="<?= $sK ?>" <?= ($lead['stage'] === $sK) ? 'selected' : '' ?>>
                                                        &rarr; <?= $sV ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </form>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    <?php else: ?>
        <!-- Table View -->
        <div class="card">
            <div class="card-body p-0">
                <?php if (empty($leads['data'])): ?>
                    <div class="empty-state-box">
                        <p>Bitimlar topilmadi.</p>
                        <a href="/leads/create" class="btn btn-primary btn-sm">Yangi bitim yaratish</a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table custom-table">
                            <thead>
                                <tr>
                                    <th>Bitim nomi</th>
                                    <th>Mijoz</th>
                                    <th>Summa</th>
                                    <th>Bosqich</th>
                                    <th>Masʼul</th>
                                    <th>Keyingi aloqa</th>
                                    <th class="text-right">Amallar</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($leads['data'] as $l): ?>
                                    <tr>
                                        <td>
                                            <a href="/leads/<?= $l['id'] ?>" class="item-title-link">
                                                <?= htmlspecialchars($l['title']) ?>
                                            </a>
                                        </td>
                                        <td>
                                            <a href="/clients/<?= $l['client_id'] ?>"><?= htmlspecialchars($l['client_name']) ?></a>
                                        </td>
                                        <td><strong><?= number_format((float)$l['amount'], 0, '.', ' ') ?> soʻm</strong></td>
                                        <td>
                                            <span class="badge badge-stage-<?= htmlspecialchars($l['stage']) ?>">
                                                <?= $stages[$l['stage']] ?? $l['stage'] ?>
                                            </span>
                                        </td>
                                        <td><?= htmlspecialchars($l['manager_name']) ?></td>
                                        <td><?= $l['next_contact_at'] ? date('d.m.Y', strtotime($l['next_contact_at'])) : '-' ?></td>
                                        <td class="text-right">
                                            <div class="action-btn-group">
                                                <a href="/leads/<?= $l['id'] ?>" class="btn btn-icon" title="Batafsil">
                                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                        <circle cx="12" cy="12" r="3"></circle>
                                                    </svg>
                                                </a>
                                                <a href="/leads/<?= $l['id'] ?>/edit" class="btn btn-icon" title="Tahrirlash">
                                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                                    </svg>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <?php if ($leads['last_page'] > 1): ?>
                        <div class="pagination-wrapper">
                            <div class="pagination-info">
                                Koʻrsatilmoqda: <?= count($leads['data']) ?> / Jami: <?= $leads['total'] ?> ta
                            </div>
                            <ul class="pagination">
                                <?php if ($leads['has_prev']): ?>
                                    <li class="page-item">
                                        <a href="?view=table&page=<?= $leads['current_page'] - 1 ?>&search=<?= urlencode($search ?? '') ?>&stage=<?= urlencode($stage ?? '') ?>" class="page-link">&laquo; Oldingi</a>
                                    </li>
                                <?php endif; ?>

                                <?php for ($i = 1; $i <= $leads['last_page']; $i++): ?>
                                    <li class="page-item <?= ($i === $leads['current_page']) ? 'active' : '' ?>">
                                        <a href="?view=table&page=<?= $i ?>&search=<?= urlencode($search ?? '') ?>&stage=<?= urlencode($stage ?? '') ?>" class="page-link"><?= $i ?></a>
                                    </li>
                                <?php endfor; ?>

                                <?php if ($leads['has_next']): ?>
                                    <li class="page-item">
                                        <a href="?view=table&page=<?= $leads['current_page'] + 1 ?>&search=<?= urlencode($search ?? '') ?>&stage=<?= urlencode($stage ?? '') ?>" class="page-link">Keyingi &raquo;</a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>
