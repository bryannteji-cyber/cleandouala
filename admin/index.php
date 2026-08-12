<?php
require_once '../config/db.php';
require_once '../includes/lang.php';

// Simple password protection (change this password!)
$ADMIN_PASSWORD = 'cleandouala2026';

// Handle login
if (isset($_POST['password'])) {
    if ($_POST['password'] === $ADMIN_PASSWORD) {
        $_SESSION['admin_logged_in'] = true;
    } else {
        $loginError = t('wrong_password');
    }
}

// Handle logout
if (isset($_GET['logout'])) {
    unset($_SESSION['admin_logged_in']);
    header('Location: index.php');
    exit;
}

// Handle status update
if (isset($_POST['update_status']) && !empty($_SESSION['admin_logged_in'])) {
    $id = (int)$_POST['report_id'];
    $status = $_POST['status'];
    if (in_array($status, ['pending', 'in_progress', 'resolved'])) {
        $stmt = $pdo->prepare("UPDATE reports SET status = ? WHERE id = ?");
        $stmt->execute([$status, $id]);
        $statusMessage = t('status_updated');
    }
}

// Handle DELETE report
if (isset($_POST['delete_report']) && !empty($_SESSION['admin_logged_in'])) {
    $id = (int)$_POST['report_id'];
    
    // First get the photo path so we can delete the file
    $stmt = $pdo->prepare("SELECT photo FROM reports WHERE id = ?");
    $stmt->execute([$id]);
    $report = $stmt->fetch();
    
    if ($report && !empty($report['photo'])) {
        $photoFile = '../' . $report['photo'];
        if (file_exists($photoFile)) {
            unlink($photoFile); // delete the photo file
        }
    }
    
    // Delete the report from database
    $stmt = $pdo->prepare("DELETE FROM reports WHERE id = ?");
    $stmt->execute([$id]);
    $statusMessage = t('report_deleted');
}

// Handle pickup status update
if (isset($_POST['update_pickup']) && !empty($_SESSION['admin_logged_in'])) {
    $id = (int)$_POST['pickup_id'];
    $status = $_POST['status'];
    if (in_array($status, ['pending', 'accepted', 'completed', 'cancelled'])) {
        $stmt = $pdo->prepare("UPDATE pickups SET status = ? WHERE id = ?");
        $stmt->execute([$status, $id]);
        $statusMessage = t('status_updated');
    }
}


$pageTitle = t('admin_panel');

// If not logged in, show login form
if (empty($_SESSION['admin_logged_in'])):
?>
<!DOCTYPE html>
<html lang="<?php echo $_SESSION['lang']; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo t('admin_login'); ?> - CleanDouala</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container" style="max-width:420px; margin-top:80px;">
        <div class="card">
            <h1 style="text-align:center; color:#0f766e; margin-bottom:20px;"><?php echo t('admin_login'); ?></h1>
            
            <?php if (!empty($loginError)): ?>
                <div class="alert alert-error"><?php echo $loginError; ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label><?php echo t('password'); ?></label>
                    <input type="password" name="password" required autofocus>
                </div>
                <button type="submit" class="btn btn-block"><?php echo t('login'); ?></button>
            </form>
            
            <p style="text-align:center; margin-top:16px;">
                <a href="../index.php" style="color:#0d9488;">← Back to site</a>
            </p>
        </div>
    </div>
</body>
</html>
<?php
exit;
endif;

// ===== LOGGED IN ADMIN PANEL =====
$reports = $pdo->query("SELECT * FROM reports ORDER BY created_at DESC")->fetchAll();
$pickups = $pdo->query("SELECT * FROM pickups ORDER BY created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="<?php echo $_SESSION['lang']; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo t('admin_panel'); ?> - CleanDouala</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .admin-table { width:100%; border-collapse:collapse; font-size:0.9rem; }
        .admin-table th, .admin-table td { padding:10px 8px; border-bottom:1px solid #e2e8f0; text-align:left; }
        .admin-table th { background:#f0fdfa; color:#0f766e; }
        .admin-table img { width:50px; height:50px; object-fit:cover; border-radius:6px; }
        .status-form { display:flex; gap:6px; align-items:center; }
        .status-form select { padding:6px; border-radius:6px; border:1px solid #cbd5e1; }
        .status-form button { padding:6px 12px; font-size:0.85rem; }
    </style>
</head>
<body>
    <header class="main-header">
        <div class="container">
            <a href="../index.php" class="logo">
                <span class="logo-icon">🌿</span>
                <span class="logo-text">CleanDouala Admin</span>
            </a>
            <nav class="main-nav">
                <a href="?logout=1"><?php echo t('logout'); ?></a>
                <span class="lang-switch">
                    <a href="?lang=fr" class="<?php echo $_SESSION['lang'] === 'fr' ? 'active-lang' : ''; ?>">FR</a>
                    <a href="?lang=en" class="<?php echo $_SESSION['lang'] === 'en' ? 'active-lang' : ''; ?>">EN</a>
                </span>
            </nav>
        </div>
    </header>

    <main>
        <div class="container">
            <h1 class="page-title"><?php echo t('admin_panel'); ?></h1>

            <?php if (!empty($statusMessage)): ?>
                <div class="alert alert-success"><?php echo $statusMessage; ?></div>
            <?php endif; ?>

            <!-- REPORTS -->
            <div class="card">
                <h2 style="margin-bottom:14px; color:#0f766e;"><?php echo t('manage_reports'); ?> (<?php echo count($reports); ?>)</h2>
                
                <?php if (empty($reports)): ?>
                    <p>No reports yet.</p>
                <?php else: ?>
                    <div style="overflow-x:auto;">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Photo</th>
                                    <th>Type</th>
                                    <th>Description</th>
                                    <th>By</th>
                                    <th>Date</th>
                                    <th><?php echo t('status'); ?></th>
                                    <th><?php echo t('actions'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($reports as $r): ?>
                                <tr>
                                    <td><?php echo $r['id']; ?></td>
                                    <td>
                                        <?php if (!empty($r['photo']) && file_exists('../' . $r['photo'])): ?>
                                            <img src="../<?php echo htmlspecialchars($r['photo']); ?>" alt="Photo">
                                        <?php else: ?>
                                            <span style="color:#999;font-size:0.8rem;"><?php echo t('no_photo'); ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($r['type']); ?></td>
                                    <td><?php echo htmlspecialchars(mb_strimwidth($r['description'] ?? '', 0, 40, '...')); ?></td>
                                    <td><?php echo htmlspecialchars($r['reporter_name']); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($r['created_at'])); ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo $r['status']; ?>">
                                            <?php echo $r['status']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div style="display:flex; flex-direction:column; gap:6px;">
                                            <form method="POST" class="status-form">
                                                <input type="hidden" name="report_id" value="<?php echo $r['id']; ?>">
                                                <select name="status">
                                                    <option value="pending" <?php echo $r['status']==='pending'?'selected':''; ?>><?php echo t('pending_status'); ?></option>
                                                    <option value="in_progress" <?php echo $r['status']==='in_progress'?'selected':''; ?>><?php echo t('in_progress'); ?></option>
                                                    <option value="resolved" <?php echo $r['status']==='resolved'?'selected':''; ?>><?php echo t('resolved'); ?></option>
                                                </select>
                                                <button type="submit" name="update_status" class="btn" style="padding:6px 12px;font-size:0.85rem;"><?php echo t('save'); ?></button>
                                            </form>
                                            
                                            <!-- Delete button -->
                                            <form method="POST" onsubmit="return confirm('<?php echo t('delete_confirm'); ?>');">
                                                <input type="hidden" name="report_id" value="<?php echo $r['id']; ?>">
                                                <button type="submit" name="delete_report" class="btn" style="padding:6px 12px;font-size:0.85rem;background:#dc2626;width:100%;">
                                                    🗑️ <?php echo t('delete'); ?>
                                                </button>
                                            </form>
                                        </div>
                                    </td>

                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>

            <!-- PICKUPS -->
            <div class="card" style="margin-top:24px;">
                <h2 style="margin-bottom:14px; color:#0f766e;"><?php echo t('manage_pickups'); ?> (<?php echo count($pickups); ?>)</h2>
                
                <?php if (empty($pickups)): ?>
                    <p>No pickup requests yet.</p>
                <?php else: ?>
                    <div style="overflow-x:auto;">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Phone</th>
                                    <th>Address</th>
                                    <th>Waste</th>
                                    <th>Qty</th>
                                    <th>Date</th>
                                    <th><?php echo t('status'); ?></th>
                                    <th><?php echo t('actions'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pickups as $p): ?>
                                <tr>
                                    <td><?php echo $p['id']; ?></td>
                                    <td><?php echo htmlspecialchars($p['reporter_name']); ?></td>
                                    <td><?php echo htmlspecialchars($p['reporter_phone']); ?></td>
                                    <td><?php echo htmlspecialchars(mb_strimwidth($p['address'], 0, 30, '...')); ?></td>
                                    <td><?php echo htmlspecialchars($p['waste_type']); ?></td>
                                    <td><?php echo htmlspecialchars($p['quantity']); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($p['created_at'])); ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo $p['status']; ?>">
                                            <?php echo $p['status']; ?>
                                        </span>
                                    </td>
                                   <td>
    <div style="display:flex; flex-direction:column; gap:6px;">
        <form method="POST" class="status-form">
            <input type="hidden" name="pickup_id" value="<?php echo $p['id']; ?>">
            <select name="status">
                <option value="pending" <?php echo $p['status']==='pending'?'selected':''; ?>>Pending</option>
                <option value="accepted" <?php echo $p['status']==='accepted'?'selected':''; ?>>Accepted</option>
                <option value="completed" <?php echo $p['status']==='completed'?'selected':''; ?>>Completed</option>
                <option value="cancelled" <?php echo $p['status']==='cancelled'?'selected':''; ?>>Cancelled</option>
            </select>
            <button type="submit" name="update_pickup" class="btn" style="padding:6px 12px;font-size:0.85rem;"><?php echo t('save'); ?></button>
        </form>

        <!-- Delete button -->
        <form method="POST" onsubmit="return confirm('Are you sure you want to delete this pickup request?');">
            <input type="hidden" name="pickup_id" value="<?php echo $p['id']; ?>">
            <button type="submit" name="delete_pickup" class="btn" style="padding:6px 12px;font-size:0.85rem;background:#dc2626;width:100%;">
                🗑️ <?php echo t('delete'); ?>
            </button>
        </form>
    </div>
</td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <footer class="main-footer">
        <div class="container">
            <p>CleanDouala Admin</p>
        </div>
    </footer>
</body>
</html>
