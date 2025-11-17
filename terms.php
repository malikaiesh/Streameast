<?php
require_once 'config/config.php';
require_once 'includes/Settings.php';

$settings = Settings::getInstance();
$siteName = $settings->get('site_name', 'Stream East');

$pageTitle = 'Terms of Service - ' . $siteName;
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
            <h1>Terms of Service</h1>
            <p class="last-updated">Last Updated: <?= date('F d, Y') ?></p>
            
            <div class="legal-content">
                <section>
                    <h2>1. Acceptance of Terms</h2>
                    <p>By accessing and using <?= Security::output($siteName) ?>, you accept and agree to be bound by these Terms of Service. If you do not agree to these terms, please do not use our website.</p>
                </section>
                
                <section>
                    <h2>2. Description of Service</h2>
                    <p><?= Security::output($siteName) ?> provides a platform for streaming and viewing video content, including:</p>
                    <ul>
                        <li>Embedded YouTube videos and content</li>
                        <li>Video organization by categories and tags</li>
                        <li>Search and discovery features</li>
                        <li>Blog content and articles</li>
                    </ul>
                    <p>We reserve the right to modify, suspend, or discontinue any part of our service at any time.</p>
                </section>
                
                <section>
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
                </section>
                
                <section>
                    <h2>4. Intellectual Property</h2>
                    <p>All content on <?= Security::output($siteName) ?>, including but not limited to text, graphics, logos, and software, is protected by copyright and other intellectual property laws.</p>
                    <ul>
                        <li><strong>Embedded Content:</strong> Videos embedded from YouTube and other platforms are the property of their respective owners</li>
                        <li><strong>Website Content:</strong> Our original content (layout, design, blog posts) is owned by <?= Security::output($siteName) ?></li>
                        <li><strong>User Rights:</strong> You may view and use content for personal, non-commercial purposes only</li>
                    </ul>
                </section>
                
                <section>
                    <h2>5. Third-Party Content</h2>
                    <p>Our website contains embedded content from third-party sources (primarily YouTube). We do not:</p>
                    <ul>
                        <li>Host or store video files on our servers</li>
                        <li>Claim ownership of embedded third-party content</li>
                        <li>Control or guarantee the availability of third-party content</li>
                    </ul>
                    <p>Third-party content is subject to the terms and policies of the respective platforms (YouTube, etc.).</p>
                </section>
                
                <section>
                    <h2>6. Copyright and DMCA</h2>
                    <p>We respect intellectual property rights. If you believe content on our website infringes your copyright, please review our <a href="dmca.php">DMCA Policy</a> and submit a takedown notice.</p>
                </section>
                
                <section>
                    <h2>7. Disclaimer of Warranties</h2>
                    <p>Our website is provided "as is" without warranties of any kind, either express or implied, including but not limited to:</p>
                    <ul>
                        <li>Warranties of merchantability or fitness for a particular purpose</li>
                        <li>Accuracy, reliability, or completeness of content</li>
                        <li>Uninterrupted or error-free service</li>
                        <li>Freedom from viruses or harmful components</li>
                    </ul>
                </section>
                
                <section>
                    <h2>8. Limitation of Liability</h2>
                    <p>To the maximum extent permitted by law, <?= Security::output($siteName) ?> shall not be liable for any indirect, incidental, special, consequential, or punitive damages resulting from:</p>
                    <ul>
                        <li>Your use or inability to use the website</li>
                        <li>Unauthorized access to or alteration of your data</li>
                        <li>Third-party content or conduct</li>
                        <li>Any other matter relating to the service</li>
                    </ul>
                </section>
                
                <section>
                    <h2>9. Indemnification</h2>
                    <p>You agree to indemnify and hold harmless <?= Security::output($siteName) ?>, its officers, directors, employees, and agents from any claims, damages, losses, liabilities, and expenses arising from your use of the website or violation of these terms.</p>
                </section>
                
                <section>
                    <h2>10. Privacy</h2>
                    <p>Your use of our website is also governed by our <a href="privacy-policy.php">Privacy Policy</a>. Please review our Privacy Policy to understand our data practices.</p>
                </section>
                
                <section>
                    <h2>11. Changes to Terms</h2>
                    <p>We reserve the right to modify these Terms of Service at any time. Changes will be effective immediately upon posting. Your continued use of the website after changes constitutes acceptance of the modified terms.</p>
                </section>
                
                <section>
                    <h2>12. Governing Law</h2>
                    <p>These terms shall be governed by and construed in accordance with applicable laws, without regard to conflict of law provisions.</p>
                </section>
                
                <section>
                    <h2>13. Contact Information</h2>
                    <p>For questions about these Terms of Service, please contact us:</p>
                    <ul>
                        <li>Email: legal@<?= strtolower(str_replace(' ', '', $siteName)) ?>.com</li>
                        <li>Contact Page: <a href="contact.php">Contact Us</a></li>
                    </ul>
                </section>
            </div>
        </div>
    </div>
    
    <?php include 'views/footer.php'; ?>
</body>
</html>
