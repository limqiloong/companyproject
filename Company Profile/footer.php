    </main>
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <div class="footer-logo">
                        <?php 
                        $logoPath = '';
                        if (file_exists('assets/images/Logo.jpg')) {
                            $logoPath = 'assets/images/Logo.jpg';
                        } elseif (file_exists('assets/images/logo.jpg')) {
                            $logoPath = 'assets/images/logo.jpg';
                        } elseif (file_exists('assets/images/logo.png')) {
                            $logoPath = 'assets/images/logo.png';
                        }
                        if ($logoPath): ?>
                            <img src="<?php echo $logoPath; ?>" alt="<?php echo SITE_NAME; ?>" class="footer-logo-img">
                        <?php endif; ?>
                    </div>
                    <h3><?php echo SITE_NAME; ?></h3>
                    <p>Building excellence, one project at a time. Your trusted partner for quality solutions.</p>
                    <div class="social-links">
                        <?php if (defined('SITE_FACEBOOK') && SITE_FACEBOOK): ?>
                        <a href="<?php echo SITE_FACEBOOK; ?>" target="_blank" rel="noopener noreferrer" aria-label="Facebook"><i class="fab fa-facebook"></i></a>
                        <?php endif; ?>
                        <?php if (defined('SITE_TWITTER') && SITE_TWITTER): ?>
                        <a href="<?php echo SITE_TWITTER; ?>" target="_blank" rel="noopener noreferrer" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                        <?php endif; ?>
                        <?php if (defined('SITE_LINKEDIN') && SITE_LINKEDIN): ?>
                        <a href="<?php echo SITE_LINKEDIN; ?>" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn"><i class="fab fa-linkedin"></i></a>
                        <?php endif; ?>
                        <?php if (defined('SITE_INSTAGRAM') && SITE_INSTAGRAM): ?>
                        <a href="<?php echo SITE_INSTAGRAM; ?>" target="_blank" rel="noopener noreferrer" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="footer-section">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="index.php">Home</a></li>
                        <li><a href="about.php">About Us</a></li>
                        <li><a href="services.php">Services</a></li>
                        <li><a href="contact.php">Contact</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Contact Info</h4>
                    <ul class="contact-info">
                        <li><i class="fas fa-phone"></i> <?php echo SITE_PHONE; ?></li>
                        <li><i class="fas fa-envelope"></i> <a href="mailto:<?php echo SITE_EMAIL; ?>"><?php echo SITE_EMAIL; ?></a></li>
                        <li><i class="fas fa-map-marker-alt"></i> <a href="<?php echo defined('SITE_GOOGLE_MAPS') ? SITE_GOOGLE_MAPS : 'https://www.google.com/maps/search/?api=1&query=' . urlencode(SITE_ADDRESS); ?>" target="_blank" rel="noopener noreferrer"><?php echo SITE_ADDRESS; ?></a></li>
                        <?php if (defined('SITE_WEBSITE')): ?>
                        <li><i class="fas fa-globe"></i> <a href="http://<?php echo SITE_WEBSITE; ?>" target="_blank"><?php echo SITE_WEBSITE; ?></a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All rights reserved.</p>
            </div>
        </div>
    </footer>
    <script src="assets/js/script.js"></script>
</body>
</html>

