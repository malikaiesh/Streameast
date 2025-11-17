# YouTube Clone - Installation & Documentation

## Overview

This is a complete YouTube-style video streaming website built in PHP with all the features requested:
- Video embedding (YouTube, YouTube Shorts, Movies, Live Sports)
- Full admin panel (CMS)
- SEO optimization
- Monetization features
- Security features
- Custom code injection
- Analytics and more

## Technology Stack

- **Frontend**: HTML5, CSS3, JavaScript
- **Backend**: PHP 8.2 (Object-Oriented)
- **Database**: SQLite (can be switched to MySQL/MariaDB)
- **Security**: CSRF protection, XSS prevention, SQL injection prevention
- **Framework**: Plain PHP (No Laravel)

## Quick Start

### 1. Server is Already Running!

The PHP development server is already running on port 5000.
Just open the website URL provided by Replit.

### 2. Access Admin Panel

**Admin URL**: `http://your-domain.com/admin/login.php`

**Default Login Credentials**:
- Username: `admin`
- Password: `admin123`

**Important**: Change the admin password after first login!

### 3. Change Admin Password

To change the admin password, you need to generate a new password hash:

```php
<?php
echo password_hash('your-new-password', PASSWORD_BCRYPT);
?>
```

Then update the password in the database.

## Database

### SQLite (Default - Current Setup)

The project uses SQLite by default for easy setup. The database file is `database.db` and is created automatically on first run.

### Switch to MySQL/MariaDB

To use MySQL instead:

1. Edit `config/config.php`:
```php
// Uncomment these lines:
define('DB_TYPE', 'mysql');
define('DB_HOST', 'localhost');
define('DB_NAME', 'youtube_clone');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
define('DB_SOCKET', '/tmp/mysql.sock'); // For Replit

// Comment out SQLite:
// define('DB_TYPE', 'sqlite');
// define('DB_PATH', __DIR__ . '/../database.db');
```

2. Import the database schema:
```bash
mysql -u root -p < database.sql
```

## Features Implemented

### 1. User Area

✅ Homepage with video categories
✅ Trending page
✅ Shorts page
✅ Movies page
✅ Live sports page
✅ Search functionality
✅ Single video page with:
  - Title and description
  - Related/suggested videos
  - Share button
  - Report button
  - View counter

### 2. Admin Panel Features

✅ Admin dashboard with statistics
✅ Add videos (via URL embed)
✅ Manage videos (edit, delete)
✅ Category management
✅ Tag management
✅ Analytics (views, popular videos)
✅ Thumbnail upload (auto-fetch from YouTube)
✅ SEO settings per video
✅ Monetization settings
✅ Custom code injection

### 3. Video Management

- Add videos via YouTube embed URL
- Auto-fetch title and thumbnail from YouTube
- Support for YouTube videos, Shorts, Movies, Live streams
- Category assignment
- Tag system
- Auto-generated slugs (SEO-friendly URLs)
- Duplicate video prevention

### 4. SEO System

✅ Meta title, description, keywords
✅ OpenGraph tags (Facebook sharing)
✅ Twitter Card tags
✅ Schema.org VideoObject
✅ Automatic sitemap.xml (`/sitemap.php`)
✅ robots.txt file
✅ SEO-friendly URLs (slugs)

### 5. Monetization Features

✅ Google AdSense integration
✅ Custom ad management (header, sidebar, footer, video overlay)
✅ ads.txt generator
✅ Multiple ad positions

### 6. Security Features

✅ CSRF token protection on all forms
✅ XSS filtering on all outputs
✅ SQL injection prevention (prepared statements/PDO)
✅ Password hashing (bcrypt)
✅ Secure session handling
✅ File upload validation
✅ Secure admin authentication

### 7. Custom Code Injection

Admin can inject custom code in:
✅ HEAD section
✅ BODY (top)
✅ BODY (bottom)
✅ FOOTER

Perfect for:
- Google Analytics
- Facebook Pixel
- Custom scripts
- Third-party integrations

### 8. Analytics

✅ View tracking per video
✅ IP address logging
✅ Top videos by views
✅ Recent activity log
✅ Daily statistics

## File Structure

```
/
├── admin/                  # Admin panel
│   ├── views/             # Admin templates
│   ├── login.php          # Admin login
│   ├── index.php          # Dashboard
│   ├── videos.php         # Video management
│   ├── add-video.php      # Add new video
│   ├── edit-video.php     # Edit video
│   ├── categories.php     # Category management
│   ├── tags.php           # Tag management
│   ├── analytics.php      # Analytics
│   ├── ads.php            # Ad management
│   ├── custom-code.php    # Custom code injection
│   ├── settings.php       # Site settings
│   └── logout.php         # Logout
├── assets/
│   ├── css/
│   │   ├── style.css      # Frontend styles
│   │   └── admin.css      # Admin panel styles
│   ├── js/
│   │   ├── main.js        # Frontend JavaScript
│   │   └── admin.js       # Admin JavaScript
│   └── thumbnails/        # Uploaded thumbnails
├── config/
│   └── config.php         # Configuration file
├── includes/              # PHP classes
│   ├── Database.php       # Database handler
│   ├── Security.php       # Security functions
│   ├── Video.php          # Video model
│   ├── Category.php       # Category model
│   ├── Tag.php            # Tag model
│   └── Settings.php       # Settings model
├── views/
│   ├── header.php         # Frontend header
│   └── footer.php         # Frontend footer
├── api/
│   └── report.php         # Report video API
├── index.php              # Homepage
├── watch.php              # Video player page
├── trending.php           # Trending videos
├── shorts.php             # Shorts page
├── movies.php             # Movies page
├── live.php               # Live sports
├── sitemap.php            # Auto-generated sitemap
├── robots.txt             # Robots file
├── database.sql           # MySQL schema
└── database_sqlite.sql    # SQLite schema
```

## Admin Panel Guide

### Adding a Video

1. Go to Admin Panel → Add Video
2. Enter video title
3. Paste YouTube URL (the script auto-detects video ID)
4. Select video type (Video, Short, Movie, Live)
5. Add description (optional)
6. Select category
7. Add tags (comma-separated)
8. Upload custom thumbnail OR leave empty to auto-fetch
9. Configure SEO settings
10. Click "Add Video"

### Managing Categories

1. Go to Categories
2. Add new categories for organizing videos
3. Categories appear in the sidebar navigation

### Managing Tags

1. Go to Tags
2. Add tags for better video organization
3. Tags can be added when creating/editing videos

### SEO Configuration

Each video has individual SEO settings:
- Meta Title
- Meta Description
- Meta Keywords

Site-wide SEO settings are in Settings page.

### Monetization Setup

**Google AdSense:**
1. Go to Settings
2. Paste your AdSense code
3. It will appear on all pages

**Custom Ads:**
1. Go to Ads Management
2. Add ad with name, position, and HTML code
3. Ads appear in selected positions

### Custom Code Injection

1. Go to Custom Code
2. Add code for HEAD, BODY TOP, BODY BOTTOM, or FOOTER
3. Perfect for analytics, pixels, custom scripts

### Analytics

View detailed statistics:
- Total views across all videos
- Views today
- Top performing videos
- Recent activity log
- Per-video statistics

## API Endpoints

### Report Video
**Endpoint**: `/api/report.php`
**Method**: POST
**Payload**:
```json
{
  "video_id": 123,
  "reason": "Inappropriate content"
}
```

## Customization

### Changing Theme

The site supports dark and light themes. Change in:
Admin → Settings → Theme Mode

### Changing Logo/Site Name

Admin → Settings → Site Name

### Adjusting Videos Per Page

Admin → Settings → Videos Per Page

## Security Best Practices

1. **Change Default Admin Password** immediately
2. **Use HTTPS** in production (enable in Replit settings)
3. **Regular Backups** of database
4. **Keep PHP Updated**
5. **Monitor Analytics** for suspicious activity

## Troubleshooting

### Videos Not Showing
- Check if videos are marked as "Active" in admin
- Verify video URLs are valid
- Check database connection

### Admin Login Not Working
- Verify credentials (default: admin/admin123)
- Clear browser cookies
- Check database has admin user

### Thumbnails Not Loading
- Ensure `assets/thumbnails/` directory exists
- Check file permissions (777)
- Verify thumbnail URLs are valid

### SEO/Sitemap Issues
- Access `/sitemap.php` to verify it's generating
- Check all videos have slugs
- Verify meta tags in page source

## Deployment to Production

1. **Switch to MySQL** for better performance
2. **Enable HTTPS**
3. **Set display_errors = 0** in config
4. **Configure caching headers**
5. **Setup proper backups**
6. **Use environment variables** for sensitive data

## Credits

Built with:
- PHP 8.2
- SQLite/MySQL
- Vanilla JavaScript
- CSS3

## Support

For issues or questions:
1. Check this documentation
2. Review the code comments
3. Check admin panel settings

---

**Enjoy your YouTube Clone! 🎥**
