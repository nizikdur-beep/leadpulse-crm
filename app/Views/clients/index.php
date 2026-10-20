<div class="page-container">
    <div class="page-header-actions">
        <div>
            <h2 class="page-heading">Mijozlar bazasi</h2>
            <p class="page-subheading">Jami <?= number_format($clients['total']) ?> ta mijoz roʻyxatdan oʻtgan</p>
        </div>
        <a href="/clients/create" class="btn btn-primary">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            <span>Yangi mijoz qoʻshish</span>
        </a>
    </div>

    <!-- Filters & Search Toolbar -->
    <div class="card toolbar-card">
        <form action="/clients" method="GET" class="filter-form">
            <div class="filter-grid">
                <div class="filter-col search-col">
                    <div class="input-with-icon">
                        <span class="input-icon">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="8"></circle>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                            </svg>
                        </span>
                        <input 
                            type="text" 
                            name="search" 
                            class="form-control" 
                            placeholder="Ism, kompaniya, telefon yoki email boʻyicha qidirish..." 
                            value="<?= htmlspecialchars($search ?? '') ?>"
                        >
                    </div>
                </div>

                <?php if (\App\Core\Auth::isAdmin() && !empty($managers)): ?>
                <div class="filter-col">
                    <select name="assigned_to" class="form-control">
                        <option value="">Barcha masʼul xodimlar</option>
                        <?php foreach ($managers as $mgr): ?>
                            <option value="<?= $mgr['id'] ?>" <?= ($assignedTo == $mgr['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($mgr['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>

                <div class="filter-col">
                    <select name="sort" class="form-control">
                        <option value="created_at" <?= ($sort === 'created_at') ? 'selected' : '' ?>>Yaratilgan sana boʻyicha</option>
                        <option value="name" <?= ($sort === 'name') ? 'selected' : '' ?>>Ism boʻyicha (A-Z)</option>
                        <option value="company" <?= ($sort === 'company') ? 'selected' : '' ?>>Kompaniya boʻyicha</option>
                    </select>
                </div>

                <div class="filter-actions">
                    <button type="submit" class="btn btn-secondary">Filtrlash</button>
                    <?php if (!empty($search) || !empty($assignedTo)): ?>
                        <a href="/clients" class="btn btn-outline" title="Filtrni tozalash">Tozalash</a>
                    <?php endif; ?>
                </div>
            </div>
        </form>
    </div>

    <!-- Clients Table Card -->
    <div class="card">
        <div class="card-body p-0">
            <?php if (empty($clients['data'])): ?>
                <div class="empty-state-box">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                    </svg>
                    <h3>Mijozlar topilmadi</h3>
                    <p>Qidiruv shartlariga mos mijoz topilmadi yoki hali mijoz qoʻshilmagan.</p>
                    <a href="/clients/create" class="btn btn-primary btn-sm">Birinchi mijozni qoʻshish</a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table custom-table">
                        <thead>
                            <tr>
                                <th>Mijoz / Kompaniya</th>
                                <th>Aloqa maʼlumotlari</th>
                                <th>Masʼul menejer</th>
                                <th>Bitimlar soni</th>
                                <th>Yutuq hajmi</th>
                                <th>Qoʻshilgan sana</th>
                                <th class="text-right">Amallar</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($clients['data'] as $c): ?>
                                <tr>
                                    <td>
                                        <div class="cell-main-text">
                                            <a href="/clients/<?= $c['id'] ?>" class="item-title-link">
                                                <?= htmlspecialchars($c['name']) ?>
                                            </a>
                                        </div>
                                        <?php if (!empty($c['company'])): ?>
                                            <div class="cell-sub-text">
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                                                    <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                                                </svg>
                                                <?= htmlspecialchars($c['company']) ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($c['phone'])): ?>
                                            <div class="contact-pill">
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                                                </svg>
                                                <a href="tel:<?= htmlspecialchars($c['phone']) ?>"><?= htmlspecialchars($c['phone']) ?></a>
                                            </div>
                                        <?php endif; ?>
                                        <?php if (!empty($c['email'])): ?>
                                            <div class="contact-pill">
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                                    <polyline points="22,6 12,13 2,6"></polyline>
                                                </svg>
                                                <a href="mailto:<?= htmlspecialchars($c['email']) ?>"><?= htmlspecialchars($c['email']) ?></a>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="user-badge-mini">
                                            <?= htmlspecialchars($c['assigned_user_name'] ?? 'Tayinlanmagan') ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-neutral"><?= (int)$c['leads_count'] ?> ta</span>
                                    </td>
                                    <td>
                                        <strong class="text-success"><?= number_format((float)$c['total_won_amount'], 0, '.', ' ') ?> soʻm</strong>
                                    </td>
                                    <td>
                                        <span class="text-muted"><?= date('d.m.Y', strtotime($c['created_at'])) ?></span>
                                    </td>
                                    <td class="text-right">
                                        <div class="action-btn-group">
                                            <a href="/clients/<?= $c['id'] ?>" class="btn btn-icon" title="Profilni koʻrish">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                    <circle cx="12" cy="12" r="3"></circle>
                                                </svg>
                                            </a>
                                            <a href="/clients/<?= $c['id'] ?>/edit" class="btn btn-icon" title="Tahrirlash">
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
                <?php if ($clients['last_page'] > 1): ?>
                    <div class="pagination-wrapper">
                        <div class="pagination-info">
                            Koʻrsatilmoqda: <?= count($clients['data']) ?> / Jami: <?= $clients['total'] ?> ta
                        </div>
                        <ul class="pagination">
                            <?php if ($clients['has_prev']): ?>
                                <li class="page-item">
                                    <a href="?page=<?= $clients['current_page'] - 1 ?>&search=<?= urlencode($search ?? '') ?>&assigned_to=<?= urlencode($assignedTo ?? '') ?>&sort=<?= urlencode($sort) ?>" class="page-link">&laquo; Oldingi</a>
                                </li>
                            <?php endif; ?>

                            <?php for ($i = 1; $i <= $clients['last_page']; $i++): ?>
                                <li class="page-item <?= ($i === $clients['current_page']) ? 'active' : '' ?>">
                                    <a href="?page=<?= $i ?>&search=<?= urlencode($search ?? '') ?>&assigned_to=<?= urlencode($assignedTo ?? '') ?>&sort=<?= urlencode($sort) ?>" class="page-link"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>

                            <?php if ($clients['has_next']): ?>
                                <li class="page-item">
                                    <a href="?page=<?= $clients['current_page'] + 1 ?>&search=<?= urlencode($search ?? '') ?>&assigned_to=<?= urlencode($assignedTo ?? '') ?>&sort=<?= urlencode($sort) ?>" class="page-link">Keyingi &raquo;</a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
