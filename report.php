<?php
require_once 'config/db.php';
require_once 'includes/lang.php';

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['type'] ?? '';
    $description = trim($_POST['description'] ?? '');
    $latitude = $_POST['latitude'] ?? '';
    $longitude = $_POST['longitude'] ?? '';
    $reporter_name = trim($_POST['reporter_name'] ?? 'Anonymous');
    $reporter_phone = trim($_POST['reporter_phone'] ?? '');

    if (empty($type) || empty($latitude) || empty($longitude)) {
        $message = t('report_error');
        $messageType = 'error';
    } else {
        $photoPath = null;

        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $allowed = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
            if (in_array($_FILES['photo']['type'], $allowed)) {
                $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
                $newName = 'report_' . time() . '_' . rand(1000,9999) . '.' . $ext;
                $uploadDir = 'uploads/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                $destination = $uploadDir . $newName;
                if (move_uploaded_file($_FILES['photo']['tmp_name'], $destination)) {
                    $photoPath = $destination;
                }
            }
        }

        try {
            $stmt = $pdo->prepare("INSERT INTO reports (type, description, latitude, longitude, photo, reporter_name, reporter_phone) 
                                   VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $type,
                $description,
                $latitude,
                $longitude,
                $photoPath,
                $reporter_name ?: 'Anonymous',
                $reporter_phone
            ]);

            $message = t('report_success');
            $messageType = 'success';
        } catch (Exception $e) {
            $message = t('save_error');
            $messageType = 'error';
        }
    }
}

$pageTitle = t('report_title');
include 'includes/header.php';
?>

<div class="container">
    <h1 class="page-title"><?php echo t('report_title'); ?></h1>
    <p class="page-subtitle"><?php echo t('report_subtitle'); ?></p>

    <?php if ($message): ?>
        <div class="alert alert-<?php echo $messageType; ?>">
            <?php echo $message; ?>
            <?php if ($messageType === 'success'): ?>
                <br><a href="index.php" style="color:inherit;font-weight:700;"><?php echo t('view_on_map'); ?></a>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="type"><?php echo t('type_problem'); ?></label>
                <select name="type" id="type" required>
                    <option value=""><?php echo t('select'); ?></option>
                    <option value="dump"><?php echo t('type_dump'); ?></option>
                    <option value="overflowing_bin"><?php echo t('type_bin'); ?></option>
                    <option value="clogged_drain"><?php echo t('type_drain'); ?></option>
                    <option value="flood_risk"><?php echo t('type_flood'); ?></option>
                    <option value="other"><?php echo t('type_other'); ?></option>
                </select>
            </div>

            <div class="form-group">
                <label for="description"><?php echo t('description'); ?></label>
                <textarea name="description" id="description" placeholder="<?php echo t('desc_placeholder'); ?>"></textarea>
            </div>

            <div class="form-group">
                <label><?php echo t('location'); ?></label>
                <div style="display:flex; gap:10px; flex-wrap:wrap;">
                    <input type="text" name="latitude" id="latitude" placeholder="<?php echo t('latitude'); ?>" required style="flex:1;">
                    <input type="text" name="longitude" id="longitude" placeholder="<?php echo t('longitude'); ?>" required style="flex:1;">
                </div>
                <button type="button" id="getLocationBtn" class="btn btn-location"><?php echo t('use_location'); ?></button>
                <p style="font-size:0.8rem; color:#666; margin-top:6px;"><?php echo t('location_help'); ?></p>
            </div>

            <div class="form-group">
                <label for="photo"><?php echo t('photo'); ?></label>
                <input type="file" name="photo" id="photo" accept="image/*">
            </div>

            <div class="form-group">
                <label for="reporter_name"><?php echo t('your_name'); ?></label>
                <input type="text" name="reporter_name" id="reporter_name" placeholder="<?php echo t('anonymous'); ?>">
            </div>

            <div class="form-group">
                <label for="reporter_phone"><?php echo t('phone'); ?></label>
                <input type="tel" name="reporter_phone" id="reporter_phone" placeholder="6XX XXX XXX">
            </div>

            <button type="submit" class="btn btn-block"><?php echo t('submit_report'); ?></button>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
