<?php
require_once 'config/config.php';
require_once 'includes/Settings.php';

$settings = new Settings();
$siteName = $settings->get('site_name', 'Stream East');

$pageTitle = 'Privacy Policy - ' . $siteName;
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
            <h1>Privacy Policy</h1>
            <p class="last-updated">Last Updated: <?= date('F d, Y') ?></p>
            
            <div class="legal-content">
                <section>
                    <h2>1. Information We Collect</h2>
                    <p>When you visit <?= Security::output($siteName) ?>, we may collect the following information:</p>
                    <ul>
                        <li><strong>Automatically Collected Information:</strong> IP address, browser type, device information, pages visited, and time spent on our website.</li>
                        <li><strong>Cookies:</strong> We use cookies to enhance your browsing experience and remember your preferences (such as theme selection).</li>
                        <li><strong>Analytics:</strong> We may use third-party analytics services (like Google Analytics) to understand how visitors use our website.</li>
                    </ul>
                </section>
                
                <section>
                    <h2>2. How We Use Your Information</h2>
                    <p>We use the collected information for the following purposes:</p>
                    <ul>
                        <li>To provide and improve our video streaming services</li>
                        <li>To analyze website traffic and user behavior</li>
                        <li>To remember your preferences (theme mode, etc.)</li>
                        <li>To detect and prevent fraudulent or malicious activity</li>
                        <li>To comply with legal obligations</li>
                    </ul>
                </section>
                
                <section>
                    <h2>3. Third-Party Services</h2>
                    <p>Our website may include embedded content from third-party services:</p>
                    <ul>
                        <li><strong>YouTube:</strong> Videos embedded from YouTube are subject to Google's Privacy Policy</li>
                        <li><strong>Analytics Services:</strong> We may use Google Analytics or similar services</li>
                        <li><strong>Advertising:</strong> Third-party advertising partners may use cookies for targeted advertising</li>
                    </ul>
                    <p>These third parties have their own privacy policies, and we encourage you to review them.</p>
                </section>
                
                <section>
                    <h2>4. Cookies and Tracking</h2>
                    <p>We use cookies and similar tracking technologies to:</p>
                    <ul>
                        <li>Remember your theme preference (dark/light mode)</li>
                        <li>Track anonymous usage statistics</li>
                        <li>Improve website functionality</li>
                    </ul>
                    <p>You can control cookie settings through your browser. Disabling cookies may affect website functionality.</p>
                </section>
                
                <section>
                    <h2>5. Data Security</h2>
                    <p>We implement reasonable security measures to protect your information from unauthorized access, alteration, or destruction. However, no internet transmission is 100% secure, and we cannot guarantee absolute security.</p>
                </section>
                
                <section>
                    <h2>6. Children's Privacy</h2>
                    <p>Our website is not intended for children under 13 years of age. We do not knowingly collect personal information from children under 13. If you believe we have collected information from a child under 13, please contact us immediately.</p>
                </section>
                
                <section>
                    <h2>7. Your Rights</h2>
                    <p>Depending on your location, you may have certain rights regarding your personal information:</p>
                    <ul>
                        <li>Right to access your personal data</li>
                        <li>Right to request deletion of your data</li>
                        <li>Right to object to data processing</li>
                        <li>Right to data portability</li>
                    </ul>
                </section>
                
                <section>
                    <h2>8. Changes to Privacy Policy</h2>
                    <p>We may update this Privacy Policy from time to time. Changes will be posted on this page with an updated "Last Updated" date. Continued use of our website after changes constitutes acceptance of the updated policy.</p>
                </section>
                
                <section>
                    <h2>9. Contact Us</h2>
                    <p>If you have questions about this Privacy Policy, please contact us:</p>
                    <ul>
                        <li>Email: privacy@<?= strtolower(str_replace(' ', '', $siteName)) ?>.com</li>
                        <li>Contact Page: <a href="contact.php">Contact Us</a></li>
                    </ul>
                </section>
            </div>
        </div>
    </div>
    
    <?php include 'views/footer.php'; ?>
</body>
</html>
