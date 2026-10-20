<div class="page-container form-page-container">
    <div class="page-header-actions">
        <div>
            <h2 class="page-heading">Foydalanuvchini tahrirlash</h2>
            <p class="page-subheading"><?= htmlspecialchars($editUser['name']) ?> profil maʼlumotlari</p>
        </div>
        <a href="/users" class="btn btn-outline">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            Foydalanuvchilar roʻyxatiga
        </a>
    </div>

    <div class="card form-card">
        <div class="card-body">
            <form action="/users/<?= $editUser['id'] ?>/update" method="POST" class="standard-form">
                <?= csrf_field() ?>

                <div class="form-row">
                    <div class="form-group col-6">
                        <label for="name" class="form-label required">F.I.Sh.</label>
                        <input 
                            type="text" 
                            id="name" 
                            name="name" 
                            class="form-control" 
                            value="<?= htmlspecialchars(old('name', $editUser['name'])) ?>" 
                            required
                        >
                    </div>

                    <div class="form-group col-6">
                        <label for="email" class="form-label required">Elektron pochta (Email)</label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            class="form-control" 
                            value="<?= htmlspecialchars(old('email', $editUser['email'])) ?>" 
                            required
                        >
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-6">
                        <label for="password" class="form-label">Yangi parol (ixtiyoriy)</label>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            class="form-control" 
                            placeholder="Oʻzgartirish kerak boʻlsa kiriting"
                        >
                    </div>

                    <div class="form-group col-6">
                        <label for="role" class="form-label required">Tizimdagi roli</label>
                        <select id="role" name="role" class="form-control">
                            <option value="manager" <?= ($editUser['role'] === 'manager') ? 'selected' : '' ?>>Sotuv menejeri (Manager)</option>
                            <option value="admin" <?= ($editUser['role'] === 'admin') ? 'selected' : '' ?>>Administrator (Admin)</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="is_active" value="1" <?= !empty($editUser['is_active']) ? 'checked' : '' ?>> <strong>Hisob faol (tizimga kirishga ruxsat berilgan)</strong>
                    </label>
                </div>

                <div class="form-actions">
                    <a href="/users" class="btn btn-outline">Bekor qilish</a>
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
