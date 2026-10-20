<div class="page-container">
    <div class="page-header-actions">
        <div class="profile-header-title">
            <a href="/leads" class="btn btn-icon mr-2" title="Bitimlar roʻyxatiga">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
            </a>
            <div>
                <h2 class="page-heading"><?= htmlspecialchars($lead['title']) ?></h2>
                <p class="page-subheading">
                    Mijoz: <a href="/clients/<?= $lead['client_id'] ?>"><strong><?= htmlspecialchars($lead['client_name']) ?></strong></a>
                    <?= !empty($lead['client_company']) ? ' (' . htmlspecialchars($lead['client_company']) . ')' : '' ?>
                </p>
            </div>
        </div>

        <div class="header-action-buttons">
            <a href="/tasks/create?lead_id=<?= $lead['id'] ?>&client_id=<?= $lead['client_id'] ?>" class="btn btn-outline btn-sm">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 5v14M5 12h14"></path>
                </svg>
                Vazifa biriktirish
            </a>
            <a href="/leads/<?= $lead['id'] ?>/edit" class="btn btn-outline btn-sm">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                </svg>
                Tahrirlash
            </a>
            <form action="/leads/<?= $lead['id'] ?>/delete" method="POST" class="inline-form" onsubmit="return confirm('Ushbu bitimni oʻchirishni tasdiqlaysizmi?');">
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

    <!-- Interactive Stage Stepper Header -->
    <div class="card stage-stepper-card mb-4">
        <div class="card-body py-3">
            <div class="stage-stepper">
                <?php 
                $stageOrder = ['new', 'contact', 'proposal', 'won'];
                $isLost = ($lead['stage'] === 'lost');
                foreach ($stageOrder as $stepKey): 
                    $isActive = ($lead['stage'] === $stepKey);
                    $stepIndex = array_search($stepKey, $stageOrder);
                    $currentIndex = array_search($lead['stage'], $stageOrder);
                    $isPassed = ($currentIndex !== false && $stepIndex < $currentIndex);
                    $stepClass = $isActive ? 'active' : ($isPassed ? 'passed' : '');
                ?>
                    <form action="/leads/<?= $lead['id'] ?>/stage" method="POST" class="stage-step-form">
                        <?= csrf_field() ?>
                        <input type="hidden" name="stage" value="<?= $stepKey ?>">
                        <button type="submit" class="stage-step-btn <?= $stepClass ?>">
                            <span class="step-num"><?= $stepIndex + 1 ?></span>
                            <span class="step-label"><?= $stages[$stepKey] ?></span>
                        </button>
                    </form>
                <?php endforeach; ?>

                <!-- Lost option -->
                <form action="/leads/<?= $lead['id'] ?>/stage" method="POST" class="stage-step-form">
                    <?= csrf_field() ?>
                    <input type="hidden" name="stage" value="lost">
                    <button type="submit" class="stage-step-btn btn-lost <?= $isLost ? 'active' : '' ?>">
                        <span class="step-num">&times;</span>
                        <span class="step-label">Yoʻqotilgan</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="profile-layout-grid">
        <!-- Left: Summary Info -->
        <div class="profile-sidebar-col">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Bitim tafsilotlari</h3>
                </div>
                <div class="card-body">
                    <div class="info-property-list">
                        <div class="info-property-item">
                            <span class="info-prop-label">Summasi:</span>
                            <span class="info-prop-val"><strong class="amount-highlight"><?= number_format((float)$lead['amount'], 0, '.', ' ') ?> soʻm</strong></span>
                        </div>
                        <div class="info-property-item">
                            <span class="info-prop-label">Joriy bosqich:</span>
                            <span class="info-prop-val">
                                <span class="badge badge-stage-<?= htmlspecialchars($lead['stage']) ?>">
                                    <?= $stages[$lead['stage']] ?? $lead['stage'] ?>
                                </span>
                            </span>
                        </div>
                        <?php if ($lead['stage'] === 'lost' && !empty($lead['lost_reason'])): ?>
                            <div class="info-property-item">
                                <span class="info-prop-label text-danger">Yoʻqotish sababi:</span>
                                <span class="info-prop-val text-danger"><strong><?= htmlspecialchars($lead['lost_reason']) ?></strong></span>
                            </div>
                        <?php endif; ?>
                        <div class="info-property-item">
                            <span class="info-prop-label">Mijoz:</span>
                            <span class="info-prop-val"><a href="/clients/<?= $lead['client_id'] ?>"><?= htmlspecialchars($lead['client_name']) ?></a></span>
                        </div>
                        <div class="info-property-item">
                            <span class="info-prop-label">Telefon:</span>
                            <span class="info-prop-val"><?= htmlspecialchars($lead['client_phone'] ?: '-') ?></span>
                        </div>
                        <div class="info-property-item">
                            <span class="info-prop-label">Masʼul menejer:</span>
                            <span class="info-prop-val"><strong><?= htmlspecialchars($lead['manager_name']) ?></strong></span>
                        </div>
                        <div class="info-property-item">
                            <span class="info-prop-label">Manba:</span>
                            <span class="info-prop-val"><?= htmlspecialchars($lead['source'] ?: 'Koʻrsatilmagan') ?></span>
                        </div>
                        <div class="info-property-item">
                            <span class="info-prop-label">Keyingi aloqa:</span>
                            <span class="info-prop-val"><?= $lead['next_contact_at'] ? date('d.m.Y', strtotime($lead['next_contact_at'])) : '-' ?></span>
                        </div>
                        <div class="info-property-item">
                            <span class="info-prop-label">Yaratilgan:</span>
                            <span class="info-prop-val"><?= date('d.m.Y H:i', strtotime($lead['created_at'])) ?></span>
                        </div>
                    </div>

                    <?php if (!empty($lead['notes'])): ?>
                        <div class="notes-preview-box">
                            <div class="notes-title">Izoh va talablar:</div>
                            <div class="notes-body"><?= nl2br(htmlspecialchars($lead['notes'])) ?></div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Quick Add Activity -->
            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">Murojaat / Faoliyat qayd etish</h3>
                </div>
                <div class="card-body">
                    <form action="/activities" method="POST" class="standard-form">
                        <?= csrf_field() ?>
                        <input type="hidden" name="lead_id" value="<?= $lead['id'] ?>">
                        <input type="hidden" name="client_id" value="<?= $lead['client_id'] ?>">

                        <div class="form-group">
                            <label for="type" class="form-label">Faoliyat turi</label>
                            <select id="type" name="type" class="form-control">
                                <option value="call">📞 Qoʻngʻiroq</option>
                                <option value="meeting">🤝 Uchrashuv</option>
                                <option value="email">✉️ Email xat</option>
                                <option value="message">💬 Xabar / Telegram</option>
                                <option value="note" selected>📝 Eslatma / Izoh</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="act_note" class="form-label">Mazmuni</label>
                            <textarea id="act_note" name="note" class="form-control" rows="3" placeholder="Suhbat tafsilotlari yoki kelishuv..." required></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block btn-sm">Qayd etish</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Right: Stage History and Activities Timeline -->
        <div class="profile-main-col">
            <!-- Stage History Timeline -->
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title">Bosqichlar oʻzgarishi tarixi (Audit)</h3>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($history)): ?>
                        <div class="empty-state-box p-3">
                            <p>Bosqich oʻzgarishlari mavjud emas.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Oldingi bosqich</th>
                                        <th>Yangi bosqich</th>
                                        <th>Oʻzgartirgan shaxs</th>
                                        <th>Sana va vaqt</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($history as $h): ?>
                                        <tr>
                                            <td>
                                                <span class="badge badge-sm badge-stage-<?= htmlspecialchars($h['old_stage']) ?>">
                                                    <?= $stages[$h['old_stage']] ?? $h['old_stage'] ?>
                                                </span>
                                            </td>
                                            <td>
                                                &rarr;
                                                <span class="badge badge-sm badge-stage-<?= htmlspecialchars($h['new_stage']) ?>">
                                                    <?= $stages[$h['new_stage']] ?? $h['new_stage'] ?>
                                                </span>
                                            </td>
                                            <td><?= htmlspecialchars($h['user_name']) ?></td>
                                            <td><span class="text-muted"><?= date('d.m.Y H:i:s', strtotime($h['created_at'])) ?></span></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Activities Timeline for Lead -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Ushbu bitim boʻyicha faoliyatlar (<?= count($activities) ?>)</h3>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($activities)): ?>
                        <div class="empty-state-box p-3">
                            <p>Hozircha hech qanday faoliyat yozilmagan.</p>
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
