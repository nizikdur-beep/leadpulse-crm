<div class="page-container">
    <div class="page-header-actions">
        <div>
            <h2 class="page-heading">Vazifalar va Eslatmalar</h2>
            <p class="page-subheading">Jami <?= number_format($tasks['total']) ?> ta vazifa mavjud</p>
        </div>
        <a href="/tasks/create" class="btn btn-primary">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            <span>Yangi vazifa yaratish</span>
        </a>
    </div>

    <!-- Filters -->
    <div class="card toolbar-card">
        <form action="/tasks" method="GET" class="filter-form">
            <div class="filter-grid">
                <div class="filter-col search-col">
                    <input 
                        type="text" 
                        name="search" 
                        class="form-control" 
                        placeholder="Vazifa nomi boʻyicha qidirish..." 
                        value="<?= htmlspecialchars($search ?? '') ?>"
                    >
                </div>

                <div class="filter-col">
                    <select name="status" class="form-control">
                        <option value="">Barcha holatlar</option>
                        <?php foreach ($statuses as $k => $label): ?>
                            <option value="<?= $k ?>" <?= ($status === $k) ? 'selected' : '' ?>><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="filter-col">
                    <select name="priority" class="form-control">
                        <option value="">Barcha muhimlik darajalari</option>
                        <?php foreach ($priorities as $pk => $pLabel): ?>
                            <option value="<?= $pk ?>" <?= ($priority === $pk) ? 'selected' : '' ?>><?= $pLabel ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

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
                    <button type="submit" class="btn btn-secondary">Filtrlash</button>
                    <?php if (!empty($search) || !empty($status) || !empty($priority) || !empty($selectedUser)): ?>
                        <a href="/tasks" class="btn btn-outline">Tozalash</a>
                    <?php endif; ?>
                </div>
            </div>
        </form>
    </div>

    <!-- Tasks List Card -->
    <div class="card">
        <div class="card-body p-0">
            <?php if (empty($tasks['data'])): ?>
                <div class="empty-state-box">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M9 11l3 3L22 4"></path>
                        <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                    </svg>
                    <h3>Vazifalar topilmadi</h3>
                    <p>Kiritilgan filtrlarga mos vazifalar mavjud emas.</p>
                    <a href="/tasks/create" class="btn btn-primary btn-sm">Yangi vazifa qoʻshish</a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table custom-table">
                        <thead>
                            <tr>
                                <th style="width: 48px;"></th>
                                <th>Vazifa sarlavhasi</th>
                                <th>Mijoz / Bitim</th>
                                <th>Muddati</th>
                                <th>Muhimligi</th>
                                <th>Holati</th>
                                <th>Masʼul</th>
                                <th class="text-right">Amallar</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tasks['data'] as $t): ?>
                                <tr class="<?= ($t['status'] === 'done') ? 'row-done' : (!empty($t['is_overdue']) ? 'row-overdue' : '') ?>">
                                    <td>
                                        <?php if ($t['status'] !== 'done'): ?>
                                            <form action="/tasks/<?= $t['id'] ?>/complete" method="POST" style="margin: 0;">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="task-check-circle" title="Bajarildi deb belgilash">
                                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                                        <polyline points="20 6 9 17 4 12"></polyline>
                                                    </svg>
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <span class="task-checked-mark">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--color-success)" stroke-width="3">
                                                    <polyline points="20 6 9 17 4 12"></polyline>
                                                </svg>
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="task-title-cell <?= ($t['status'] === 'done') ? 'strike-through' : '' ?>">
                                            <strong><?= htmlspecialchars($t['title']) ?></strong>
                                        </div>
                                        <?php if (!empty($t['description'])): ?>
                                            <div class="cell-sub-text"><?= htmlspecialchars(mb_strimwidth($t['description'], 0, 70, '...')) ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($t['client_name'])): ?>
                                            <div><a href="/clients/<?= $t['client_id'] ?>"><?= htmlspecialchars($t['client_name']) ?></a></div>
                                        <?php endif; ?>
                                        <?php if (!empty($t['lead_title'])): ?>
                                            <small><a href="/leads/<?= $t['lead_id'] ?>" class="text-muted"><?= htmlspecialchars($t['lead_title']) ?></a></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="<?= !empty($t['is_overdue']) ? 'text-danger font-bold' : '' ?>">
                                            <?= date('d.m.Y', strtotime($t['due_date'])) ?>
                                        </span>
                                        <?php if (!empty($t['is_overdue'])): ?>
                                            <span class="badge badge-danger">Kechikkan</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge badge-priority-<?= htmlspecialchars($t['priority']) ?>">
                                            <?= $priorities[$t['priority']] ?? $t['priority'] ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-status-<?= htmlspecialchars($t['status']) ?>">
                                            <?= $statuses[$t['status']] ?? $t['status'] ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($t['user_name']) ?></td>
                                    <td class="text-right">
                                        <a href="/tasks/<?= $t['id'] ?>/edit" class="btn btn-icon" title="Tahrirlash">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                            </svg>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if ($tasks['last_page'] > 1): ?>
                    <div class="pagination-wrapper">
                        <div class="pagination-info">
                            Koʻrsatilmoqda: <?= count($tasks['data']) ?> / Jami: <?= $tasks['total'] ?> ta
                        </div>
                        <ul class="pagination">
                            <?php if ($tasks['has_prev']): ?>
                                <li class="page-item">
                                    <a href="?page=<?= $tasks['current_page'] - 1 ?>&search=<?= urlencode($search ?? '') ?>&status=<?= urlencode($status ?? '') ?>&priority=<?= urlencode($priority ?? '') ?>" class="page-link">&laquo; Oldingi</a>
                                </li>
                            <?php endif; ?>

                            <?php for ($i = 1; $i <= $tasks['last_page']; $i++): ?>
                                <li class="page-item <?= ($i === $tasks['current_page']) ? 'active' : '' ?>">
                                    <a href="?page=<?= $i ?>&search=<?= urlencode($search ?? '') ?>&status=<?= urlencode($status ?? '') ?>&priority=<?= urlencode($priority ?? '') ?>" class="page-link"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>

                            <?php if ($tasks['has_next']): ?>
                                <li class="page-item">
                                    <a href="?page=<?= $tasks['current_page'] + 1 ?>&search=<?= urlencode($search ?? '') ?>&status=<?= urlencode($status ?? '') ?>&priority=<?= urlencode($priority ?? '') ?>" class="page-link">Keyingi &raquo;</a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
