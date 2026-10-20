<?php
$currentUser = \App\Core\Auth::user();
?>
<header class="app-header">
    <div class="header-left">
        <button type="button" class="sidebar-toggle-btn" id="sidebarToggleBtn" aria-label="Menyuni ochish/yopish">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="3" y1="12" x2="21" y2="12"></line>
                <line x1="3" y1="6" x2="21" y2="6"></line>
                <line x1="3" y1="18" x2="21" y2="18"></line>
            </svg>
        </button>
        <div class="header-page-info">
            <h1 class="page-main-heading"><?= htmlspecialchars($pageTitle ?? 'Bosh sahifa') ?></h1>
        </div>
    </div>

    <div class="header-right">
        <!-- Quick Action Dropdown -->
        <div class="quick-add-group">
            <a href="/leads/create" class="btn btn-primary btn-sm">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                <span>Yangi bitim</span>
            </a>
            <a href="/clients/create" class="btn btn-outline btn-sm">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="8.5" cy="7" r="4"></circle>
                    <line x1="20" y1="8" x2="20" y2="14"></line>
                    <line x1="23" y1="11" x2="17" y2="11"></line>
                </svg>
                <span>Mijoz</span>
            </a>
            <a href="/tasks/create" class="btn btn-outline btn-sm">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 5v14M5 12h14"></path>
                </svg>
                <span>Vazifa</span>
            </a>
        </div>

        <div class="header-user-status">
            <span class="status-indicator online" title="Tizim faol"></span>
            <span class="user-display-name"><?= htmlspecialchars($currentUser['name'] ?? '') ?></span>
        </div>
    </div>
</header>
