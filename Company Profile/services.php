<?php
require_once 'config.php';
$pageTitle = 'Services';
include 'header.php';
?>

<section class="page-header">
    <div class="container">
        <h1>Our Services</h1>
        <p>Comprehensive solutions tailored to your needs</p>
    </div>
</section>

<section class="services-content">
    <div class="container">
        <div class="services-intro">
            <h2>Our Investment Projects & Services</h2>
            <p><?php echo SITE_NAME; ?> is engaged in various mining, construction, and resource extraction activities. We provide comprehensive solutions in earthwork, infrastructure development, and mineral extraction services.</p>
        </div>

        <div class="services-grid">
            <!-- Earthwork, Infrastructure & Transportation -->
            <a id="earthwork" href="projects-earthwork.php" class="service-card service-card-link">
                <div class="service-icon">
                    <i class="fas fa-truck"></i>
                </div>
                <h3>Earthwork, Infrastructure & Transportation</h3>
                <p>With over 21 years of experience, we are established as a main earthwork contractor and supplier. We provide comprehensive earthwork, infrastructure construction, and transportation services, specializing in drainage systems, flood mitigation projects, and material supply.</p>
                <ul class="service-features">
                    <li>Earthwork Construction</li>
                    <li>Infrastructure Development</li>
                    <li>Drainage Systems</li>
                    <li>Flood Mitigation Projects</li>
                    <li>Land Levelling Services</li>
                    <li>Transportation & Supply Services</li>
                    <li>Armour Rock & Boulder Supply</li>
                    <li>Quarry Products Delivery</li>
                </ul>
                <div class="service-card-footer">
                    <span class="view-projects-link">View Projects <i class="fas fa-arrow-right"></i></span>
            </div>
            </a>

            <!-- Mining -->
            <a id="mining" href="projects-mining.php" class="service-card service-card-link">
                <div class="service-icon">
                    <i class="fas fa-mountain"></i>
                </div>
                <h3>Mining</h3>
                <p>We have extensive experience in various mining operations including bauxite, tin, manganese, iron mining, and quarry products. Our operations span multiple locations across Pahang with significant reserves and production capabilities.</p>
                <ul class="service-features">
                    <li><strong>Bauxite Mining:</strong> Strong track record in mining, transportation, and exporting. Annual export target: 2 Million Metric Tons</li>
                    <li><strong>Tin Mining:</strong> Located at Muadzam Shah, Pahang. Current production: 200 metric tons, 2025 target: 2,400 metric tons</li>
                    <li><strong>Manganese Mining:</strong> 100 acres at Pekan, Pahang. Estimated reserve: 200,000 metric tons. Operations commencing 2025</li>
                    <li><strong>Iron Mining:</strong> Two potential mines with 60-65% iron content. Combined reserves: 3.5-4 million metric tons</li>
                    <li><strong>Stone, Sand & Quarry Products:</strong> Quarry Dust, Armour Rock (1-1.5m), Core Rock (300-800mm), Crusher Run, Aggregates (10mm, 20mm, 25mm), Blocks (150 x 225mm), River Sand & Marine Sand</li>
                </ul>
                <div class="service-card-footer">
                    <span class="view-projects-link">View Projects <i class="fas fa-arrow-right"></i></span>
                </div>
            </a>

            <!-- Timber Logging -->
            <div id="timber" class="service-card">
                <div class="service-icon">
                    <i class="fas fa-tree"></i>
                </div>
                <h3>Timber Logging</h3>
                <p>We are engaged in timber logging activities with three concession areas totaling approximately 700 acres in the Districts of Bentong and Rompin, Pahang.</p>
                <ul class="service-features">
                    <li>3 Concession Areas</li>
                    <li>Total Area: ~700 acres</li>
                    <li>Locations: Bentong & Rompin, Pahang</li>
                    <li>Permits & Approvals in Process</li>
                    <li>Operations Expected: 2020 onwards</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<section class="cta-section">
    <div class="container">
        <div class="cta-content">
            <h2>Interested in Our Services?</h2>
            <p>Let's discuss how we can help your business grow</p>
            <a href="contact.php" class="btn btn-primary btn-large">Get a Quote</a>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>

