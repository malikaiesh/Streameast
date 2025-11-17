        </main>
    </div>
    
    <footer class="main-footer">
        <div class="footer-content">
            <p>&copy; <?= date('Y') ?> <?= Security::output((new Settings())->get('site_name', 'YouTube Clone')) ?>. All rights reserved.</p>
            <!-- Custom Footer Code -->
            <?php echo (new Settings())->getCustomCode('footer'); ?>
        </div>
    </footer>
    
    <!-- Custom Body Bottom Code -->
    <?php echo (new Settings())->getCustomCode('body_bottom'); ?>
    
    <script src="<?= SITE_URL ?>/assets/js/main.js"></script>
</body>
</html>
