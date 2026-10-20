<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Tizimga kirish') ?> — LeadPulse CRM</title>
    <link rel="stylesheet" href="/assets/css/app.css">
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%232563eb' stroke-width='2.5'><polyline points='22 12 18 12 15 21 9 3 6 12 2 12'/></svg>">
</head>
<body class="auth-page-body">
    <div class="auth-container">
        <?= $content ?>
    </div>
</body>
</html>
