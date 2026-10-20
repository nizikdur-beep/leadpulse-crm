<div class="page-container form-page-container">
    <div class="page-header-actions">
        <div>
            <h2 class="page-heading">Bitimni tahrirlash</h2>
            <p class="page-subheading"><?= htmlspecialchars($lead['title']) ?> maʼlumotlarini yangilash</p>
        </div>
        <a href="/leads/<?= $lead['id'] ?>" class="btn btn-outline">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            Profilga qaytish
        </a>
    </div>

    <div class="card form-card">
        <div class="card-body">
            <form action="/leads/<?= $lead['id'] ?>/update" method="POST" class="standard-form">
                <?= csrf_field() ?>

                <div class="form-group">
                    <label for="title" class="form-label required">Bitim sarlavhasi</label>
                    <input 
                        type="text" 
                        id="title" 
                        name="title" 
                        class="form-control" 
                        value="<?= htmlspecialchars(old('title', $lead['title'])) ?>" 
                        required
                    >
                </div>

                <div class="form-row">
                    <div class="form-group col-6">
                        <label for="client_id" class="form-label required">Mijoz</label>
                        <select id="client_id" name="client_id" class="form-control" required>
                            <?php foreach ($clients as $c): ?>
                                <option value="<?= $c['id'] ?>" <?= ($lead['client_id'] == $c['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($c['name']) ?> <?= !empty($c['company']) ? ' (' . htmlspecialchars($c['company']) . ')' : '' ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group col-6">
                        <label for="amount" class="form-label">Bitim summasi (soʻm)</label>
                        <input 
                            type="number" 
                            step="0.01" 
                            id="amount" 
                            name="amount" 
                            class="form-control" 
                            value="<?= htmlspecialchars(old('amount', $lead['amount'])) ?>"
                        >
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-6">
                        <label for="stage" class="form-label">Bosqich</label>
                        <select id="stage" name="stage" class="form-control" onchange="toggleLostReason(this.value)">
                            <?php foreach ($stages as $k => $label): ?>
                                <option value="<?= $k ?>" <?= ($lead['stage'] === $k) ? 'selected' : '' ?>><?= $label ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group col-6">
                        <label for="source" class="form-label">Murojaat manbasi</label>
                        <input 
                            type="text" 
                            id="source" 
                            name="source" 
                            class="form-control" 
                            value="<?= htmlspecialchars(old('source', $lead['source'] ?? '')) ?>"
                        >
                    </div>
                </div>

                <div class="form-group" id="lostReasonGroup" style="<?= ($lead['stage'] === 'lost') ? '' : 'display: none;' ?>">
                    <label for="lost_reason" class="form-label">Bitim yoʻqotilishining sababi</label>
                    <input 
                        type="text" 
                        id="lost_reason" 
                        name="lost_reason" 
                        class="form-control" 
                        placeholder="Masalan: Byudjet yetarli emas, raqobatchi tanlandi..." 
                        value="<?= htmlspecialchars(old('lost_reason', $lead['lost_reason'] ?? '')) ?>"
                    >
                </div>

                <div class="form-row">
                    <div class="form-group col-6">
                        <label for="next_contact_at" class="form-label">Keyingi aloqa sanasi</label>
                        <input 
                            type="date" 
                            id="next_contact_at" 
                            name="next_contact_at" 
                            class="form-control" 
                            value="<?= htmlspecialchars(old('next_contact_at', $lead['next_contact_at'] ?? '')) ?>"
                        >
                    </div>

                    <?php if (\App\Core\Auth::isAdmin() && !empty($managers)): ?>
                        <div class="form-group col-6">
                            <label for="user_id" class="form-label">Masʼul menejer</label>
                            <select id="user_id" name="user_id" class="form-control">
                                <?php foreach ($managers as $mgr): ?>
                                    <option value="<?= $mgr['id'] ?>" <?= ($lead['user_id'] == $mgr['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($mgr['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="notes" class="form-label">Izoh va qaydlar</label>
                    <textarea 
                        id="notes" 
                        name="notes" 
                        class="form-control" 
                        rows="4"
                    ><?= htmlspecialchars(old('notes', $lead['notes'] ?? '')) ?></textarea>
                </div>

                <div class="form-actions">
                    <a href="/leads/<?= $lead['id'] ?>" class="btn btn-outline">Bekor qilish</a>
                    <button type="submit" class="btn btn-primary">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                            <polyline points="17 21 17 13 7 13 7 21"></polyline>
                            <polyline points="7 3 7 8 15 8"></polyline>
                        </svg>
                        <span>Oʻzgarishlarni saqlash</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function toggleLostReason(val) {
    const group = document.getElementById('lostReasonGroup');
    if (val === 'lost') {
        group.style.display = 'block';
    } else {
        group.style.display = 'none';
    }
}
</script>
