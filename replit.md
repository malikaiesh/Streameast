# Stream East - Video Streaming Website

## Overview

A complete video streaming platform called **Stream East** built with pure PHP (no frameworks) that enables embedding and managing YouTube videos, Shorts, movies, and live sports streams. The application features a full-featured CMS admin panel for content management, SEO optimization, monetization capabilities, a professional multi-section footer, and a YouTube-style Shorts carousel section on the homepage.

## Recent Updates

### November 17, 2025 - User Management & Enhanced Security Features
- **Password Visibility Toggle:**
  - Eye icon button on login page to show/hide password
  - Smooth toggle animation with accessible icons
  - Same functionality added to all password fields in admin panel
- **Role-Based Access Control (RBAC):**
  - Complete multi-user system with role hierarchy
  - Four role levels: Super Admin, Admin, Editor, Viewer
  - Role-specific permissions and access levels
  - Database schema updated with role, full_name, status, last_login fields
- **User Management System:**
  - New "User Management" page in Security section
  - Full CRUD operations for admin users
  - Add new users with username, password, full name, email, and role
  - Edit existing users (update details or change password)
  - Delete users with confirmation (cannot delete yourself)
  - Password visibility toggle in user forms
  - User status management (active/inactive)
  - Search and filter users by role
  - Pagination for large user lists
- **Account Settings:**
  - Dedicated "Account Settings" page in Configuration section
  - Update profile information (username, full name, email)
  - Change password with current password verification
  - Password strength validation (minimum 6 characters)
  - View account details (ID, member since, last login, status)
  - Password visibility toggles on all password fields
- **Authentication Enhancements:**
  - Last login tracking for all admin users
  - Role information stored in session
  - Automatic role assignment on login
  - Enhanced login security with activity logging

### November 17, 2025 - Advanced Security System Implementation
- **Comprehensive Security Management:**
  - New "Security" section in admin sidebar with dedicated dashboard
  - Real-time security statistics and threat monitoring
  - Activity logging for all admin actions with IP tracking
  - Complete security event audit trail
- **Brute Force Protection:**
  - Automatic IP blocking after failed login attempts (configurable threshold)
  - Rate limiting to prevent automated attacks
  - Temporary and permanent IP blocking capabilities
  - IP whitelist/blacklist management interface
- **Security Headers:**
  - Automatic HTTP security headers (X-Frame-Options, CSP, X-XSS-Protection, etc.)
  - Content Security Policy (CSP) for XSS prevention
  - Clickjacking protection
  - MIME sniffing prevention
- **Activity Monitoring:**
  - Complete login attempt tracking (successful and failed)
  - Admin action logging with timestamps and IP addresses
  - Security event dashboard with statistics
  - Filterable activity logs with pagination
- **Security Settings:**
  - Configurable max login attempts
  - Adjustable lockout duration
  - Session timeout configuration
  - Rate limiting controls
  - Toggle for security headers
- **Database Security:**
  - New security tables: login_attempts, ip_blocks, activity_logs, security_settings, sessions
  - Automated cleanup of expired IP blocks
  - Session management and tracking

### November 17, 2025 - UI/UX Improvements & Backup Restore
- **Pages Manager UI Redesign:**
  - Professional gradient purple header matching Calculators Manager style
  - Icon badge with user info widget showing logged-in admin
  - Improved text visibility with white/light text on dark backgrounds
  - Modern table design with icon column and color-coded status badges
  - Professional action buttons (Edit/Delete) with proper hover effects
  - Enhanced empty states and better spacing throughout
- **Backup System - Restore Functionality:**
  - Added full restore capability to backup system
  - One-click restore button for each backup with safety confirmation
  - Automatically restores database (SQLite or MySQL), uploads, and thumbnails
  - Temporary extraction directory with automatic cleanup
  - Clear warning dialogs to prevent accidental data loss
  - Success messages showing restore timestamp

### November 17, 2025 - Pages Management System
- **Pages Management in Admin Panel:**
  - Complete CRUD system for managing website pages from admin panel
  - List all pages with search, pagination, and status indicators
  - Add/Edit pages with title, slug, content, meta description, and status
  - Delete pages with confirmation
  - Database-driven pages with automatic slug generation
  - HTML sanitization for security (using production-ready DOM sanitizer)
  - Fixed session authentication using Security::requireAdmin() for consistent behavior
- **Database:**
  - New `pages` table to store all website pages
  - Fields: id, title, slug, content, meta_description, status, created_at, updated_at
- **Legal Pages (Database-Driven):**
  - Privacy Policy, Terms of Service, DMCA, Contact Us pages now pull content from database
  - Pages can be edited from admin panel without modifying code
  - Automatic "last updated" dates based on database timestamps
  - SEO-friendly with meta descriptions
  - Favicon support added to all legal pages
- **Admin Interface:**
  - New "Pages" menu item in Content Management section
  - Pages list view with actions (View, Edit, Delete)
  - Add/Edit page form with rich text editor support
  - Slug preview showing final URL
  - Draft/Published status management
- **Footer Updates:**
  - Added functional links to all legal pages in the "LEGAL" section
  - Added Blog link to "QUICK LINKS" section
  - All footer links now properly navigate to their respective pages

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

**Admin Dashboard:**
- Modern dark-themed interface with analytics and data visualization
- Icon-based sidebar navigation with grouped sections (Dashboard, Content, Security, Configuration)
- 8 gradient stat cards showing real-time metrics:
  - Total Videos (purple), Shorts (blue), Total Views (green), Categories (pink)
  - Videos Today (indigo), Movies (orange), Live Sports (teal), Tags (cyan)
- Chart.js integration for real-time data visualization:
  - Line chart: 7-day views trend with smooth curves and gradient fill
  - Horizontal bar chart: Top 8 categories by video count with colorful bars
  - Doughnut chart: Content type distribution (Regular, Shorts, Movies, Live)
- Interactive chart filters for different time periods (7D, 15D, 30D, 3M)
- Recent videos table with dark theme styling
- Full blog management system (CRUD operations):
  - Blog list with search, filtering, and pagination
  - Create/edit blog posts with title, category, excerpt, content, and featured images
  - Draft/published status management
  - Production-ready DOM-based HTML sanitization for user-generated content
- Complete backup system:
  - One-click full website backups (database + files)
  - Download backups as ZIP files
  - Restore from backups with one click
  - Manage and delete old backups
  - Automatic inclusion of database, uploads, and thumbnails
- User Management & Security:
  - User Management page with role-based access control
  - Account Settings for password and profile updates
  - Security Dashboard with threat monitoring
  - Activity Logs tracking all admin actions
  - IP Management for blocking suspicious addresses
  - Security Settings for configuring protection features
- Real database data integration for all stats and charts
- Responsive design for mobile and tablet views
- Professional modal dialogs for forms and confirmations

### Backend Architecture

**Technology Stack:**
- PHP 8.2 with Object-Oriented Programming principles
- SQLite database (MySQL/MariaDB compatible architecture)
- No framework dependency - pure PHP implementation

**Security Implementation:**
- CSRF token protection for forms
- XSS prevention through output sanitization and production-ready HTML sanitizer
- DOM-based HTML sanitization for blog content:
  - Whitelist approach with allowed tags (p, br, strong, b, em, i, u, a, ul, ol, li, blockquote, h1-h6, img, code, pre)
  - Attribute filtering per tag (a: href/title, img: src/alt/title)
  - Dangerous protocol blocking (javascript:, data:, vbscript:, file:, about:)
  - HTML entity decoding to catch obfuscated payloads
  - Event handler removal (onclick, onerror, etc.)
  - Sibling-iteration algorithm that preserves whitelisted tags even when nested in stripped wrappers
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
- Blog_posts table: Blog content management with title, slug, category, excerpt, content, featured_image, draft/published status, and timestamps

**Content Management:**
- Auto-fetch video metadata from YouTube URLs
- Thumbnail management (upload or auto-fetch from YouTube)
- Category and tag assignment system
- View counter tracking per video
- Full blog management with CRUD operations:
  - Rich text content with HTML sanitization (preserves formatting, blocks XSS)
  - Automatic slug generation from titles
  - Category organization
  - Featured image uploads (stored in uploads/blog/)
  - Draft/published workflow with publication timestamps
  - Excerpt support with automatic generation fallback

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