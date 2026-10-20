<div class="page-container">
    <div class="page-header-actions">
        <div>
            <h2 class="page-heading">Barcha faoliyatlar tarixi</h2>
            <p class="page-subheading">Qoʻngʻiroqlar, uchrashuvlar, xatlar va qaydlar jurnali</p>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <?php if (empty($activities)): ?>
                <div class="empty-state-box">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                    </svg>
                    <h3>Faoliyatlar mavjud emas</h3>
                    <p>Mijozlar yoki bitimlar sahifasidan yangi faoliyat qayd etishingiz mumkin.</p>
                </div>
            <?php else: ?>
                <div class="timeline-list p-4">
                    <?php foreach ($activities as $act): ?>
                        <div class="timeline-item">
                            <div class="timeline-dot dot-type-<?= htmlspecialchars($act['type']) ?>"></div>
                            <div class="timeline-content">
                                <div class="timeline-header">
                                    <span class="timeline-user"><strong><?= htmlspecialchars($act['user_name']) ?></strong></span>
                                    <span class="badge badge-sm badge-type-<?= htmlspecialchars($act['type']) ?>">
                                        <?= $types[$act['type']] ?? $act['type'] ?>
                                    </span>
                                    <span class="timeline-time"><?= date('d.m.Y H:i', strtotime($act['created_at'])) ?></span>
                                </div>
                                <div class="timeline-text"><?= nl2br(htmlspecialchars($act['note'])) ?></div>
                                <div class="timeline-meta mt-2">
                                    <?php if (!empty($act['client_name'])): ?>
                                        <a href="/clients/<?= $act['client_id'] ?>" class="meta-tag">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                                <circle cx="9" cy="7" r="4"></circle>
                                            </svg>
                                            <?= htmlspecialchars($act['client_name']) ?>
                                        </a>
                                    <?php endif; ?>
                                    <?php if (!empty($act['lead_title'])): ?>
                                        <a href="/leads/<?= $act['lead_id'] ?>" class="meta-tag">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                                            </svg>
                                            <?= htmlspecialchars($act['lead_title']) ?>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
