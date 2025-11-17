<?php
require_once 'config/config.php';
require_once 'includes/Settings.php';

$settings = Settings::getInstance();
$siteName = $settings->get('site_name', 'Stream East');

$pageTitle = 'Contact Us - ' . $siteName;
$message = '';
$error = '';

// Handle contact form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $messageText = trim($_POST['message'] ?? '');
    
    // Validation
    if (empty($name) || empty($email) || empty($subject) || empty($messageText)) {
        $error = 'Please fill in all required fields';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address';
    } else {
        // Here you could send an email or store in database
        // For now, we'll just show a success message
        $message = 'Thank you for contacting us! We will respond to your message as soon as possible.';
        
        // Clear form
        $name = $email = $subject = $messageText = '';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= Security::output($pageTitle) ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'views/header.php'; ?>
    
    <div class="legal-page">
        <div class="legal-container">
            <h1>Contact Us</h1>
            <p class="page-description">Have questions, feedback, or need assistance? We'd love to hear from you!</p>
            
            <?php if ($message): ?>
                <div class="alert alert-success">
                    ✅ <?= Security::output($message) ?>
                </div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-error">
                    ❌ <?= Security::output($error) ?>
                </div>
            <?php endif; ?>
            
            <div class="contact-wrapper">
                <div class="contact-form-section">
                    <h2>Send Us a Message</h2>
                    <form method="POST" class="contact-form">
                        <div class="form-group">
                            <label for="name">Your Name *</label>
                            <input type="text" id="name" name="name" required 
                                   value="<?= Security::output($name ?? '') ?>"
                                   placeholder="John Doe">
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email Address *</label>
                            <input type="email" id="email" name="email" required 
                                   value="<?= Security::output($email ?? '') ?>"
                                   placeholder="john@example.com">
                        </div>
                        
                        <div class="form-group">
                            <label for="subject">Subject *</label>
                            <select id="subject" name="subject" required>
                                <option value="">Select a subject</option>
                                <option value="General Inquiry">General Inquiry</option>
                                <option value="Technical Support">Technical Support</option>
                                <option value="Content Request">Content Request</option>
                                <option value="DMCA/Copyright Issue">DMCA/Copyright Issue</option>
                                <option value="Business Partnership">Business Partnership</option>
                                <option value="Feedback">Feedback</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="message">Your Message *</label>
                            <textarea id="message" name="message" required rows="6" 
                                      placeholder="Tell us how we can help you..."><?= Security::output($messageText ?? '') ?></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-submit">
                            📧 Send Message
                        </button>
                    </form>
                </div>
                
                <div class="contact-info-section">
                    <h2>Other Ways to Reach Us</h2>
                    
                    <div class="contact-info-card">
                        <div class="contact-icon">📧</div>
                        <h3>Email</h3>
                        <p>General Inquiries:<br>
                        <a href="mailto:info@<?= strtolower(str_replace(' ', '', $siteName)) ?>.com">
                            info@<?= strtolower(str_replace(' ', '', $siteName)) ?>.com
                        </a></p>
                    </div>
                    
                    <div class="contact-info-card">
                        <div class="contact-icon">⚖️</div>
                        <h3>Legal & DMCA</h3>
                        <p>Copyright Issues:<br>
                        <a href="mailto:dmca@<?= strtolower(str_replace(' ', '', $siteName)) ?>.com">
                            dmca@<?= strtolower(str_replace(' ', '', $siteName)) ?>.com
                        </a></p>
                        <p><a href="dmca.php">View DMCA Policy →</a></p>
                    </div>
                    
                    <div class="contact-info-card">
                        <div class="contact-icon">🤝</div>
                        <h3>Business Inquiries</h3>
                        <p>Partnerships & Advertising:<br>
                        <a href="mailto:business@<?= strtolower(str_replace(' ', '', $siteName)) ?>.com">
                            business@<?= strtolower(str_replace(' ', '', $siteName)) ?>.com
                        </a></p>
                    </div>
                    
                    <div class="contact-info-card">
                        <div class="contact-icon">⏱️</div>
                        <h3>Response Time</h3>
                        <p>We typically respond within:<br>
                        <strong>24-48 hours</strong> (business days)</p>
                    </div>
                </div>
            </div>
            
            <div class="faq-section">
                <h2>Frequently Asked Questions</h2>
                <div class="faq-grid">
                    <div class="faq-item">
                        <h3>📹 How do I request a video?</h3>
                        <p>Use the contact form above and select "Content Request" as the subject. Include the video URL or description in your message.</p>
                    </div>
                    
                    <div class="faq-item">
                        <h3>🔒 Is my information secure?</h3>
                        <p>Yes! We take privacy seriously. Read our <a href="privacy-policy.php">Privacy Policy</a> for more information.</p>
                    </div>
                    
                    <div class="faq-item">
                        <h3>⚖️ How do I report copyright infringement?</h3>
                        <p>Please review our <a href="dmca.php">DMCA Policy</a> and submit a formal takedown notice to dmca@<?= strtolower(str_replace(' ', '', $siteName)) ?>.com</p>
                    </div>
                    
                    <div class="faq-item">
                        <h3>💼 Do you accept advertising?</h3>
                        <p>Yes! Contact us at business@<?= strtolower(str_replace(' ', '', $siteName)) ?>.com for advertising and partnership opportunities.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php include 'views/footer.php'; ?>
</body>
</html>
