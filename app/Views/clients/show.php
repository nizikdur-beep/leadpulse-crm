<div class="page-container">
    <div class="page-header-actions">
        <div class="profile-header-title">
            <a href="/clients" class="btn btn-icon mr-2" title="Mijozlar roʻyxatiga">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
            </a>
            <div>
                <h2 class="page-heading"><?= htmlspecialchars($client['name']) ?></h2>
                <p class="page-subheading"><?= htmlspecialchars($client['company'] ?: 'Kompaniya koʻrsatilmagan') ?></p>
            </div>
        </div>

        <div class="header-action-buttons">
            <a href="/leads/create?client_id=<?= $client['id'] ?>" class="btn btn-primary btn-sm">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                Yangi bitim
            </a>
            <a href="/tasks/create?client_id=<?= $client['id'] ?>" class="btn btn-outline btn-sm">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 5v14M5 12h14"></path>
                </svg>
                Vazifa
            </a>
            <a href="/clients/<?= $client['id'] ?>/edit" class="btn btn-outline btn-sm">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                </svg>
                Tahrirlash
            </a>
            <form action="/clients/<?= $client['id'] ?>/delete" method="POST" class="inline-form" onsubmit="return confirm('Mijozni oʻchirishni tasdiqlaysizmi? (Tarix va maʼlumotlar arxivlanadi)');">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-danger btn-sm" title="Oʻchirish">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="3 6 5 6 21 6"></polyline>
                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                    </svg>
                </button>
            </form>
        </div>
    </div>

    <!-- Client Profile Grid -->
    <div class="profile-layout-grid">
        <!-- Left Side: Profile Info Card -->
        <div class="profile-sidebar-col">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Mijoz maʼlumotlari</h3>
                </div>
                <div class="card-body">
                    <div class="info-property-list">
                        <div class="info-property-item">
                            <span class="info-prop-label">Telefon:</span>
                            <span class="info-prop-val">
                                <?= !empty($client['phone']) ? '<a href="tel:' . htmlspecialchars($client['phone']) . '">' . htmlspecialchars($client['phone']) . '</a>' : '<span class="text-muted">Mavjud emas</span>' ?>
                            </span>
                        </div>
                        <div class="info-property-item">
                            <span class="info-prop-label">Email:</span>
                            <span class="info-prop-val">
                                <?= !empty($client['email']) ? '<a href="mailto:' . htmlspecialchars($client['email']) . '">' . htmlspecialchars($client['email']) . '</a>' : '<span class="text-muted">Mavjud emas</span>' ?>
                            </span>
                        </div>
                        <div class="info-property-item">
                            <span class="info-prop-label">Kompaniya:</span>
                            <span class="info-prop-val"><?= htmlspecialchars($client['company'] ?: 'Mavjud emas') ?></span>
                        </div>
                        <div class="info-property-item">
                            <span class="info-prop-label">Manzil:</span>
                            <span class="info-prop-val"><?= htmlspecialchars($client['address'] ?: 'Koʻrsatilmagan') ?></span>
                        </div>
                        <div class="info-property-item">
                            <span class="info-prop-label">Masʼul menejer:</span>
                            <span class="info-prop-val"><strong><?= htmlspecialchars($client['assigned_user_name'] ?? 'Tayinlanmagan') ?></strong></span>
                        </div>
                        <div class="info-property-item">
                            <span class="info-prop-label">Qoʻshgan shaxs:</span>
                            <span class="info-prop-val"><?= htmlspecialchars($client['created_by_name'] ?? '-') ?></span>
                        </div>
                        <div class="info-property-item">
                            <span class="info-prop-label">Roʻyxatdan oʻtgan:</span>
                            <span class="info-prop-val"><?= date('d.m.Y H:i', strtotime($client['created_at'])) ?></span>
                        </div>
                    </div>

                    <?php if (!empty($client['notes'])): ?>
                        <div class="notes-preview-box">
                            <div class="notes-title">Izohlar:</div>
                            <div class="notes-body"><?= nl2br(htmlspecialchars($client['notes'])) ?></div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Quick Add Activity Form -->
            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">Faoliyat yoki eslatma yozish</h3>
                </div>
                <div class="card-body">
                    <form action="/activities" method="POST" class="standard-form">
                        <?= csrf_field() ?>
                        <input type="hidden" name="client_id" value="<?= $client['id'] ?>">

                        <div class="form-group">
                            <label for="act_type" class="form-label">Turi</label>
                            <select id="act_type" name="type" class="form-control">
                                <option value="call">📞 Qoʻngʻiroq</option>
                                <option value="meeting">🤝 Uchrashuv</option>
                                <option value="email">✉️ Email xat</option>
                                <option value="message">💬 Xabar / Telegram</option>
                                <option value="note" selected>📝 Eslatma / Izoh</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="act_note" class="form-label">Mazmuni</label>
                            <textarea id="act_note" name="note" class="form-control" rows="3" placeholder="Suhbat natijasi yoki eslatma..." required></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block btn-sm">Qayd etish</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Right Side: Deals, Activities, Tasks -->
        <div class="profile-main-col">
            <!-- Related Leads / Deals -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Bogʻlangan bitimlar (<?= count($leads) ?>)</h3>
                    <a href="/leads/create?client_id=<?= $client['id'] ?>" class="btn btn-outline btn-sm">+ Bitim qoʻshish</a>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($leads)): ?>
                        <div class="empty-state-box p-3">
                            <p>Ushbu mijozga biriktirilgan bitimlar mavjud emas.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Sarlavha</th>
                                        <th>Summa</th>
                                        <th>Bosqich</th>
                                        <th>Keyingi aloqa</th>
                                        <th class="text-right">Batafsil</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($leads as $l): ?>
                                        <tr>
                                            <td>
                                                <a href="/leads/<?= $l['id'] ?>" class="item-title-link">
                                                    <?= htmlspecialchars($l['title']) ?>
                                                </a>
                                            </td>
                                            <td><strong><?= number_format((float)$l['amount'], 0, '.', ' ') ?> soʻm</strong></td>
                                            <td>
                                                <span class="badge badge-stage-<?= htmlspecialchars($l['stage']) ?>">
                                                    <?= \App\Models\Lead::STAGES[$l['stage']] ?? $l['stage'] ?>
                                                </span>
                                            </td>
                                            <td><?= $l['next_contact_at'] ? date('d.m.Y', strtotime($l['next_contact_at'])) : '-' ?></td>
                                            <td class="text-right">
                                                <a href="/leads/<?= $l['id'] ?>" class="btn btn-sm btn-outline">Koʻrish</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Activities Timeline for Client -->
            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">Faoliyatlar tarixi (<?= count($activities) ?>)</h3>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($activities)): ?>
                        <div class="empty-state-box p-3">
                            <p>Hali hech qanday faoliyat yozilmagan.</p>
                        </div>
                    <?php else: ?>
                        <div class="timeline-list">
                            <?php foreach ($activities as $act): ?>
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
                                        <div class="timeline-text"><?= nl2br(htmlspecialchars($act['note'])) ?></div>
                                        <?php if (!empty($act['lead_title'])): ?>
                                            <div class="timeline-meta">
                                                <span class="meta-tag">Bitim: <?= htmlspecialchars($act['lead_title']) ?></span>
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
</div>
