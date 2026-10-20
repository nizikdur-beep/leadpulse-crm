<?php
$currentUri = $_SERVER['REQUEST_URI'] ?? '/';
function isActiveNav(string $path, string $currentUri): string {
    if ($path === '/dashboard' && ($currentUri === '/dashboard' || $currentUri === '/')) {
        return 'active';
    }
    if ($path !== '/dashboard' && str_starts_with($currentUri, $path)) {
        return 'active';
    }
    return '';
}
$currentUser = \App\Core\Auth::user();
?>
<aside class="app-sidebar" id="appSidebar">
    <div class="sidebar-brand">
        <a href="/dashboard" class="brand-link">
            <div class="brand-logo">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                </svg>
            </div>
            <div class="brand-text">
                <span class="brand-name">LeadPulse</span>
                <span class="brand-tag">CRM</span>
            </div>
        </a>
        <button type="button" class="sidebar-toggle-btn mobile-only" id="sidebarCloseBtn" aria-label="Menyuni yopish">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </button>
    </div>

    <div class="sidebar-menu-wrapper">
        <div class="menu-section-label">Asosiy menyu</div>
        <ul class="sidebar-menu">
            <li class="menu-item <?= isActiveNav('/dashboard', $currentUri) ?>">
                <a href="/dashboard" class="menu-link">
                    <span class="menu-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="3" width="7" height="9"></rect>
                            <rect x="14" y="3" width="7" height="5"></rect>
                            <rect x="14" y="12" width="7" height="9"></rect>
                            <rect x="3" y="16" width="7" height="5"></rect>
                        </svg>
                    </span>
                    <span class="menu-title">Bosh sahifa</span>
                </a>
            </li>

            <li class="menu-item <?= isActiveNav('/clients', $currentUri) ?>">
                <a href="/clients" class="menu-link">
                    <span class="menu-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                    </span>
                    <span class="menu-title">Mijozlar</span>
                </a>
            </li>

            <li class="menu-item <?= isActiveNav('/leads', $currentUri) ?>">
                <a href="/leads" class="menu-link">
                    <span class="menu-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                        </svg>
                    </span>
                    <span class="menu-title">Bitimlar (Leads)</span>
                </a>
            </li>

            <li class="menu-item <?= isActiveNav('/tasks', $currentUri) ?>">
                <a href="/tasks" class="menu-link">
                    <span class="menu-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 11l3 3L22 4"></path>
                            <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                        </svg>
                    </span>
                    <span class="menu-title">Vazifalar</span>
                </a>
            </li>

            <li class="menu-item <?= isActiveNav('/calendar', $currentUri) ?>">
                <a href="/calendar" class="menu-link">
                    <span class="menu-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                    </span>
                    <span class="menu-title">Kalendar</span>
                </a>
            </li>

            <li class="menu-item <?= isActiveNav('/activities', $currentUri) ?>">
                <a href="/activities" class="menu-link">
                    <span class="menu-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                        </svg>
                    </span>
                    <span class="menu-title">Faoliyatlar</span>
                </a>
            </li>

            <li class="menu-item <?= isActiveNav('/reports', $currentUri) ?>">
                <a href="/reports" class="menu-link">
                    <span class="menu-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="18" y1="20" x2="18" y2="10"></line>
                            <line x1="12" y1="20" x2="12" y2="4"></line>
                            <line x1="6" y1="20" x2="6" y2="14"></line>
                        </svg>
                    </span>
                    <span class="menu-title">Hisobotlar</span>
                </a>
            </li>

            <?php if (\App\Core\Auth::isAdmin()): ?>
            <div class="menu-section-label">Maʼmuriyat</div>
            <li class="menu-item <?= isActiveNav('/users', $currentUri) ?>">
                <a href="/users" class="menu-link">
                    <span class="menu-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <line x1="19" y1="8" x2="19" y2="14"></line>
                            <line x1="22" y1="11" x2="16" y2="11"></line>
                        </svg>
                    </span>
                    <span class="menu-title">Foydalanuvchilar</span>
                </a>
            </li>
            <?php endif; ?>
        </ul>
    </div>

    <div class="sidebar-footer">
        <div class="user-profile-badge">
            <div class="user-avatar">
                <?= strtoupper(substr($currentUser['name'] ?? 'U', 0, 1)) ?>
            </div>
            <div class="user-meta">
                <span class="user-name"><?= htmlspecialchars($currentUser['name'] ?? 'Foydalanuvchi') ?></span>
                <span class="user-role badge-role-<?= htmlspecialchars($currentUser['role'] ?? 'manager') ?>">
                    <?= ($currentUser['role'] ?? 'manager') === 'admin' ? 'Administrator' : 'Menejer' ?>
                </span>
            </div>
        </div>
        <form action="/logout" method="POST" class="logout-form">
            <?= csrf_field() ?>
            <button type="submit" class="logout-btn" title="Tizimdan chiqish">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                    <polyline points="16 17 21 12 16 7"></polyline>
                    <line x1="21" y1="12" x2="9" y2="12"></line>
                </svg>
            </button>
        </form>
    </div>
</aside>
