<div class="page-container form-page-container">
    <div class="page-header-actions">
        <div>
            <h2 class="page-heading">Yangi foydalanuvchi qoʻshish</h2>
            <p class="page-subheading">CRM tizimi uchun yangi administrator yoki sotuv menejeri yaratish</p>
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
            <form action="/users" method="POST" class="standard-form">
                <?= csrf_field() ?>

                <div class="form-row">
                    <div class="form-group col-6">
                        <label for="name" class="form-label required">F.I.Sh. (Ism va familiya)</label>
                        <input 
                            type="text" 
                            id="name" 
                            name="name" 
                            class="form-control" 
                            placeholder="Masalan: Jamshid Aliyev" 
                            value="<?= htmlspecialchars(old('name')) ?>" 
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
                            placeholder="jamshid@leadpulse.uz" 
                            value="<?= htmlspecialchars(old('email')) ?>" 
                            required
                        >
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-6">
                        <label for="password" class="form-label required">Boshlangʻich parol</label>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            class="form-control" 
                            placeholder="Kamida 6 ta belgi" 
                            required
                        >
                    </div>

                    <div class="form-group col-6">
                        <label for="role" class="form-label required">Tizimdagi roli</label>
                        <select id="role" name="role" class="form-control">
                            <option value="manager" <?= (old('role') === 'manager') ? 'selected' : '' ?>>Sotuv menejeri (Manager)</option>
                            <option value="admin" <?= (old('role') === 'admin') ? 'selected' : '' ?>>Administrator (Admin)</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="is_active" value="1" checked> <strong>Ushbu hisob darhol faollashtirilsin</strong>
                    </label>
                </div>

                <div class="form-actions">
                    <a href="/users" class="btn btn-outline">Bekor qilish</a>
                    <button type="submit" class="btn btn-primary">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="8.5" cy="7" r="4"></circle>
                            <line x1="20" y1="8" x2="20" y2="14"></line>
                            <line x1="23" y1="11" x2="17" y2="11"></line>
                        </svg>
                        <span>Foydalanuvchini yaratish</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
