<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'LeadPulse CRM') ?> — LeadPulse</title>
    <link rel="stylesheet" href="/assets/css/app.css">
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%232563eb' stroke-width='2.5'><polyline points='22 12 18 12 15 21 9 3 6 12 2 12'/></svg>">
</head>
<body class="app-body">
    <div class="app-wrapper">
        <!-- Sidebar Navigation -->
        <?php include __DIR__ . '/sidebar.php'; ?>

        <!-- Main Workspace -->
        <div class="app-main-content">
            <!-- Header Bar -->
            <?php include __DIR__ . '/header.php'; ?>

            <!-- Flash Notifications -->
            <div class="content-body">
                <?php if (has_flash('success')): ?>
                    <div class="alert alert-success" role="alert">
                        <div class="alert-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                <polyline points="22 4 12 14.01 9 11.01"></polyline>
                            </svg>
                        </div>
                        <div class="alert-message"><?= htmlspecialchars(flash('success')) ?></div>
                        <button type="button" class="alert-close-btn" onclick="this.parentElement.remove()">&times;</button>
                    </div>
                <?php endif; ?>

                <?php if (has_flash('error')): ?>
                    <div class="alert alert-danger" role="alert">
                        <div class="alert-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="8" x2="12" y2="12"></line>
                                <line x1="12" y1="16" x2="12.01" y2="16"></line>
                            </svg>
                        </div>
                        <div class="alert-message"><?= htmlspecialchars(flash('error')) ?></div>
                        <button type="button" class="alert-close-btn" onclick="this.parentElement.remove()">&times;</button>
                    </div>
                <?php endif; ?>

                <!-- View Specific Content -->
                <?= $content ?>
            </div>

            <!-- Footer -->
            <?php include __DIR__ . '/footer.php'; ?>
        </div>
    </div>

    <!-- Backdrop for mobile sidebar -->
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <script src="/assets/js/app.js"></script>
</body>
</html>
