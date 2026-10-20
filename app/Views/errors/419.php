<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>419 — Sessiya muddati tugadi | LeadPulse</title>
    <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body class="error-page-body">
    <div class="error-card">
        <div class="error-icon warning">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"></circle>
                <polyline points="12 6 12 12 16 14"></polyline>
            </svg>
        </div>
        <h1 class="error-title">419 — Sessiya muddati tugadi</h1>
        <p class="error-text"><?= htmlspecialchars($message ?? 'Xavfsizlik belgisi (CSRF token) muddati tugagan. Iltimos, sahifani yangilang va qayta urinib koʻring.') ?></p>
        <div class="error-actions">
            <a href="javascript:history.back()" class="btn btn-outline">Ortga qaytish</a>
            <a href="/login" class="btn btn-primary">Tizimga kirish</a>
        </div>
    </div>
</body>
</html>
