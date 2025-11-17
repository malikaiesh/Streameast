-- SQLite Database Schema for YouTube Clone

CREATE TABLE IF NOT EXISTS admin (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT UNIQUE NOT NULL,
    password TEXT NOT NULL,
    email TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT UNIQUE,
    email TEXT UNIQUE NOT NULL,
    password TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS categories (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT UNIQUE NOT NULL,
    slug TEXT UNIQUE NOT NULL,
    description TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS tags (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT UNIQUE NOT NULL,
    slug TEXT UNIQUE NOT NULL
);

CREATE TABLE IF NOT EXISTS videos (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT NOT NULL,
    description TEXT,
    video_url TEXT NOT NULL,
    embed_code TEXT,
    thumbnail TEXT,
    duration TEXT,
    video_type TEXT DEFAULT 'video',
    category_id INTEGER,
    views INTEGER DEFAULT 0,
    likes INTEGER DEFAULT 0,
    slug TEXT UNIQUE NOT NULL,
    meta_title TEXT,
    meta_description TEXT,
    meta_keywords TEXT,
    is_active INTEGER DEFAULT 1,
    is_trending INTEGER DEFAULT 0,
    featured INTEGER DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS video_tags (
    video_id INTEGER NOT NULL,
    tag_id INTEGER NOT NULL,
    PRIMARY KEY (video_id, tag_id),
    FOREIGN KEY (video_id) REFERENCES videos(id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS analytics_views (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    video_id INTEGER NOT NULL,
    user_ip TEXT,
    user_agent TEXT,
    viewed_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (video_id) REFERENCES videos(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS custom_codes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    code_location TEXT NOT NULL,
    code_content TEXT,
    is_active INTEGER DEFAULT 1,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS site_settings (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    setting_key TEXT UNIQUE NOT NULL,
    setting_value TEXT,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS ads (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    ad_name TEXT,
    ad_position TEXT NOT NULL,
    ad_code TEXT NOT NULL,
    is_active INTEGER DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS reports (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    video_id INTEGER NOT NULL,
    reason TEXT NOT NULL,
    user_ip TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    status TEXT DEFAULT 'pending',
    FOREIGN KEY (video_id) REFERENCES videos(id) ON DELETE CASCADE
);

-- Security Tables
CREATE TABLE IF NOT EXISTS security_login_attempts (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT,
    ip_address TEXT NOT NULL,
    user_agent TEXT,
    success INTEGER DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS security_ip_blocks (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    ip_address TEXT UNIQUE NOT NULL,
    reason TEXT,
    block_type TEXT DEFAULT 'temporary',
    expires_at DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS security_activity_logs (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    admin_id INTEGER,
    action TEXT NOT NULL,
    table_name TEXT,
    record_id INTEGER,
    details TEXT,
    ip_address TEXT,
    user_agent TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_id) REFERENCES admin(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS security_settings (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    setting_key TEXT UNIQUE NOT NULL,
    setting_value TEXT,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS security_sessions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    session_id TEXT UNIQUE NOT NULL,
    admin_id INTEGER NOT NULL,
    ip_address TEXT,
    user_agent TEXT,
    last_activity DATETIME DEFAULT CURRENT_TIMESTAMP,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_id) REFERENCES admin(id) ON DELETE CASCADE
);

-- Insert default security settings
INSERT OR IGNORE INTO security_settings (setting_key, setting_value) VALUES
('max_login_attempts', '5'),
('login_lockout_time', '900'),
('session_timeout', '1800'),
('enable_rate_limiting', '1'),
('rate_limit_requests', '100'),
('rate_limit_window', '60'),
('enable_2fa', '0'),
('enable_ip_whitelist', '0'),
('security_headers_enabled', '1');

-- Admin user will be created on first setup via environment variables
-- Set ADMIN_USERNAME, ADMIN_PASSWORD, and ADMIN_EMAIL in your environment

-- Insert default categories
INSERT OR IGNORE INTO categories (name, slug, description) VALUES
('Technology', 'technology', 'Tech videos and tutorials'),
('Gaming', 'gaming', 'Gaming videos and streams'),
('Movies', 'movies', 'Movie trailers and clips'),
('Sports', 'sports', 'Sports highlights and live streams'),
('Music', 'music', 'Music videos and concerts'),
('Education', 'education', 'Educational content'),
('Entertainment', 'entertainment', 'Entertainment videos'),
('News', 'news', 'News and current events');

-- Insert default site settings
INSERT OR IGNORE INTO site_settings (setting_key, setting_value) VALUES
('site_name', 'YouTube Clone'),
('site_description', 'Watch and share videos'),
('site_keywords', 'videos, streaming, youtube, watch'),
('theme_mode', 'dark'),
('enable_downloads', '0'),
('videos_per_page', '12'),
('adsense_code', ''),
('google_analytics', '');

-- Add role-based access control columns to admin table
-- Role types: super_admin, admin, editor, viewer
ALTER TABLE admin ADD COLUMN role TEXT DEFAULT 'admin';
ALTER TABLE admin ADD COLUMN full_name TEXT;
ALTER TABLE admin ADD COLUMN status TEXT DEFAULT 'active';
ALTER TABLE admin ADD COLUMN last_login DATETIME;
ALTER TABLE admin ADD COLUMN updated_at DATETIME DEFAULT CURRENT_TIMESTAMP;
