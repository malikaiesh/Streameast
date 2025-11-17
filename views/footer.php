        </main>
    </div>
    
    <footer class="main-footer">
        <div class="footer-container">
            <div class="footer-column">
                <div class="footer-brand">
                    <h3>🎬 Stream East</h3>
                    <p>Your ultimate destination for streaming videos, movies, sports, and entertainment. Watch your favorite content anytime, anywhere.</p>
                </div>
                <?php
                $settings = new Settings();
                $social_links = [
                    'facebook' => $settings->get('social_facebook'),
                    'twitter' => $settings->get('social_twitter'),
                    'instagram' => $settings->get('social_instagram'),
                    'youtube' => $settings->get('social_youtube'),
                    'tiktok' => $settings->get('social_tiktok'),
                    'linkedin' => $settings->get('social_linkedin'),
                    'discord' => $settings->get('social_discord'),
                    'telegram' => $settings->get('social_telegram'),
                ];
                $has_social = array_filter($social_links);
                
                if ($has_social):
                ?>
                <div class="social-links">
                    <h4>FOLLOW US</h4>
                    <div class="social-icons-grid">
                        <?php if ($social_links['facebook']): ?>
                        <a href="<?= Security::output($social_links['facebook']) ?>" target="_blank" rel="noopener" class="social-icon facebook" aria-label="Facebook"><svg width="22" height="22" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg></a>
                        <?php endif; ?>
                        
                        <?php if ($social_links['twitter']): ?>
                        <a href="<?= Security::output($social_links['twitter']) ?>" target="_blank" rel="noopener" class="social-icon twitter" aria-label="Twitter"><svg width="22" height="22" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg></a>
                        <?php endif; ?>
                        
                        <?php if ($social_links['instagram']): ?>
                        <a href="<?= Security::output($social_links['instagram']) ?>" target="_blank" rel="noopener" class="social-icon instagram" aria-label="Instagram"><svg width="22" height="22" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0C8.74 0 8.333.015 7.053.072 5.775.132 4.905.333 4.14.63c-.789.306-1.459.717-2.126 1.384S.935 3.35.63 4.14C.333 4.905.131 5.775.072 7.053.012 8.333 0 8.74 0 12s.015 3.667.072 4.947c.06 1.277.261 2.148.558 2.913.306.788.717 1.459 1.384 2.126.667.666 1.336 1.079 2.126 1.384.766.296 1.636.499 2.913.558C8.333 23.988 8.74 24 12 24s3.667-.015 4.947-.072c1.277-.06 2.148-.262 2.913-.558.788-.306 1.459-.718 2.126-1.384.666-.667 1.079-1.335 1.384-2.126.296-.765.499-1.636.558-2.913.06-1.28.072-1.687.072-4.947s-.015-3.667-.072-4.947c-.06-1.277-.262-2.149-.558-2.913-.306-.789-.718-1.459-1.384-2.126C21.319 1.347 20.651.935 19.86.63c-.765-.297-1.636-.499-2.913-.558C15.667.012 15.26 0 12 0zm0 2.16c3.203 0 3.585.016 4.85.071 1.17.055 1.805.249 2.227.415.562.217.96.477 1.382.896.419.42.679.819.896 1.381.164.422.36 1.057.413 2.227.057 1.266.07 1.646.07 4.85s-.015 3.585-.074 4.85c-.061 1.17-.256 1.805-.421 2.227-.224.562-.479.96-.899 1.382-.419.419-.824.679-1.38.896-.42.164-1.065.36-2.235.413-1.274.057-1.649.07-4.859.07-3.211 0-3.586-.015-4.859-.074-1.171-.061-1.816-.256-2.236-.421-.569-.224-.96-.479-1.379-.899-.421-.419-.69-.824-.9-1.38-.165-.42-.359-1.065-.42-2.235-.045-1.26-.061-1.649-.061-4.844 0-3.196.016-3.586.061-4.861.061-1.17.255-1.814.42-2.234.21-.57.479-.96.9-1.381.419-.419.81-.689 1.379-.898.42-.166 1.051-.361 2.221-.421 1.275-.045 1.65-.06 4.859-.06l.045.03zm0 3.678c-3.405 0-6.162 2.76-6.162 6.162 0 3.405 2.76 6.162 6.162 6.162 3.405 0 6.162-2.76 6.162-6.162 0-3.405-2.76-6.162-6.162-6.162zM12 16c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4zm7.846-10.405c0 .795-.646 1.44-1.44 1.44-.795 0-1.44-.646-1.44-1.44 0-.794.646-1.439 1.44-1.439.793-.001 1.44.645 1.44 1.439z"/></svg></a>
                        <?php endif; ?>
                        
                        <?php if ($social_links['youtube']): ?>
                        <a href="<?= Security::output($social_links['youtube']) ?>" target="_blank" rel="noopener" class="social-icon youtube" aria-label="YouTube"><svg width="22" height="22" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg></a>
                        <?php endif; ?>
                        
                        <?php if ($social_links['tiktok']): ?>
                        <a href="<?= Security::output($social_links['tiktok']) ?>" target="_blank" rel="noopener" class="social-icon tiktok" aria-label="TikTok"><svg width="22" height="22" fill="currentColor" viewBox="0 0 24 24"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/></svg></a>
                        <?php endif; ?>
                        
                        <?php if ($social_links['linkedin']): ?>
                        <a href="<?= Security::output($social_links['linkedin']) ?>" target="_blank" rel="noopener" class="social-icon linkedin" aria-label="LinkedIn"><svg width="22" height="22" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg></a>
                        <?php endif; ?>
                        
                        <?php if ($social_links['discord']): ?>
                        <a href="<?= Security::output($social_links['discord']) ?>" target="_blank" rel="noopener" class="social-icon discord" aria-label="Discord"><svg width="22" height="22" fill="currentColor" viewBox="0 0 24 24"><path d="M20.317 4.3698a19.7913 19.7913 0 00-4.8851-1.5152.0741.0741 0 00-.0785.0371c-.211.3753-.4447.8648-.6083 1.2495-1.8447-.2762-3.68-.2762-5.4868 0-.1636-.3933-.4058-.8742-.6177-1.2495a.077.077 0 00-.0785-.037 19.7363 19.7363 0 00-4.8852 1.515.0699.0699 0 00-.0321.0277C.5334 9.0458-.319 13.5799.0992 18.0578a.0824.0824 0 00.0312.0561c2.0528 1.5076 4.0413 2.4228 5.9929 3.0294a.0777.0777 0 00.0842-.0276c.4616-.6304.8731-1.2952 1.226-1.9942a.076.076 0 00-.0416-.1057c-.6528-.2476-1.2743-.5495-1.8722-.8923a.077.077 0 01-.0076-.1277c.1258-.0943.2517-.1923.3718-.2914a.0743.0743 0 01.0776-.0105c3.9278 1.7933 8.18 1.7933 12.0614 0a.0739.0739 0 01.0785.0095c.1202.099.246.1981.3728.2924a.077.077 0 01-.0066.1276 12.2986 12.2986 0 01-1.873.8914.0766.0766 0 00-.0407.1067c.3604.698.7719 1.3628 1.225 1.9932a.076.076 0 00.0842.0286c1.961-.6067 3.9495-1.5219 6.0023-3.0294a.077.077 0 00.0313-.0552c.5004-5.177-.8382-9.6739-3.5485-13.6604a.061.061 0 00-.0312-.0286zM8.02 15.3312c-1.1825 0-2.1569-1.0857-2.1569-2.419 0-1.3332.9555-2.4189 2.157-2.4189 1.2108 0 2.1757 1.0952 2.1568 2.419 0 1.3332-.9555 2.4189-2.1569 2.4189zm7.9748 0c-1.1825 0-2.1569-1.0857-2.1569-2.419 0-1.3332.9554-2.4189 2.1569-2.4189 1.2108 0 2.1757 1.0952 2.1568 2.419 0 1.3332-.946 2.4189-2.1568 2.4189Z"/></svg></a>
                        <?php endif; ?>
                        
                        <?php if ($social_links['telegram']): ?>
                        <a href="<?= Security::output($social_links['telegram']) ?>" target="_blank" rel="noopener" class="social-icon telegram" aria-label="Telegram"><svg width="22" height="22" fill="currentColor" viewBox="0 0 24 24"><path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/></svg></a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="footer-column">
                <h4>QUICK LINKS</h4>
                <ul class="footer-links">
                    <li><a href="/">› Home</a></li>
                    <li><a href="/trending.php">› Trending</a></li>
                    <li><a href="/shorts.php">› Shorts</a></li>
                    <li><a href="/movies.php">› Movies</a></li>
                    <li><a href="/live.php">› Live Sports</a></li>
                    <li><a href="/blog.php">› Blog</a></li>
                </ul>
            </div>
            
            <div class="footer-column">
                <h4>LEGAL</h4>
                <ul class="footer-links">
                    <li><a href="/privacy-policy.php">› Privacy Policy</a></li>
                    <li><a href="/terms.php">› Terms of Service</a></li>
                    <li><a href="/dmca.php">› DMCA</a></li>
                    <li><a href="/contact.php">› Contact Us</a></li>
                </ul>
            </div>
            
            <div class="footer-column">
                <h4>STAY UPDATED</h4>
                <p class="newsletter-text">Subscribe to get new updates and tips!</p>
                <form class="newsletter-form" action="#" method="post">
                    <input type="email" name="email" placeholder="Your email" required>
                    <button type="submit" aria-label="Subscribe">→</button>
                </form>
                
                <?php
                // App download links
                $settings = new Settings();
                $app_links = [
                    'android' => $settings->get('app_android'),
                    'ios' => $settings->get('app_ios'),
                    'windows' => $settings->get('app_windows'),
                    'macos' => $settings->get('app_macos'),
                ];
                $has_apps = array_filter($app_links);
                
                if ($has_apps):
                ?>
                <div class="app-download-section">
                    <h4>DOWNLOAD OUR APP</h4>
                    <div class="app-buttons">
                        <?php if ($app_links['android']): ?>
                        <a href="<?= Security::output($app_links['android']) ?>" target="_blank" rel="noopener" class="app-badge">
                            <span class="app-icon">📱</span>
                            <div class="app-text">
                                <small>GET IT ON</small>
                                <strong>Google Play</strong>
                            </div>
                        </a>
                        <?php endif; ?>
                        
                        <?php if ($app_links['ios']): ?>
                        <a href="<?= Security::output($app_links['ios']) ?>" target="_blank" rel="noopener" class="app-badge">
                            <span class="app-icon">🍎</span>
                            <div class="app-text">
                                <small>Download on the</small>
                                <strong>App Store</strong>
                            </div>
                        </a>
                        <?php endif; ?>
                        
                        <?php if ($app_links['windows']): ?>
                        <a href="<?= Security::output($app_links['windows']) ?>" target="_blank" rel="noopener" class="app-badge">
                            <span class="app-icon">🪟</span>
                            <div class="app-text">
                                <small>GET IT ON</small>
                                <strong>Windows</strong>
                            </div>
                        </a>
                        <?php endif; ?>
                        
                        <?php if ($app_links['macos']): ?>
                        <a href="<?= Security::output($app_links['macos']) ?>" target="_blank" rel="noopener" class="app-badge">
                            <span class="app-icon">🍏</span>
                            <div class="app-text">
                                <small>Download on the</small>
                                <strong>Mac App Store</strong>
                            </div>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; <?= date('Y') ?> Stream East. All rights reserved.</p>
        </div>
        
        <!-- Custom Footer Code -->
        <?php echo (new Settings())->getCustomCode('footer'); ?>
    </footer>
    
    <!-- Custom Body Bottom Code -->
    <?php echo (new Settings())->getCustomCode('body_bottom'); ?>
</body>
</html>
