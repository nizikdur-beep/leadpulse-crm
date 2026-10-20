<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 — Ruxsat etilmagan | LeadPulse</title>
    <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body class="error-page-body">
    <div class="error-card">
        <div class="error-icon danger">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line>
            </svg>
        </div>
        <h1 class="error-title">403 — Kirish taqiqlangan</h1>
        <p class="error-text"><?= htmlspecialchars($message ?? 'Sizda ushbu sahifaga yoki amalga ruxsat mavjud emas.') ?></p>
        <div class="error-actions">
            <a href="/dashboard" class="btn btn-primary">Bosh sahifaga qaytish</a>
        </div>
    </div>
</body>
</html>
