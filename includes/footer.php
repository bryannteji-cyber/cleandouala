</main>
    <footer class="main-footer">
        <div class="container">
            <p><?php echo t('site_name'); ?> &copy; <?php echo date('Y'); ?> — <?php echo t('footer_text'); ?></p>
            <p class="small"><?php echo t('built_with'); ?></p>
        </div>
    </footer>

    <!-- Floating Report Button (always visible) -->
    <?php if (basename($_SERVER['PHP_SELF']) !== 'report.php'): ?>
    <a href="report.php" class="floating-report-btn" title="<?php echo t('report_now'); ?>">
        📢
    </a>
    <?php endif; ?>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    <!-- Our JS -->
    <script src="js/main.js"></script>
</body>
</html>
