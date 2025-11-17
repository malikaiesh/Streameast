<?php
require_once 'config/config.php';
require_once 'includes/Settings.php';

$settings = Settings::getInstance();
$siteName = $settings->get('site_name', 'Stream East');

$pageTitle = 'DMCA Policy - ' . $siteName;
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
            <h1>DMCA Copyright Policy</h1>
            <p class="last-updated">Last Updated: <?= date('F d, Y') ?></p>
            
            <div class="legal-content">
                <section>
                    <h2>1. Overview</h2>
                    <p><?= Security::output($siteName) ?> respects the intellectual property rights of others and expects our users to do the same. We comply with the Digital Millennium Copyright Act (DMCA) and will respond promptly to valid copyright infringement claims.</p>
                </section>
                
                <section>
                    <h2>2. Important Notice</h2>
                    <div class="notice-box">
                        <p><strong>⚠️ Please Note:</strong> <?= Security::output($siteName) ?> does not host any video files on our servers. We only embed videos from third-party platforms (primarily YouTube). If you believe content on YouTube or another platform infringes your copyright, please contact that platform directly:</p>
                        <ul>
                            <li><strong>YouTube:</strong> <a href="https://www.youtube.com/copyright_complaint_form" target="_blank">YouTube Copyright Complaint Form</a></li>
                            <li><strong>Other Platforms:</strong> Contact the respective platform's copyright department</li>
                        </ul>
                    </div>
                </section>
                
                <section>
                    <h2>3. Filing a DMCA Takedown Notice</h2>
                    <p>If you believe content on our website (such as blog posts, thumbnails, or descriptions) infringes your copyright, you may submit a DMCA takedown notice with the following information:</p>
                    
                    <h3>Required Information:</h3>
                    <ol>
                        <li><strong>Identification of copyrighted work:</strong> Describe the copyrighted work you claim has been infringed</li>
                        <li><strong>Identification of infringing material:</strong> Provide the URL or specific location of the material on our website</li>
                        <li><strong>Your contact information:</strong> Name, address, telephone number, and email address</li>
                        <li><strong>Good faith statement:</strong> A statement that you have a good faith belief that the use is not authorized by the copyright owner, its agent, or the law</li>
                        <li><strong>Accuracy statement:</strong> A statement, under penalty of perjury, that the information in your notice is accurate and that you are the copyright owner or authorized to act on behalf of the owner</li>
                        <li><strong>Physical or electronic signature:</strong> Your physical or electronic signature</li>
                    </ol>
                </section>
                
                <section>
                    <h2>4. How to Submit a Notice</h2>
                    <p>Send your DMCA takedown notice to:</p>
                    <div class="contact-box">
                        <p><strong>Email:</strong> dmca@<?= strtolower(str_replace(' ', '', $siteName)) ?>.com</p>
                        <p><strong>Subject Line:</strong> DMCA Takedown Request</p>
                        <p><strong>Contact Form:</strong> <a href="contact.php">Contact Us</a> (select "DMCA/Copyright Issue")</p>
                    </div>
                </section>
                
                <section>
                    <h2>5. Counter-Notice</h2>
                    <p>If you believe material you posted was removed or disabled by mistake or misidentification, you may file a counter-notice with the following information:</p>
                    <ol>
                        <li>Your name, address, telephone number, and email address</li>
                        <li>Identification of the material that was removed and its location before removal</li>
                        <li>A statement under penalty of perjury that you have a good faith belief the material was removed by mistake or misidentification</li>
                        <li>A statement that you consent to the jurisdiction of the federal court in your district</li>
                        <li>Your physical or electronic signature</li>
                    </ol>
                </section>
                
                <section>
                    <h2>6. Repeat Infringers</h2>
                    <p>We have a policy of terminating, in appropriate circumstances, users or content that are repeat infringers of copyright.</p>
                </section>
                
                <section>
                    <h2>7. Response Time</h2>
                    <p>We will review and respond to valid DMCA notices within:</p>
                    <ul>
                        <li><strong>Acknowledgment:</strong> Within 24-48 hours of receipt</li>
                        <li><strong>Action:</strong> Within 5-7 business days for content removal or investigation</li>
                    </ul>
                </section>
                
                <section>
                    <h2>8. False Claims</h2>
                    <p><strong>Warning:</strong> Submitting false or misleading DMCA notices may result in legal consequences. Under the DMCA, you may be liable for damages, including costs and attorney's fees, if you knowingly materially misrepresent that material is infringing.</p>
                </section>
                
                <section>
                    <h2>9. Our Embedded Content Policy</h2>
                    <p>Since we embed videos from third-party platforms:</p>
                    <ul>
                        <li>We do not control the availability of embedded content</li>
                        <li>If a video is removed from YouTube, it will automatically disappear from our website</li>
                        <li>We will remove embeds from our database upon valid DMCA requests</li>
                        <li>Original copyright claims should be directed to the hosting platform (YouTube, etc.)</li>
                    </ul>
                </section>
                
                <section>
                    <h2>10. Contact Information</h2>
                    <p>For copyright-related inquiries:</p>
                    <ul>
                        <li><strong>DMCA Agent Email:</strong> dmca@<?= strtolower(str_replace(' ', '', $siteName)) ?>.com</li>
                        <li><strong>General Inquiries:</strong> legal@<?= strtolower(str_replace(' ', '', $siteName)) ?>.com</li>
                        <li><strong>Contact Form:</strong> <a href="contact.php">Contact Us</a></li>
                    </ul>
                </section>
            </div>
        </div>
    </div>
    
    <?php include 'views/footer.php'; ?>
</body>
</html>
