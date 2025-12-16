<?php
require_once 'config.php';
$pageTitle = 'Contact Us';
include 'header.php';

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
    $subject = isset($_POST['subject']) ? trim($_POST['subject']) : '';
    $message_text = isset($_POST['message']) ? trim($_POST['message']) : '';
    
    // Validation
    if (empty($name) || empty($email) || empty($message_text)) {
        $message = 'Please fill in all required fields.';
        $messageType = 'error';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Please enter a valid email address.';
        $messageType = 'error';
    } else {
        // In a real application, you would send an email or save to database
        // For now, we'll just show a success message
        $to = SITE_EMAIL;
        $email_subject = "Contact Form: " . $subject;
        $email_body = "Name: $name\n";
        $email_body .= "Email: $email\n";
        $email_body .= "Phone: $phone\n\n";
        $email_body .= "Message:\n$message_text";
        $headers = "From: $email\r\nReply-To: $email";
        
        // Uncomment the line below to actually send emails (requires mail server configuration)
        // mail($to, $email_subject, $email_body, $headers);
        
        $message = 'Thank you for contacting us! We will get back to you soon.';
        $messageType = 'success';
        
        // Clear form
        $name = $email = $phone = $subject = $message_text = '';
    }
}
?>

<section class="page-header">
    <div class="container">
        <h1>Contact Us</h1>
        <p>We'd love to hear from you. Get in touch with us today!</p>
    </div>
</section>

<section class="contact-content">
    <div class="container">
        <div class="contact-wrapper">
            <div class="contact-info">
                <h2>Get in Touch</h2>
                <p>Have a question or want to work together? We're here to help!</p>
                
                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div class="info-text">
                        <h4>Address</h4>
                        <p><a href="<?php echo defined('SITE_GOOGLE_MAPS') ? SITE_GOOGLE_MAPS : 'https://www.google.com/maps/search/?api=1&query=' . urlencode(SITE_ADDRESS); ?>" target="_blank" rel="noopener noreferrer"><?php echo SITE_ADDRESS; ?></a></p>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-phone"></i>
                    </div>
                    <div class="info-text">
                        <h4>Phone</h4>
                        <p><?php echo SITE_PHONE; ?></p>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div class="info-text">
                        <h4>Email</h4>
                        <p><a href="mailto:<?php echo SITE_EMAIL; ?>"><?php echo SITE_EMAIL; ?></a></p>
                    </div>
                </div>
                
                <?php if (defined('SITE_WEBSITE')): ?>
                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-globe"></i>
                    </div>
                    <div class="info-text">
                        <h4>Website</h4>
                        <p><a href="http://<?php echo SITE_WEBSITE; ?>" target="_blank"><?php echo SITE_WEBSITE; ?></a></p>
                    </div>
                </div>
                <?php endif; ?>
                
                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="info-text">
                        <h4>Business Hours</h4>
                        <p>Monday - Friday: 9:00 AM - 6:00 PM<br>Saturday: 10:00 AM - 4:00 PM</p>
                    </div>
                </div>
            </div>
            
            <div class="contact-form-wrapper">
                <h2>Send us a Message</h2>
                <?php if ($message): ?>
                    <div class="alert alert-<?php echo $messageType; ?>">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="contact.php" class="contact-form">
                    <div class="form-group">
                        <label for="name">Name <span class="required">*</span></label>
                        <input type="text" id="name" name="name" required value="<?php echo isset($name) ? htmlspecialchars($name) : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email <span class="required">*</span></label>
                        <input type="email" id="email" name="email" required value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">Phone</label>
                        <input type="tel" id="phone" name="phone" value="<?php echo isset($phone) ? htmlspecialchars($phone) : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="subject">Subject</label>
                        <input type="text" id="subject" name="subject" value="<?php echo isset($subject) ? htmlspecialchars($subject) : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="message">Message <span class="required">*</span></label>
                        <textarea id="message" name="message" rows="5" required><?php echo isset($message_text) ? htmlspecialchars($message_text) : ''; ?></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-large">Send Message</button>
                </form>
            </div>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>

