<div class="page-container">
    <div class="page-header-actions">
        <div>
            <h2 class="page-heading">Foydalanuvchilar va Xodimlar</h2>
            <p class="page-subheading">Tizim administratorlari va sotuv menejerlarini boshqarish</p>
        </div>
        <a href="/users/create" class="btn btn-primary">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            <span>Yangi xodim qoʻshish</span>
        </a>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table custom-table">
                    <thead>
                        <tr>
                            <th>Ism / F.I.Sh.</th>
                            <th>Email</th>
                            <th>Roli</th>
                            <th>Holati</th>
                            <th>Qoʻshilgan sana</th>
                            <th class="text-right">Amallar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $u): ?>
                            <tr>
                                <td>
                                    <div class="user-cell-wrap">
                                        <div class="user-avatar-mini">
                                            <?= strtoupper(substr($u['name'], 0, 1)) ?>
                                        </div>
                                        <strong><?= htmlspecialchars($u['name']) ?></strong>
                                    </div>
                                </td>
                                <td><?= htmlspecialchars($u['email']) ?></td>
                                <td>
                                    <span class="badge badge-role-<?= htmlspecialchars($u['role']) ?>">
                                        <?= ($u['role'] === 'admin') ? 'Administrator' : 'Menejer' ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if (!empty($u['is_active'])): ?>
                                        <span class="badge badge-success">Faol</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger">Nofaol (Bloklangan)</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= date('d.m.Y H:i', strtotime($u['created_at'])) ?></td>
                                <td class="text-right">
                                    <a href="/users/<?= $u['id'] ?>/edit" class="btn btn-icon" title="Tahrirlash">
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
        </div>
    </div>
</div>
