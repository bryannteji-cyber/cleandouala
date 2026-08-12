<?php
// Language system
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


// Default language = French (Douala)
if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = 'fr';
}

// Change language if requested
if (isset($_GET['lang']) && in_array($_GET['lang'], ['fr', 'en'])) {
    $_SESSION['lang'] = $_GET['lang'];
}

// Load the correct language file
$langFile = __DIR__ . '/../lang/' . $_SESSION['lang'] . '.php';
if (file_exists($langFile)) {
    require $langFile;
} else {
    require __DIR__ . '/../lang/fr.php';
}

// Helper function
if (!function_exists('t')) {
    function t($key) {
        global $lang;
        return $lang[$key] ?? $key;
    }
}
?>

