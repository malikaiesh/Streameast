# Stream East - Video Streaming Website

## Overview

A complete video streaming platform called **Stream East** built with pure PHP (no frameworks) that enables embedding and managing YouTube videos, Shorts, movies, and live sports streams. The application features a full-featured CMS admin panel for content management, SEO optimization, monetization capabilities, a professional multi-section footer, and a YouTube-style Shorts carousel section on the homepage.

## User Preferences

Preferred communication style: Simple, everyday language.

## System Architecture

### Frontend Architecture

**Technology Stack:**
- Pure HTML5, CSS3, and vanilla JavaScript (no frontend frameworks)
- CSS custom properties for theming (dark/light mode support)
- Responsive design with mobile-first approach

**Key Design Patterns:**
- Component-based CSS architecture with modular stylesheets (`style.css` for user area, `admin.css` for admin panel)
- JavaScript event delegation for dynamic elements
- Progressive enhancement approach

**User Interface Structure:**
- Main user area with video grid layout
- Dedicated pages: Homepage, Trending, Shorts, Movies, Live Sports, Blog, Search
- Homepage Shorts carousel: Horizontal scrolling section appearing after 2 rows (8 videos) with left/right navigation buttons
- Single video player page with embedded video, related videos, sharing, and reporting
- Centered search bar in sticky header navigation
- Theme switching capability (dark/light modes via CSS variables with localStorage persistence)
- Theme toggle button (moon/sun icons) for seamless theme switching
- Login button with gradient styling in header
- Blog section with modern card-based grid layout (6 sample posts)
- Professional footer with social media links, quick navigation, legal links, and newsletter signup form
- Full light mode support with optimized text visibility for sidebar and footer

### Backend Architecture

**Technology Stack:**
- PHP 8.2 with Object-Oriented Programming principles
- SQLite database (MySQL/MariaDB compatible architecture)
- No framework dependency - pure PHP implementation

**Security Implementation:**
- CSRF token protection for forms
- XSS prevention through output sanitization
- SQL injection prevention (parameterized queries)
- Password hashing using bcrypt (PASSWORD_BCRYPT)
- Secure admin authentication system

**Architecture Decisions:**

1. **Database Choice: SQLite over MySQL**
   - Problem: Need lightweight, portable database solution
   - Solution: SQLite for development with MySQL compatibility
   - Rationale: Easy setup on Replit, zero configuration, file-based portability
   - Trade-off: SQLite chosen for simplicity; can switch to MySQL for production scaling

2. **Framework-less PHP Approach**
   - Problem: Required plain PHP without Laravel
   - Solution: Custom OOP structure without framework overhead
   - Rationale: Full control, minimal dependencies, easier deployment
   - Pros: Lightweight, fast execution, no framework learning curve
   - Cons: Manual implementation of routing, validation, ORM features

3. **Admin Panel Separation**
   - Problem: Need secure content management system
   - Solution: Separate `/admin` directory with dedicated authentication
   - Rationale: Clear separation of concerns, enhanced security
   - Implementation: Independent login system with session management

### Data Architecture

**Database Schema (SQLite/MySQL compatible):**
- Videos table: Stores video metadata, URLs, thumbnails, categories, tags, view counts
- Categories table: Video categorization system
- Tags table: Tagging system for content organization
- Analytics table: View tracking and statistics
- Ads table: Advertisement management
- Settings table: Site-wide configuration (custom code injection, SEO settings)
- Users/Admin table: Authentication credentials (bcrypt hashed passwords)

**Content Management:**
- Auto-fetch video metadata from YouTube URLs
- Thumbnail management (upload or auto-fetch from YouTube)
- Category and tag assignment system
- View counter tracking per video

### SEO Implementation

**Metadata System:**
- Dynamic meta tags (title, description, keywords) per page
- OpenGraph tags for social media sharing (Facebook)
- Twitter Card tags for Twitter integration
- Schema.org VideoObject structured data for rich snippets
- Sitemap.php for search engine crawling
- robots.txt for crawler directives

**Strategy:**
Each video page generates unique SEO metadata to maximize search engine visibility and social media sharing capabilities.

### Monetization Architecture

**Ad Management System:**
- Custom code injection points (HEAD, BODY, FOOTER sections)
- Ad placement management through admin panel
- Support for third-party ad networks via code injection

**Implementation:**
Site-wide settings allow injecting custom HTML/JavaScript for:
- Analytics tracking codes
- Ad network scripts
- Custom styling or functionality

## External Dependencies

### Third-Party Services

**YouTube Embedding:**
- YouTube IFrame API for video playback
- YouTube oEmbed API for auto-fetching video metadata
- Support for standard YouTube videos, Shorts, and live streams

**Video Sources:**
- YouTube video embeds (standard and Shorts)
- Generic iframe embeds for movies
- Live sports stream embeds

### Server Requirements

**PHP Configuration:**
- PHP 8.2 or higher
- Required extensions: PDO, PDO_SQLite (or PDO_MySQL for production)
- File upload support for thumbnail management

**Web Server:**
- Currently running on PHP built-in development server (port 5000)
- Production deployment compatible with Apache/Nginx

**Database:**
- SQLite 3 (development)
- MySQL 5.7+ / MariaDB 10.2+ (production alternative)

### Browser Dependencies

**Client-side Requirements:**
- Modern browser with HTML5 video support
- JavaScript enabled for interactive features
- CSS Grid and Flexbox support for responsive layout

**No External JavaScript Libraries:**
- Pure vanilla JavaScript implementation
- No jQuery, React, Vue, or other frameworks
- Keeps page weight minimal and load times fast