<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 — Server xatosi | LeadPulse</title>
    <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body class="error-page-body">
    <div class="error-card">
        <div class="error-icon danger">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                <line x1="12" y1="9" x2="12" y2="13"></line>
                <line x1="12" y1="17" x2="12.01" y2="17"></line>
            </svg>
        </div>
        <h1 class="error-title">500 — Tizimda xatolik yuz berdi</h1>
        <p class="error-text"><?= htmlspecialchars($message ?? 'Ichki server xatosi yuz berdi. Dasturchilar bu haqda ogohlantirildi.') ?></p>
        <div class="error-actions">
            <a href="/dashboard" class="btn btn-primary">Bosh sahifaga qaytish</a>
        </div>
    </div>
</body>
</html>
