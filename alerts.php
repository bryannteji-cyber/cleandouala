<?php
require_once 'config/db.php';
require_once 'includes/lang.php';

// Get drain and flood related reports
$stmt = $pdo->query("SELECT * FROM reports 
                     WHERE type IN ('clogged_drain', 'flood_risk') 
                     ORDER BY created_at DESC");
$drainReports = $stmt->fetchAll();

// Count high risk areas
$highRiskCount = 0;
foreach ($drainReports as $r) {
    if ($r['status'] === 'pending') $highRiskCount++;
}

$pageTitle = t('alerts_title');
include 'includes/header.php';
?>

<div class="container">
    <h1 class="page-title"><?php echo t('alerts_title'); ?></h1>
    <p class="page-subtitle"><?php echo t('alerts_subtitle'); ?></p>

    <div class="stats-bar">
        <div class="stat-card">
            <div class="stat-number"><?php echo count($drainReports); ?></div>
            <div class="stat-label"><?php echo t('drain_flood_reports'); ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-number" style="color:#dc2626;"><?php echo $highRiskCount; ?></div>
            <div class="stat-label"><?php echo t('active_high_risk'); ?></div>
        </div>
    </div>

    <?php if ($highRiskCount > 0): ?>
        <div class="alert alert-error">
            <?php echo sprintf(t('high_risk_msg'), $highRiskCount); ?>
        </div>
    <?php else: ?>
        <div class="alert alert-success">
            <?php echo t('no_risk_msg'); ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <h2 style="margin-bottom:14px; color:#0f766e;"><?php echo t('current_drain_reports'); ?></h2>
        
        <?php if (empty($drainReports)): ?>
            <p><?php echo t('no_drain_reports'); ?></p>
        <?php else: ?>
            <ul class="report-list">
                <?php foreach ($drainReports as $report): ?>
                    <li class="report-item <?php echo htmlspecialchars($report['type']); ?>">
                        <?php if (!empty($report['photo']) && file_exists($report['photo'])): ?>
                            <img src="<?php echo htmlspecialchars($report['photo']); ?>" alt="Photo" class="report-photo">
                        <?php else: ?>
                            <div class="report-photo-placeholder"><?php echo t('no_photo'); ?></div>
                        <?php endif; ?>
                        
                        <div style="flex:1;">
                            <div class="report-type">
                                <?php echo $report['type'] === 'clogged_drain' ? t('type_drain') : t('type_flood'); ?>
                            </div>
                            <div class="report-desc"><?php echo htmlspecialchars($report['description'] ?: '-'); ?></div>
                            <div class="report-meta">
                                <?php echo t('by'); ?> <?php echo htmlspecialchars($report['reporter_name']); ?> • 
                                <?php echo date('d M Y H:i', strtotime($report['created_at'])); ?>
                            </div>
                        </div>
                        <span class="status-badge status-<?php echo $report['status']; ?>">
                            <?php echo $report['status']; ?>
                        </span>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <a href="report.php" class="btn btn-block" style="margin-top:16px;"><?php echo t('report_drain'); ?></a>
    </div>

    <div class="card" style="margin-top:20px;">
        <h3 style="color:#0f766e; margin-bottom:10px;"><?php echo t('what_to_do'); ?></h3>
        <ul style="padding-left:20px; color:#444; line-height:1.7;">
            <li><?php echo t('tip1'); ?></li>
            <li><?php echo t('tip2'); ?></li>
            <li><?php echo t('tip3'); ?></li>
            <li><?php echo t('tip4'); ?></li>
        </ul>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
