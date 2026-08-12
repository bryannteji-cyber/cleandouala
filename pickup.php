<?php
require_once 'config/db.php';
require_once 'includes/lang.php';

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['reporter_name'] ?? '');
    $phone = trim($_POST['reporter_phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $latitude = $_POST['latitude'] ?? null;
    $longitude = $_POST['longitude'] ?? null;
    $waste_type = $_POST['waste_type'] ?? 'Mixed household';
    $quantity = $_POST['quantity'] ?? 'Medium';
    $preferred_date = $_POST['preferred_date'] ?? null;
    $preferred_time = $_POST['preferred_time'] ?? null;
    $notes = trim($_POST['notes'] ?? '');

    if (empty($name) || empty($phone) || empty($address)) {
        $message = t('pickup_error');
        $messageType = 'error';
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO pickups 
                (reporter_name, reporter_phone, address, latitude, longitude, waste_type, quantity, preferred_date, preferred_time, notes) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $name, $phone, $address, $latitude, $longitude,
                $waste_type, $quantity, $preferred_date, $preferred_time, $notes
            ]);

            $message = t('pickup_success');
            $messageType = 'success';
        } catch (Exception $e) {
            $message = t('save_error');
            $messageType = 'error';
        }
    }
}

$pageTitle = t('pickup_title');
include 'includes/header.php';
?>

<div class="container">
    <h1 class="page-title"><?php echo t('pickup_title'); ?></h1>
    <p class="page-subtitle"><?php echo t('pickup_subtitle'); ?></p>

    <?php if ($message): ?>
        <div class="alert alert-<?php echo $messageType; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <form method="POST">
            <div class="form-group">
                <label for="reporter_name"><?php echo t('full_name'); ?></label>
                <input type="text" name="reporter_name" id="reporter_name" required>
            </div>

            <div class="form-group">
                <label for="reporter_phone"><?php echo t('phone_required'); ?></label>
                <input type="tel" name="reporter_phone" id="reporter_phone" placeholder="6XX XXX XXX" required>
            </div>

            <div class="form-group">
                <label for="address"><?php echo t('address'); ?></label>
                <textarea name="address" id="address" placeholder="<?php echo t('address_placeholder'); ?>" required></textarea>
            </div>

            <div class="form-group">
                <label><?php echo t('location_optional'); ?></label>
                <div style="display:flex; gap:10px; flex-wrap:wrap;">
                    <input type="text" name="latitude" id="latitude" placeholder="<?php echo t('latitude'); ?>" style="flex:1;">
                    <input type="text" name="longitude" id="longitude" placeholder="<?php echo t('longitude'); ?>" style="flex:1;">
                </div>
                <button type="button" id="getLocationBtn" class="btn btn-location"><?php echo t('use_location'); ?></button>
            </div>

            <div class="form-group">
                <label for="waste_type"><?php echo t('waste_type'); ?></label>
                <select name="waste_type" id="waste_type">
                    <option value="Mixed household"><?php echo t('mixed'); ?></option>
                    <option value="Plastic only"><?php echo t('plastic'); ?></option>
                    <option value="Organic / Food"><?php echo t('organic'); ?></option>
                    <option value="Cardboard / Paper"><?php echo t('cardboard'); ?></option>
                    <option value="Construction debris"><?php echo t('construction'); ?></option>
                    <option value="Other"><?php echo t('type_other'); ?></option>
                </select>
            </div>

            <div class="form-group">
                <label for="quantity"><?php echo t('quantity'); ?></label>
                <select name="quantity" id="quantity">
                    <option value="Small"><?php echo t('qty_small'); ?></option>
                    <option value="Medium" selected><?php echo t('qty_medium'); ?></option>
                    <option value="Large"><?php echo t('qty_large'); ?></option>
                </select>
            </div>

            <div class="form-group">
                <label for="preferred_date"><?php echo t('preferred_date'); ?></label>
                <input type="date" name="preferred_date" id="preferred_date">
            </div>

            <div class="form-group">
                <label for="preferred_time"><?php echo t('preferred_time'); ?></label>
                <select name="preferred_time" id="preferred_time">
                    <option value=""><?php echo t('any_time'); ?></option>
                    <option value="Morning (8h-12h)"><?php echo t('morning'); ?></option>
                    <option value="Afternoon (12h-17h)"><?php echo t('afternoon'); ?></option>
                    <option value="Evening (17h-20h)"><?php echo t('evening'); ?></option>
                </select>
            </div>

            <div class="form-group">
                <label for="notes"><?php echo t('notes'); ?></label>
                <textarea name="notes" id="notes" placeholder="<?php echo t('notes_placeholder'); ?>"></textarea>
            </div>

            <button type="submit" class="btn btn-block"><?php echo t('request_pickup'); ?></button>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
