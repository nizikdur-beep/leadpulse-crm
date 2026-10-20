<div class="page-container form-page-container">
    <div class="page-header-actions">
        <div>
            <h2 class="page-heading">Mijozni tahrirlash</h2>
            <p class="page-subheading"><?= htmlspecialchars($client['name']) ?> maʼlumotlarini yangilash</p>
        </div>
        <a href="/clients/<?= $client['id'] ?>" class="btn btn-outline">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            Profilga qaytish
        </a>
    </div>

    <div class="card form-card">
        <div class="card-body">
            <form action="/clients/<?= $client['id'] ?>/update" method="POST" class="standard-form">
                <?= csrf_field() ?>

                <div class="form-row">
                    <div class="form-group col-6">
                        <label for="name" class="form-label required">Mijoz ismi yoki F.I.Sh.</label>
                        <input 
                            type="text" 
                            id="name" 
                            name="name" 
                            class="form-control" 
                            value="<?= htmlspecialchars(old('name', $client['name'])) ?>" 
                            required
                        >
                    </div>

                    <div class="form-group col-6">
                        <label for="company" class="form-label">Kompaniya nomi</label>
                        <input 
                            type="text" 
                            id="company" 
                            name="company" 
                            class="form-control" 
                            value="<?= htmlspecialchars(old('company', $client['company'] ?? '')) ?>"
                        >
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-6">
                        <label for="phone" class="form-label">Telefon raqami</label>
                        <input 
                            type="text" 
                            id="phone" 
                            name="phone" 
                            class="form-control" 
                            value="<?= htmlspecialchars(old('phone', $client['phone'] ?? '')) ?>"
                        >
                    </div>

                    <div class="form-group col-6">
                        <label for="email" class="form-label">Elektron pochta (Email)</label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            class="form-control" 
                            value="<?= htmlspecialchars(old('email', $client['email'] ?? '')) ?>"
                        >
                    </div>
                </div>

                <div class="form-group">
                    <label for="address" class="form-label">Manzil</label>
                    <input 
                        type="text" 
                        id="address" 
                        name="address" 
                        class="form-control" 
                        value="<?= htmlspecialchars(old('address', $client['address'] ?? '')) ?>"
                    >
                </div>

                <?php if (\App\Core\Auth::isAdmin() && !empty($managers)): ?>
                <div class="form-group">
                    <label for="assigned_to" class="form-label">Masʼul menejer</label>
                    <select id="assigned_to" name="assigned_to" class="form-control">
                        <?php foreach ($managers as $mgr): ?>
                            <option value="<?= $mgr['id'] ?>" <?= ($client['assigned_to'] == $mgr['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($mgr['name']) ?> (<?= htmlspecialchars($mgr['email']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>

                <div class="form-group">
                    <label for="notes" class="form-label">Izoh va eslatmalar</label>
                    <textarea 
                        id="notes" 
                        name="notes" 
                        class="form-control" 
                        rows="4"
                    ><?= htmlspecialchars(old('notes', $client['notes'] ?? '')) ?></textarea>
                </div>

                <div class="form-actions">
                    <a href="/clients/<?= $client['id'] ?>" class="btn btn-outline">Bekor qilish</a>
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
