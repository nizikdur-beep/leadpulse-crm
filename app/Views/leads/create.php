<div class="page-container form-page-container">
    <div class="page-header-actions">
        <div>
            <h2 class="page-heading">Yangi bitim qoʻshish</h2>
            <p class="page-subheading">Savdo voronkasiga yangi potentsial bitim kiritish</p>
        </div>
        <a href="/leads" class="btn btn-outline">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            Bitimlar roʻyxatiga
        </a>
    </div>

    <div class="card form-card">
        <div class="card-body">
            <form action="/leads" method="POST" class="standard-form">
                <?= csrf_field() ?>

                <div class="form-group">
                    <label for="title" class="form-label required">Bitim sarlavhasi (nomi)</label>
                    <input 
                        type="text" 
                        id="title" 
                        name="title" 
                        class="form-control" 
                        placeholder="Masalan: ERP tizimini joriy qilish va integratsiya" 
                        value="<?= htmlspecialchars(old('title')) ?>" 
                        required
                    >
                </div>

                <div class="form-row">
                    <div class="form-group col-6">
                        <label for="client_id" class="form-label required">Mijozni tanlang</label>
                        <select id="client_id" name="client_id" class="form-control" required>
                            <option value="">-- Mijozni tanlang --</option>
                            <?php foreach ($clients as $c): ?>
                                <option value="<?= $c['id'] ?>" <?= ($clientId == $c['id'] || old('client_id') == $c['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($c['name']) ?> <?= !empty($c['company']) ? ' (' . htmlspecialchars($c['company']) . ')' : '' ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="form-hint"><a href="/clients/create">+ Yangi mijoz qoʻshish</a></small>
                    </div>

                    <div class="form-group col-6">
                        <label for="amount" class="form-label">Bitim summasi (soʻm)</label>
                        <input 
                            type="number" 
                            step="0.01" 
                            id="amount" 
                            name="amount" 
                            class="form-control" 
                            placeholder="0.00" 
                            value="<?= htmlspecialchars(old('amount', '0')) ?>"
                        >
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-6">
                        <label for="stage" class="form-label">Boshlangʻich bosqich</label>
                        <select id="stage" name="stage" class="form-control">
                            <?php foreach ($stages as $k => $label): ?>
                                <option value="<?= $k ?>" <?= (old('stage') === $k) ? 'selected' : '' ?>><?= $label ?></option>
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
                            placeholder="Veb-sayt, Telegram, Tavsiya, Sovuq qoʻngʻiroq..." 
                            value="<?= htmlspecialchars(old('source')) ?>"
                        >
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-6">
                        <label for="next_contact_at" class="form-label">Keyingi aloqa sanasi</label>
                        <input 
                            type="date" 
                            id="next_contact_at" 
                            name="next_contact_at" 
                            class="form-control" 
                            value="<?= htmlspecialchars(old('next_contact_at')) ?>"
                        >
                    </div>

                    <?php if (\App\Core\Auth::isAdmin() && !empty($managers)): ?>
                        <div class="form-group col-6">
                            <label for="user_id" class="form-label">Masʼul menejer</label>
                            <select id="user_id" name="user_id" class="form-control">
                                <?php foreach ($managers as $mgr): ?>
                                    <option value="<?= $mgr['id'] ?>" <?= (old('user_id') == $mgr['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($mgr['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="notes" class="form-label">Batafsil maʼlumot va talablar</label>
                    <textarea 
                        id="notes" 
                        name="notes" 
                        class="form-control" 
                        rows="4" 
                        placeholder="Mijozning ehtiyojlari, taklif qilingan xizmatlar yoki shartlar..."
                    ><?= htmlspecialchars(old('notes')) ?></textarea>
                </div>

                <div class="form-actions">
                    <a href="/leads" class="btn btn-outline">Bekor qilish</a>
                    <button type="submit" class="btn btn-primary">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                            <polyline points="17 21 17 13 7 13 7 21"></polyline>
                            <polyline points="7 3 7 8 15 8"></polyline>
                        </svg>
                        <span>Bitimni saqlash</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
