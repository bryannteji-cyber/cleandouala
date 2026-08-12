<?php
require_once 'config/db.php';
require_once 'includes/lang.php';

// Fetch all reports for the map
$stmt = $pdo->query("SELECT * FROM reports ORDER BY created_at DESC");
$reports = $stmt->fetchAll();

// Simple stats
$total = count($reports);
$pending = 0;
$drains = 0;
foreach ($reports as $r) {
    if ($r['status'] === 'pending') $pending++;
    if ($r['type'] === 'clogged_drain' || $r['type'] === 'flood_risk') $drains++;
}

$pageTitle = t('map_title');
include 'includes/header.php';
?>

<div class="container">
    <h1 class="page-title"><?php echo t('map_title'); ?></h1>
    <p class="page-subtitle"><?php echo t('map_subtitle'); ?></p>

    <div class="stats-bar">
        <div class="stat-card">
            <div class="stat-number"><?php echo $total; ?></div>
            <div class="stat-label"><?php echo t('total_reports'); ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?php echo $pending; ?></div>
            <div class="stat-label"><?php echo t('pending'); ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?php echo $drains; ?></div>
            <div class="stat-label"><?php echo t('drain_flood'); ?></div>
        </div>
    </div>

    <div id="map"></div>

    <div class="card">
        <h2 style="margin-bottom:12px; color:#0f766e;"><?php echo t('recent_reports'); ?></h2>
        <ul class="report-list">
            <?php if (empty($reports)): ?>
                <li><?php echo t('no_reports'); ?></li>
            <?php else: ?>
                <?php foreach (array_slice($reports, 0, 8) as $report): ?>
                    <li class="report-item <?php echo htmlspecialchars($report['type']); ?>">
                        <?php if (!empty($report['photo']) && file_exists($report['photo'])): ?>
                            <img src="<?php echo htmlspecialchars($report['photo']); ?>" alt="Photo" class="report-photo">
                        <?php else: ?>
                            <div class="report-photo-placeholder"><?php echo t('no_photo'); ?></div>
                        <?php endif; ?>
                        
                        <div style="flex:1;">
                            <div class="report-type">
                                <?php
                                $labels = [
                                    'dump' => t('type_dump'),
                                    'overflowing_bin' => t('type_bin'),
                                    'clogged_drain' => t('type_drain'),
                                    'flood_risk' => t('type_flood'),
                                    'other' => t('type_other')
                                ];
                                echo $labels[$report['type']] ?? $report['type'];
                                ?>
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
            <?php endif; ?>
        </ul>
        <a href="report.php" class="btn btn-block" style="margin-top:10px;"><?php echo t('report_problem'); ?></a>
    </div>
</div>

<script>
    // Pass PHP reports to JavaScript (including photo)
    const reportsData = <?php echo json_encode($reports, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
</script>


<?php include 'includes/footer.php'; ?>
