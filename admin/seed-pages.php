<?php
require_once '../config/config.php';
require_once '../includes/Page.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

$pageModel = new Page();
$success = 0;
$errors = [];

// Define the pages to seed
$pages = [
    [
        'title' => 'Privacy Policy',
        'slug' => 'privacy-policy',
        'meta_description' => 'Learn about how Stream East collects, uses, and protects your personal information.',
        'content' => '<h2>1. Information We Collect</h2>
<p>When you visit Stream East, we may collect the following information:</p>
<ul>
    <li><strong>Automatically Collected Information:</strong> IP address, browser type, device information, pages visited, and time spent on our website.</li>
    <li><strong>Cookies:</strong> We use cookies to enhance your browsing experience and remember your preferences (such as theme selection).</li>
    <li><strong>Analytics:</strong> We may use third-party analytics services (like Google Analytics) to understand how visitors use our website.</li>
</ul>

<h2>2. How We Use Your Information</h2>
<p>We use the collected information for the following purposes:</p>
<ul>
    <li>To provide and improve our video streaming services</li>
    <li>To analyze website traffic and user behavior</li>
    <li>To remember your preferences (theme mode, etc.)</li>
    <li>To detect and prevent fraudulent or malicious activity</li>
    <li>To comply with legal obligations</li>
</ul>

<h2>3. Third-Party Services</h2>
<p>Our website may include embedded content from third-party services:</p>
<ul>
    <li><strong>YouTube:</strong> Videos embedded from YouTube are subject to Google\'s Privacy Policy</li>
    <li><strong>Analytics Services:</strong> We may use Google Analytics or similar services</li>
    <li><strong>Advertising:</strong> Third-party advertising partners may use cookies for targeted advertising</li>
</ul>
<p>These third parties have their own privacy policies, and we encourage you to review them.</p>

<h2>4. Cookies and Tracking</h2>
<p>We use cookies and similar tracking technologies to:</p>
<ul>
    <li>Remember your theme preference (dark/light mode)</li>
    <li>Track anonymous usage statistics</li>
    <li>Improve website functionality</li>
</ul>
<p>You can control cookie settings through your browser. Disabling cookies may affect website functionality.</p>

<h2>5. Data Security</h2>
<p>We implement reasonable security measures to protect your information from unauthorized access, alteration, or destruction. However, no internet transmission is 100% secure, and we cannot guarantee absolute security.</p>

<h2>6. Children\'s Privacy</h2>
<p>Our website is not intended for children under 13 years of age. We do not knowingly collect personal information from children under 13.</p>

<h2>7. Your Rights</h2>
<p>Depending on your location, you may have certain rights regarding your personal information:</p>
<ul>
    <li>Right to access your personal data</li>
    <li>Right to request deletion of your data</li>
    <li>Right to object to data processing</li>
    <li>Right to data portability</li>
</ul>

<h2>8. Contact Us</h2>
<p>If you have questions about this Privacy Policy, please contact us at <a href="/contact.php">our contact page</a>.</p>'
    ],
    [
        'title' => 'Terms of Service',
        'slug' => 'terms',
        'meta_description' => 'Read our terms of service governing your use of Stream East video streaming platform.',
        'content' => '<h2>1. Acceptance of Terms</h2>
<p>By accessing and using Stream East, you accept and agree to be bound by these Terms of Service. If you do not agree to these terms, please do not use our website.</p>

<h2>2. Description of Service</h2>
<p>Stream East provides a platform for streaming and viewing video content, including embedded YouTube videos, video organization by categories and tags, search and discovery features, and blog content.</p>

<h2>3. User Conduct</h2>
<p>You agree to use our website only for lawful purposes. You must not:</p>
<ul>
    <li>Use the website in any way that violates applicable laws or regulations</li>
    <li>Attempt to gain unauthorized access to our systems or data</li>
    <li>Interfere with or disrupt the website or servers</li>
    <li>Use automated tools (bots, scrapers) without permission</li>
    <li>Impersonate any person or entity</li>
    <li>Upload malicious code or viruses</li>
</ul>

<h2>4. Intellectual Property</h2>
<p>All content on Stream East, including but not limited to text, graphics, logos, and software, is protected by copyright and other intellectual property laws.</p>

<h2>5. Third-Party Content</h2>
<p>Our website contains embedded content from third-party sources (primarily YouTube). We do not host or store video files on our servers, claim ownership of embedded third-party content, or control the availability of third-party content.</p>

<h2>6. Disclaimer of Warranties</h2>
<p>Our website is provided "as is" without warranties of any kind, either express or implied.</p>

<h2>7. Limitation of Liability</h2>
<p>To the maximum extent permitted by law, Stream East shall not be liable for any indirect, incidental, special, consequential, or punitive damages.</p>

<h2>8. Contact Information</h2>
<p>For questions about these Terms of Service, please <a href="/contact.php">contact us</a>.</p>'
    ],
    [
        'title' => 'DMCA Policy',
        'slug' => 'dmca',
        'meta_description' => 'Our DMCA copyright policy and procedures for reporting copyright infringement.',
        'content' => '<h2>1. Overview</h2>
<p>Stream East respects the intellectual property rights of others and expects our users to do the same. We comply with the Digital Millennium Copyright Act (DMCA).</p>

<h2>2. Important Notice</h2>
<div class="notice-box">
    <p><strong>⚠️ Please Note:</strong> Stream East does not host any video files on our servers. We only embed videos from third-party platforms (primarily YouTube). If you believe content on YouTube or another platform infringes your copyright, please contact that platform directly.</p>
</div>

<h2>3. Filing a DMCA Takedown Notice</h2>
<p>If you believe content on our website (such as blog posts, thumbnails, or descriptions) infringes your copyright, you may submit a DMCA takedown notice with the following information:</p>
<ol>
    <li>Identification of the copyrighted work</li>
    <li>Identification of the infringing material with URL</li>
    <li>Your contact information</li>
    <li>Good faith statement</li>
    <li>Accuracy statement under penalty of perjury</li>
    <li>Physical or electronic signature</li>
</ol>

<h2>4. How to Submit a Notice</h2>
<p>Send your DMCA takedown notice via our <a href="/contact.php">contact form</a> (select "DMCA/Copyright Issue").</p>

<h2>5. Counter-Notice</h2>
<p>If you believe material you posted was removed by mistake or misidentification, you may file a counter-notice.</p>

<h2>6. False Claims</h2>
<p><strong>Warning:</strong> Submitting false or misleading DMCA notices may result in legal consequences.</p>'
    ],
    [
        'title' => 'Contact Us',
        'slug' => 'contact',
        'meta_description' => 'Get in touch with Stream East. Send us your questions, feedback, or support requests.',
        'content' => '<p class="page-description">Have questions, feedback, or need assistance? We\'d love to hear from you!</p>

<div class="contact-wrapper">
    <div class="contact-form-section">
        <h2>Send Us a Message</h2>
        <form method="POST" class="contact-form">
            <div class="form-group">
                <label for="name">Your Name *</label>
                <input type="text" id="name" name="name" required placeholder="John Doe">
            </div>
            
            <div class="form-group">
                <label for="email">Email Address *</label>
                <input type="email" id="email" name="email" required placeholder="john@example.com">
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
                <textarea id="message" name="message" required rows="6" placeholder="Tell us how we can help you..."></textarea>
            </div>
            
            <button type="submit" class="btn btn-primary btn-submit">📧 Send Message</button>
        </form>
    </div>
    
    <div class="contact-info-section">
        <h2>Other Ways to Reach Us</h2>
        
        <div class="contact-info-card">
            <div class="contact-icon">📧</div>
            <h3>Email</h3>
            <p>General Inquiries:<br><a href="mailto:info@streameast.com">info@streameast.com</a></p>
        </div>
        
        <div class="contact-info-card">
            <div class="contact-icon">⚖️</div>
            <h3>Legal & DMCA</h3>
            <p>Copyright Issues:<br><a href="mailto:dmca@streameast.com">dmca@streameast.com</a></p>
        </div>
        
        <div class="contact-info-card">
            <div class="contact-icon">⏱️</div>
            <h3>Response Time</h3>
            <p>We typically respond within:<br><strong>24-48 hours</strong> (business days)</p>
        </div>
    </div>
</div>

<div class="faq-section">
    <h2>Frequently Asked Questions</h2>
    <div class="faq-grid">
        <div class="faq-item">
            <h3>📹 How do I request a video?</h3>
            <p>Use the contact form above and select "Content Request" as the subject.</p>
        </div>
        
        <div class="faq-item">
            <h3>🔒 Is my information secure?</h3>
            <p>Yes! We take privacy seriously. Read our <a href="/privacy-policy.php">Privacy Policy</a> for more information.</p>
        </div>
        
        <div class="faq-item">
            <h3>⚖️ How do I report copyright infringement?</h3>
            <p>Please review our <a href="/dmca.php">DMCA Policy</a> and submit a formal takedown notice.</p>
        </div>
        
        <div class="faq-item">
            <h3>💼 Do you accept advertising?</h3>
            <p>Yes! Contact us for advertising and partnership opportunities.</p>
        </div>
    </div>
</div>'
    ]
];

foreach ($pages as $pageData) {
    // Check if page already exists
    if (!$pageModel->slugExists($pageData['slug'])) {
        if ($pageModel->create($pageData)) {
            $success++;
        } else {
            $errors[] = "Failed to create: " . $pageData['title'];
        }
    }
}

$pageTitle = 'Seed Pages';
include 'views/header.php';
?>

<div class="admin-content">
    <div class="content-header">
        <h1>📋 Seed Pages</h1>
        <a href="pages.php" class="btn btn-primary">View All Pages</a>
    </div>

    <div class="card">
        <div class="card-body">
            <?php if ($success > 0): ?>
                <div class="alert alert-success">
                    ✅ Successfully created <?= $success ?> page(s) in the database!
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    ℹ️ All pages already exist in the database.
                </div>
            <?php endif; ?>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-error">
                    <?php foreach ($errors as $error): ?>
                        <p>❌ <?= htmlspecialchars($error) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <p>This script adds the default legal pages (Privacy Policy, Terms of Service, DMCA, Contact) to your database so you can manage them from the Pages section.</p>
            
            <p><strong>Next steps:</strong></p>
            <ol>
                <li>Go to <a href="pages.php">Pages</a> to view and edit your pages</li>
                <li>Customize the content to match your needs</li>
                <li>Add new pages as needed</li>
            </ol>
        </div>
    </div>
</div>

<?php include 'views/footer.php'; ?>
