<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 — Sahifa topilmadi | LeadPulse</title>
    <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body class="error-page-body">
    <div class="error-card">
        <div class="error-icon warning">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="8" x2="12" y2="12"></line>
                <line x1="12" y1="16" x2="12.01" y2="16"></line>
            </svg>
        </div>
        <h1 class="error-title">404 — Sahifa topilmadi</h1>
        <p class="error-text"><?= htmlspecialchars($message ?? 'Siz qidirayotgan sahifa mavjud emas yoki oʻchirilgan.') ?></p>
        <div class="error-actions">
            <a href="/dashboard" class="btn btn-primary">Bosh sahifaga qaytish</a>
        </div>
    </div>
</body>
</html>
