<?php
require_once '../config/config.php';
Security::requireAdmin();

$settings = new Settings();
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf_token = $_POST['csrf_token'] ?? '';
    if (Security::verifyCSRFToken($csrf_token)) {
        
        // Handle logo upload
        if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp'];
            $filename = $_FILES['logo']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            
            if (in_array($ext, $allowed)) {
                $newFilename = 'logo.' . $ext;
                $uploadPath = '../uploads/branding/' . $newFilename;
                
                if (move_uploaded_file($_FILES['logo']['tmp_name'], $uploadPath)) {
                    $settings->set('site_logo', 'uploads/branding/' . $newFilename);
                }
            } else {
                $error = 'Invalid logo file type. Allowed: JPG, PNG, GIF, SVG, WEBP';
            }
        }
        
        // Handle favicon upload
        if (isset($_FILES['favicon']) && $_FILES['favicon']['error'] === UPLOAD_ERR_OK) {
            $allowed = ['ico', 'png', 'svg'];
            $filename = $_FILES['favicon']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            
            if (in_array($ext, $allowed)) {
                $newFilename = 'favicon.' . $ext;
                $uploadPath = '../uploads/branding/' . $newFilename;
                
                if (move_uploaded_file($_FILES['favicon']['tmp_name'], $uploadPath)) {
                    $settings->set('site_favicon', 'uploads/branding/' . $newFilename);
                }
            } else {
                $error = 'Invalid favicon file type. Allowed: ICO, PNG, SVG';
            }
        }
        
        // Save general settings
        $settings->set('site_name', $_POST['site_name'] ?? '');
        $settings->set('site_description', $_POST['site_description'] ?? '');
        $settings->set('site_keywords', $_POST['site_keywords'] ?? '');
        $settings->set('theme_mode', $_POST['theme_mode'] ?? 'dark');
        $settings->set('enable_downloads', isset($_POST['enable_downloads']) ? '1' : '0');
        $settings->set('videos_per_page', $_POST['videos_per_page'] ?? '12');
        
        // Save social media links
        $settings->set('social_facebook', $_POST['social_facebook'] ?? '');
        $settings->set('social_twitter', $_POST['social_twitter'] ?? '');
        $settings->set('social_instagram', $_POST['social_instagram'] ?? '');
        $settings->set('social_youtube', $_POST['social_youtube'] ?? '');
        $settings->set('social_tiktok', $_POST['social_tiktok'] ?? '');
        $settings->set('social_linkedin', $_POST['social_linkedin'] ?? '');
        $settings->set('social_pinterest', $_POST['social_pinterest'] ?? '');
        $settings->set('social_snapchat', $_POST['social_snapchat'] ?? '');
        $settings->set('social_reddit', $_POST['social_reddit'] ?? '');
        $settings->set('social_discord', $_POST['social_discord'] ?? '');
        $settings->set('social_telegram', $_POST['social_telegram'] ?? '');
        $settings->set('social_whatsapp', $_POST['social_whatsapp'] ?? '');
        
        // Save app links
        $settings->set('app_android', $_POST['app_android'] ?? '');
        $settings->set('app_ios', $_POST['app_ios'] ?? '');
        $settings->set('app_windows', $_POST['app_windows'] ?? '');
        $settings->set('app_macos', $_POST['app_macos'] ?? '');
        
        // Save contact info
        $settings->set('contact_email', $_POST['contact_email'] ?? '');
        $settings->set('contact_phone', $_POST['contact_phone'] ?? '');
        $settings->set('contact_address', $_POST['contact_address'] ?? '');
        
        // Save tracking codes
        $settings->set('adsense_code', $_POST['adsense_code'] ?? '');
        $settings->set('google_analytics', $_POST['google_analytics'] ?? '');
        
        if (!$error) {
            $success = 'Settings saved successfully!';
        }
    }
}

$siteSettings = $settings->getAll();
$csrf_token = Security::generateCSRFToken();
include 'views/header.php';
?>

<style>
.settings-container {
    max-width: 1200px;
}

.settings-section {
    background: white;
    padding: 24px;
    margin-bottom: 24px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.settings-section h3 {
    margin: 0 0 20px 0;
    padding-bottom: 12px;
    border-bottom: 2px solid #f0f0f0;
    color: #333;
    font-size: 18px;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 20px;
}

.form-row.three-col {
    grid-template-columns: 1fr 1fr 1fr;
}

.upload-preview {
    margin-top: 10px;
    padding: 10px;
    background: #f9f9f9;
    border-radius: 4px;
}

.upload-preview img {
    max-width: 200px;
    max-height: 100px;
    display: block;
    margin-top: 8px;
}

.social-icon-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 16px;
}

.input-with-icon {
    position: relative;
}

.input-with-icon input {
    padding-left: 36px;
}

.input-icon {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 16px;
}

.help-text {
    font-size: 12px;
    color: #666;
    margin-top: 4px;
}
</style>

<div class="settings-container">
    <h2>⚙️ Site Settings</h2>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= Security::output($success) ?></div>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= Security::output($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="" enctype="multipart/form-data" class="admin-form">
        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
        
        <!-- Branding Section -->
        <div class="settings-section">
            <h3>🎨 Branding</h3>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Site Logo</label>
                    <input type="file" name="logo" accept=".jpg,.jpeg,.png,.gif,.svg,.webp">
                    <div class="help-text">Recommended: PNG or SVG, max 500KB</div>
                    <?php if (!empty($siteSettings['site_logo'])): ?>
                        <div class="upload-preview">
                            <strong>Current Logo:</strong>
                            <img src="<?= SITE_URL . '/' . Security::output($siteSettings['site_logo']) ?>" alt="Site Logo">
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label>Favicon</label>
                    <input type="file" name="favicon" accept=".ico,.png,.svg">
                    <div class="help-text">Recommended: ICO or PNG, 32x32px or 64x64px</div>
                    <?php if (!empty($siteSettings['site_favicon'])): ?>
                        <div class="upload-preview">
                            <strong>Current Favicon:</strong>
                            <img src="<?= SITE_URL . '/' . Security::output($siteSettings['site_favicon']) ?>" alt="Site Favicon">
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- General Settings Section -->
        <div class="settings-section">
            <h3>📋 General Settings</h3>
            
            <div class="form-group">
                <label>Site Name</label>
                <input type="text" name="site_name" value="<?= Security::output($siteSettings['site_name'] ?? '') ?>" placeholder="Stream East">
            </div>
            
            <div class="form-group">
                <label>Site Description</label>
                <textarea name="site_description" rows="2" placeholder="Watch trending videos, shorts, movies and live sports"><?= Security::output($siteSettings['site_description'] ?? '') ?></textarea>
            </div>
            
            <div class="form-group">
                <label>Site Keywords (comma separated)</label>
                <input type="text" name="site_keywords" value="<?= Security::output($siteSettings['site_keywords'] ?? '') ?>" placeholder="streaming, videos, sports, movies">
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Theme Mode</label>
                    <select name="theme_mode">
                        <option value="dark" <?= ($siteSettings['theme_mode'] ?? 'dark') === 'dark' ? 'selected' : '' ?>>Dark</option>
                        <option value="light" <?= ($siteSettings['theme_mode'] ?? 'dark') === 'light' ? 'selected' : '' ?>>Light</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Videos Per Page</label>
                    <input type="number" name="videos_per_page" value="<?= Security::output($siteSettings['videos_per_page'] ?? '12') ?>" min="6" max="48">
                </div>
            </div>
            
            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" name="enable_downloads" <?= ($siteSettings['enable_downloads'] ?? '0') === '1' ? 'checked' : '' ?>> Enable Download Button
                </label>
            </div>
        </div>
        
        <!-- Social Media Links Section -->
        <div class="settings-section">
            <h3>🌐 Social Media Links</h3>
            <p style="color: #666; margin-bottom: 20px;">Enter your social media profile URLs. Leave blank to hide.</p>
            
            <div class="social-icon-grid">
                <div class="form-group">
                    <label>📘 Facebook</label>
                    <div class="input-with-icon">
                        <span class="input-icon">🔗</span>
                        <input type="url" name="social_facebook" value="<?= Security::output($siteSettings['social_facebook'] ?? '') ?>" placeholder="https://facebook.com/yourpage">
                    </div>
                </div>
                
                <div class="form-group">
                    <label>🐦 Twitter / X</label>
                    <div class="input-with-icon">
                        <span class="input-icon">🔗</span>
                        <input type="url" name="social_twitter" value="<?= Security::output($siteSettings['social_twitter'] ?? '') ?>" placeholder="https://twitter.com/yourhandle">
                    </div>
                </div>
                
                <div class="form-group">
                    <label>📷 Instagram</label>
                    <div class="input-with-icon">
                        <span class="input-icon">🔗</span>
                        <input type="url" name="social_instagram" value="<?= Security::output($siteSettings['social_instagram'] ?? '') ?>" placeholder="https://instagram.com/yourprofile">
                    </div>
                </div>
                
                <div class="form-group">
                    <label>▶️ YouTube</label>
                    <div class="input-with-icon">
                        <span class="input-icon">🔗</span>
                        <input type="url" name="social_youtube" value="<?= Security::output($siteSettings['social_youtube'] ?? '') ?>" placeholder="https://youtube.com/@yourchannel">
                    </div>
                </div>
                
                <div class="form-group">
                    <label>🎵 TikTok</label>
                    <div class="input-with-icon">
                        <span class="input-icon">🔗</span>
                        <input type="url" name="social_tiktok" value="<?= Security::output($siteSettings['social_tiktok'] ?? '') ?>" placeholder="https://tiktok.com/@yourhandle">
                    </div>
                </div>
                
                <div class="form-group">
                    <label>💼 LinkedIn</label>
                    <div class="input-with-icon">
                        <span class="input-icon">🔗</span>
                        <input type="url" name="social_linkedin" value="<?= Security::output($siteSettings['social_linkedin'] ?? '') ?>" placeholder="https://linkedin.com/company/yourcompany">
                    </div>
                </div>
                
                <div class="form-group">
                    <label>📌 Pinterest</label>
                    <div class="input-with-icon">
                        <span class="input-icon">🔗</span>
                        <input type="url" name="social_pinterest" value="<?= Security::output($siteSettings['social_pinterest'] ?? '') ?>" placeholder="https://pinterest.com/yourprofile">
                    </div>
                </div>
                
                <div class="form-group">
                    <label>👻 Snapchat</label>
                    <div class="input-with-icon">
                        <span class="input-icon">🔗</span>
                        <input type="url" name="social_snapchat" value="<?= Security::output($siteSettings['social_snapchat'] ?? '') ?>" placeholder="https://snapchat.com/add/yourusername">
                    </div>
                </div>
                
                <div class="form-group">
                    <label>🤖 Reddit</label>
                    <div class="input-with-icon">
                        <span class="input-icon">🔗</span>
                        <input type="url" name="social_reddit" value="<?= Security::output($siteSettings['social_reddit'] ?? '') ?>" placeholder="https://reddit.com/r/yoursubreddit">
                    </div>
                </div>
                
                <div class="form-group">
                    <label>💬 Discord</label>
                    <div class="input-with-icon">
                        <span class="input-icon">🔗</span>
                        <input type="url" name="social_discord" value="<?= Security::output($siteSettings['social_discord'] ?? '') ?>" placeholder="https://discord.gg/yourinvite">
                    </div>
                </div>
                
                <div class="form-group">
                    <label>✈️ Telegram</label>
                    <div class="input-with-icon">
                        <span class="input-icon">🔗</span>
                        <input type="url" name="social_telegram" value="<?= Security::output($siteSettings['social_telegram'] ?? '') ?>" placeholder="https://t.me/yourchannel">
                    </div>
                </div>
                
                <div class="form-group">
                    <label>💚 WhatsApp</label>
                    <div class="input-with-icon">
                        <span class="input-icon">🔗</span>
                        <input type="url" name="social_whatsapp" value="<?= Security::output($siteSettings['social_whatsapp'] ?? '') ?>" placeholder="https://wa.me/1234567890">
                    </div>
                </div>
            </div>
        </div>
        
        <!-- App Download Links Section -->
        <div class="settings-section">
            <h3>📱 Mobile & Desktop App Links</h3>
            <p style="color: #666; margin-bottom: 20px;">Add links to your mobile and desktop apps. Leave blank to hide.</p>
            
            <div class="form-row">
                <div class="form-group">
                    <label>🤖 Android App (Google Play)</label>
                    <div class="input-with-icon">
                        <span class="input-icon">🔗</span>
                        <input type="url" name="app_android" value="<?= Security::output($siteSettings['app_android'] ?? '') ?>" placeholder="https://play.google.com/store/apps/details?id=com.yourapp">
                    </div>
                </div>
                
                <div class="form-group">
                    <label>🍎 iOS App (App Store)</label>
                    <div class="input-with-icon">
                        <span class="input-icon">🔗</span>
                        <input type="url" name="app_ios" value="<?= Security::output($siteSettings['app_ios'] ?? '') ?>" placeholder="https://apps.apple.com/app/yourapp/id123456789">
                    </div>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>🪟 Windows App</label>
                    <div class="input-with-icon">
                        <span class="input-icon">🔗</span>
                        <input type="url" name="app_windows" value="<?= Security::output($siteSettings['app_windows'] ?? '') ?>" placeholder="https://microsoft.com/store/apps/yourapp">
                    </div>
                </div>
                
                <div class="form-group">
                    <label>🍏 macOS App</label>
                    <div class="input-with-icon">
                        <span class="input-icon">🔗</span>
                        <input type="url" name="app_macos" value="<?= Security::output($siteSettings['app_macos'] ?? '') ?>" placeholder="https://apps.apple.com/app/yourapp/id123456789">
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Contact Information Section -->
        <div class="settings-section">
            <h3>📞 Contact Information</h3>
            
            <div class="form-row three-col">
                <div class="form-group">
                    <label>📧 Contact Email</label>
                    <input type="email" name="contact_email" value="<?= Security::output($siteSettings['contact_email'] ?? '') ?>" placeholder="contact@streameast.com">
                </div>
                
                <div class="form-group">
                    <label>📱 Contact Phone</label>
                    <input type="tel" name="contact_phone" value="<?= Security::output($siteSettings['contact_phone'] ?? '') ?>" placeholder="+1 (555) 123-4567">
                </div>
                
                <div class="form-group">
                    <label>📍 Address</label>
                    <input type="text" name="contact_address" value="<?= Security::output($siteSettings['contact_address'] ?? '') ?>" placeholder="123 Main St, City, Country">
                </div>
            </div>
        </div>
        
        <!-- Tracking & Analytics Section -->
        <div class="settings-section">
            <h3>📊 Tracking & Analytics</h3>
            
            <div class="form-group">
                <label>Google AdSense Code</label>
                <textarea name="adsense_code" rows="3" placeholder="Paste your AdSense code here"><?= Security::output($siteSettings['adsense_code'] ?? '') ?></textarea>
                <div class="help-text">Paste your complete AdSense script tag</div>
            </div>
            
            <div class="form-group">
                <label>Google Analytics Code</label>
                <textarea name="google_analytics" rows="3" placeholder="Paste your Google Analytics code here"><?= Security::output($siteSettings['google_analytics'] ?? '') ?></textarea>
                <div class="help-text">Paste your complete Google Analytics script tag</div>
            </div>
        </div>
        
        <button type="submit" class="btn btn-primary" style="font-size: 16px; padding: 12px 32px;">💾 Save All Settings</button>
    </form>
</div>

<?php include 'views/footer.php'; ?>
