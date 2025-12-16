<?php
require_once 'config.php';
$pageTitle = 'Home';
include 'header.php';
?>

<section class="hero">
    <div class="hero-content">
        <div class="container">
            <h1 class="hero-title">Welcome to <?php echo SITE_NAME; ?></h1>
            <p class="hero-subtitle">Excellence in Every Project, Innovation in Every Solution</p>
            <div class="hero-buttons">
                <a href="services.php" class="btn btn-primary">Our Services</a>
                <a href="contact.php" class="btn btn-secondary">Get in Touch</a>
            </div>
        </div>
    </div>
    <div class="hero-overlay"></div>
</section>

<section class="home-services">
    <div class="container">
        <h2 class="section-title">Our Services</h2>
        <p class="section-subtitle">Explore our core services across earthwork, mining, and timber operations</p>
        <div class="services-carousel-wrapper">
            <button class="carousel-btn carousel-btn-prev" id="servicesPrev" aria-label="Previous services">
                <i class="fas fa-chevron-left"></i>
            </button>
            <div class="services-preview-grid" id="servicesCarousel">
            <a href="services.php#earthwork" class="service-preview-card">
                <div class="service-preview-image" style="background: linear-gradient(135deg, #D4AF37 0%, #B8860B 100%);">
                    <i class="fas fa-truck"></i>
                </div>
                <div class="service-preview-content">
                    <h3>Earthwork & Infrastructure</h3>
                </div>
            </a>

            <a href="services.php#mining" class="service-preview-card">
                <div class="service-preview-image" style="background: linear-gradient(135deg, #FFD700 0%, #1a1a1a 100%);">
                    <i class="fas fa-mountain"></i>
                </div>
                <div class="service-preview-content">
                    <h3>Mining</h3>
                </div>
            </a>

            <a href="services.php#timber" class="service-preview-card">
                <div class="service-preview-image" style="background: linear-gradient(135deg, #C9A961 0%, #6b4e16 100%);">
                    <i class="fas fa-tree"></i>
                </div>
                <div class="service-preview-content">
                    <h3>Timber Logging</h3>
                </div>
            </a>
            </div>
            <button class="carousel-btn carousel-btn-next" id="servicesNext" aria-label="Next services">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
        <div class="services-view-all">
            <a href="services.php" class="btn btn-primary btn-large">View All Services</a>
        </div>
    </div>
</section>

<section class="cta-section">
    <div class="container">
        <div class="cta-content">
            <h2>Ready to Get Started?</h2>
            <p>Let's discuss how we can help bring your vision to life.</p>
            <a href="contact.php" class="btn btn-primary btn-large">Contact Us Today</a>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>

