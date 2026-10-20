<div class="auth-card">
    <div class="auth-header">
        <div class="auth-brand-logo">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
            </svg>
        </div>
        <h2 class="auth-title">LeadPulse CRM</h2>
        <p class="auth-subtitle">Boshqaruv paneliga kirish uchun maʼlumotlaringizni kiriting</p>
    </div>

    <?php if (has_flash('error')): ?>
        <div class="alert alert-danger" role="alert">
            <div class="alert-message"><?= htmlspecialchars(flash('error')) ?></div>
        </div>
    <?php endif; ?>

    <?php if (has_flash('success')): ?>
        <div class="alert alert-success" role="alert">
            <div class="alert-message"><?= htmlspecialchars(flash('success')) ?></div>
        </div>
    <?php endif; ?>

    <form action="/login" method="POST" class="auth-form" autocomplete="on">
        <?= csrf_field() ?>

        <div class="form-group">
            <label for="email" class="form-label">Elektron pochta (Email)</label>
            <div class="input-with-icon">
                <span class="input-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                        <polyline points="22,6 12,13 2,6"></polyline>
                    </svg>
                </span>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    class="form-control" 
                    placeholder="nom@kompaniya.uz" 
                    value="<?= htmlspecialchars(old('email')) ?>" 
                    required 
                    autofocus
                >
            </div>
        </div>

        <div class="form-group">
            <label for="password" class="form-label">Parol</label>
            <div class="input-with-icon">
                <span class="input-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                    </svg>
                </span>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    class="form-control" 
                    placeholder="••••••••" 
                    required
                >
            </div>
        </div>

        <button type="submit" class="btn btn-primary btn-block btn-lg">
            <span>Tizimga kirish</span>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="5" y1="12" x2="19" y2="12"></line>
                <polyline points="12 5 19 12 12 19"></polyline>
            </svg>
        </button>
    </form>

    <div class="demo-credentials-box">
        <div class="demo-title">Namunaviy kirish maʼlumotlari (Demo):</div>
        <div class="demo-item">
            <span class="demo-role">Admin:</span>
            <code>admin@leadpulse.uz</code> / <code>Admin@12345</code>
        </div>
        <div class="demo-item">
            <span class="demo-role">Menejer:</span>
            <code>aziz@leadpulse.uz</code> / <code>Manager@123</code>
        </div>
    </div>
</div>
