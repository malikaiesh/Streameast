# YouTube Clone - Complete Video Streaming Website

## 🎥 Project Overview

A fully-featured YouTube-style video streaming website built with **PHP**, **SQLite/MySQL**, and vanilla **JavaScript**. This is a production-ready CMS for embedding and managing YouTube videos, Shorts, Movies, and Live Sports streams.

## ✨ Features Implemented

### 🌐 Frontend (User Area)
- ✅ Homepage with video grid
- ✅ Trending videos page
- ✅ YouTube Shorts page
- ✅ Movies section
- ✅ Live Sports streams
- ✅ Search functionality
- ✅ Single video player page with:
  - Responsive video embed
  - Related/suggested videos
  - Share functionality
  - Report system
  - View counter
  - Tags and categories
- ✅ Dark/Light theme support
- ✅ Mobile responsive design
- ✅ Category-based filtering

### 🔐 Admin Panel (Complete CMS)
- ✅ Secure admin login (CSRF protected)
- ✅ Dashboard with statistics
- ✅ Add videos via URL (auto-fetch YouTube data)
- ✅ Edit/delete videos
- ✅ Category management
- ✅ Tag management
- ✅ Analytics & view tracking
- ✅ Ad management
- ✅ Custom code injection (HEAD/BODY/FOOTER)
- ✅ Site settings
- ✅ Thumbnail upload/auto-fetch

**Admin Account Setup:**
- On first installation, visit `/admin/setup.php` to create your admin account
- You can set environment variables (`ADMIN_USERNAME`, `ADMIN_PASSWORD`, `ADMIN_EMAIL`) for automatic setup
- Or use the web-based setup wizard to create your account manually

⚠️ **Security: Delete `admin/setup.php` after creating your admin account!**

### 🔍 SEO Features
- ✅ Meta tags (title, description, keywords)
- ✅ OpenGraph tags (Facebook sharing)
- ✅ Twitter Card tags
- ✅ Schema.org VideoObject structured data
- ✅ Auto-generated sitemap.xml
- ✅ robots.txt
- ✅ SEO-friendly URLs (slugs)
- ✅ Per-video SEO customization

### 💰 Monetization
- ✅ Google AdSense integration
- ✅ Custom ad positions (header, sidebar, footer, video overlay)
- ✅ Ad management dashboard
- ✅ ads.txt support

### 🔒 Security
- ✅ CSRF token protection
- ✅ XSS filtering
- ✅ SQL injection prevention (prepared statements)
- ✅ Password hashing (bcrypt)
- ✅ Secure session management
- ✅ Input validation

### 📊 Analytics
- ✅ View tracking per video
- ✅ IP address logging
- ✅ Top videos dashboard
- ✅ Recent activity log
- ✅ Daily statistics

### 🛠️ Technical Features
- ✅ Auto-fetch YouTube metadata (title, thumbnail, duration)
- ✅ Video type support (Regular, Short, Movie, Live)
- ✅ Auto-generate embed codes
- ✅ Custom code injection for analytics/pixels
- ✅ Multi-category support
- ✅ Tag system
- ✅ Report system

## 🚀 Quick Start

The website is already running! Just:
1. **First-time setup**: Visit `/admin/setup.php` to create your admin account
   - Option A: Set environment variables `ADMIN_USERNAME`, `ADMIN_PASSWORD`, `ADMIN_EMAIL`
   - Option B: Use the web setup wizard
2. **Delete setup file**: Remove `/admin/setup.php` for security
3. Go to `/admin/login.php` and login with your credentials
4. Start adding videos!

## 📁 Project Structure

```
/
├── admin/              Admin panel
│   ├── login.php      Login page
│   ├── index.php      Dashboard
│   ├── add-video.php  Add new video
│   ├── videos.php     Manage videos
│   ├── categories.php Category management
│   ├── tags.php       Tag management
│   ├── analytics.php  Analytics
│   ├── ads.php        Ad management
│   ├── custom-code.php Custom code injection
│   └── settings.php   Site settings
├── includes/          PHP classes
│   ├── Database.php   Database handler
│   ├── Security.php   Security functions
│   ├── Video.php      Video model
│   ├── Category.php   Category model
│   ├── Tag.php        Tag model
│   ├── Settings.php   Settings model
│   └── YouTubeHelper.php YouTube API integration
├── views/             Frontend templates
├── assets/            CSS, JS, images
├── config/            Configuration
├── index.php          Homepage
├── watch.php          Video player
├── trending.php       Trending page
├── shorts.php         Shorts page
├── movies.php         Movies page
├── live.php           Live sports page
└── sitemap.php        Auto sitemap
```

## 📖 How to Use

### Adding a Video
1. Go to Admin → Add Video
2. Paste YouTube URL (e.g., `https://www.youtube.com/watch?v=...`)
3. The system auto-fetches title, thumbnail, and duration
4. Select video type (Video/Short/Movie/Live)
5. Choose category, add tags
6. Configure SEO settings
7. Click "Add Video"

### Managing Categories
1. Go to Admin → Categories
2. Add/delete categories
3. Categories appear in sidebar navigation

### SEO Configuration
- **Per-video SEO**: Edit each video to set meta title, description, keywords
- **Site-wide SEO**: Settings → Site Name, Description, Keywords
- **Sitemap**: Auto-generated at `/sitemap.php`

### Monetization Setup
1. **AdSense**: Settings → paste AdSense code
2. **Custom Ads**: Ads Management → Add ad with position and HTML

### Custom Code Injection
Admin → Custom Code
- **HEAD**: Meta tags, CSS, fonts
- **BODY Top**: Google Tag Manager
- **BODY Bottom**: Analytics, pixels
- **FOOTER**: Copyright, custom HTML

## 🗄️ Database

**Current Setup**: SQLite (file: `database.db`)

**Switch to MySQL**: Edit `config/config.php` and uncomment MySQL configuration

### Database Tables
- `admin` - Admin users
- `videos` - All videos
- `categories` - Video categories
- `tags` - Video tags
- `video_tags` - Video-tag relationships
- `analytics_views` - View tracking
- `site_settings` - Site configuration
- `custom_codes` - Code injection
- `ads` - Ad management
- `reports` - Video reports

## 🎨 Customization

### Theme
Settings → Theme Mode (Dark/Light)

### Logo/Site Name
Settings → Site Name

### Videos Per Page
Settings → Videos Per Page

### Colors/Styles
Edit `assets/css/style.css`

## 🔧 Configuration

**File**: `config/config.php`

Key settings:
- Database type (SQLite/MySQL)
- Site URL
- Session lifetime
- Error reporting
- Upload paths

## 📝 Features Checklist

✅ All required features implemented:
- [x] YouTube video embedding
- [x] YouTube Shorts support
- [x] Movies section
- [x] Live sports streams
- [x] Full admin CMS
- [x] Category system
- [x] Tag system
- [x] SEO system
- [x] Analytics
- [x] Monetization
- [x] Security features
- [x] Custom code injection
- [x] Auto sitemap
- [x] Report system
- [x] Share functionality
- [x] Dark/Light themes

## 🚨 Important Notes

1. **First-time setup**: Run `/admin/setup.php` then delete it for security
2. **Environment variables**: Copy `.env.example` to `.env` for configuration
3. **Database resets**: Deleting `database.db` will reset everything
4. **Session warnings**: Normal in development, will resolve in production
5. **YouTube API**: Uses public oEmbed (no API key needed)
6. **Thumbnails**: Auto-fetch from YouTube or upload custom

## 🔐 Security Best Practices

1. ✅ Delete `/admin/setup.php` after initial setup
2. ✅ Use strong admin passwords (8+ characters)
3. ✅ Store credentials in environment variables, never in code
4. ✅ Use HTTPS in production
5. ✅ Regular database backups
6. ✅ Keep PHP updated
7. ✅ Monitor analytics for unusual activity
8. ✅ Review custom code before injection

## 📊 Testing

- **Verify Database**: Visit `/verify-db.php`
- **Test Login**: Visit `/test-login.php`
- **Admin Panel**: `/admin/login.php`
- **Frontend**: `/` (homepage)
- **Sitemap**: `/sitemap.php`

## 🎯 Next Steps

1. **Setup**: Visit `/admin/setup.php` to create admin account
2. **Security**: Delete `/admin/setup.php` after setup
3. Login to admin panel
4. Add your first video
5. Customize categories
6. Configure SEO settings
7. Setup monetization (AdSense/Ads)
8. Add custom code (Analytics)
9. Test frontend pages
10. Deploy to production!

## 📄 License

All code is provided as-is for your use and modification.

---

**Built with ❤️ using PHP 8.2, SQLite, and Vanilla JavaScript**
