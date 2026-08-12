<?php
require_once __DIR__ . '/lang.php';
?>
<!DOCTYPE html>
<html lang="<?php echo $_SESSION['lang']; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' . t('site_name') : t('site_name'); ?></title>
    
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    
    <!-- Our CSS -->
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header class="main-header">
        <div class="container">
            <a href="index.php" class="logo">
                <span class="logo-icon">🌿</span>
                <span class="logo-text"><?php echo t('site_name'); ?></span>
            </a>
            <nav class="main-nav">
                <a href="index.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>"><?php echo t('nav_map'); ?></a>
                
                <!-- Very visible Report button in the menu -->
                <a href="report.php" class="nav-report-btn <?php echo basename($_SERVER['PHP_SELF']) == 'report.php' ? 'active' : ''; ?>">
                    📢 <?php echo t('nav_report'); ?>
                </a>
                
                <a href="pickup.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'pickup.php' ? 'active' : ''; ?>"><?php echo t('nav_pickup'); ?></a>
                <a href="alerts.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'alerts.php' ? 'active' : ''; ?>"><?php echo t('nav_alerts'); ?></a>
                <a href="admin/" class="<?php echo strpos($_SERVER['PHP_SELF'], '/admin') !== false ? 'active' : ''; ?>"><?php echo t('nav_admin'); ?></a>
                
                <!-- Language switcher --><!-- Language switcher -->
<span class="lang-switch">
    <a href="?lang=fr" class="<?php echo $_SESSION['lang'] === 'fr' ? 'active-lang' : ''; ?>">FR</a>
    <a href="?lang=en" class="<?php echo $_SESSION['lang'] === 'en' ? 'active-lang' : ''; ?>">EN</a>
</span>

<!-- Dark / Light mode button -->
<button class="theme-toggle" id="themeToggle" title="Switch theme">🌙</button>
                <span class="lang-switch">
                    <a href="?lang=fr" class="<?php echo $_SESSION['lang'] === 'fr' ? 'active-lang' : ''; ?>">FR</a>
                    <a href="?lang=en" class="<?php echo $_SESSION['lang'] === 'en' ? 'active-lang' : ''; ?>">EN</a>
                </span>
            </nav>
        </div>
    </header>

    <!-- Big visible call-to-action bar under header -->
    <?php if (basename($_SERVER['PHP_SELF']) !== 'report.php'): ?>
    <div class="report-cta-bar">
        <div class="container">
            <a href="report.php" class="btn-report-cta">
                📢 <?php echo t('report_now'); ?>
            </a>
        </div>
    </div>
    <?php endif; ?>

    <main>
