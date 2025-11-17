# Stream East - Video Streaming Website

## Overview

Stream East is a complete video streaming platform built with pure PHP, designed for embedding and managing YouTube videos, Shorts, movies, and live sports streams. It features a full-featured CMS admin panel for content management, SEO optimization, monetization, a professional multi-section footer, and a YouTube-style Shorts carousel. The platform aims to provide a robust, lightweight, and easily deployable solution for video content delivery with a focus on administrative control and user experience.

## User Preferences

Preferred communication style: Simple, everyday language.

## System Architecture

### Frontend Architecture

**Technology Stack:** Pure HTML5, CSS3 (with custom properties for theming), and vanilla JavaScript.
**Design Principles:** Responsive design with a mobile-first approach, component-based CSS, and JavaScript event delegation.
**User Interface:**
-   **User Area:** Video grid layouts, dedicated pages (Homepage, Trending, Shorts, Movies, Live Sports, Blog, Search), a YouTube-style Shorts carousel, single video player pages with embeds, related videos, sharing, and reporting. Features a sticky header, theme switching (dark/light mode with localStorage persistence), and a professional multi-section footer.
-   **Admin Dashboard:** Modern dark-themed interface with analytics, real-time data visualization (Chart.js for views trend, category distribution), and icon-based sidebar navigation. Includes gradient stat cards for key metrics, a comprehensive blog management system (CRUD), and a full website backup/restore system. Enhanced UI for pages manager, user management, and security settings.

### Backend Architecture

**Technology Stack:** PHP 8.2 with Object-Oriented Programming (OOP) principles. No external frameworks are used, ensuring a lightweight and fully custom implementation.
**Database:** SQLite for development (chosen for portability and ease of setup on Replit) with architecture compatible with MySQL/MariaDB for production scaling.
**Security:**
-   **Core Protections:** CSRF token protection, XSS prevention through output sanitization and a production-ready DOM-based HTML sanitizer (whitelisting tags and attributes, blocking dangerous protocols, removing event handlers), SQL injection prevention via parameterized queries, and bcrypt password hashing.
-   **Admin Security:** Secure admin authentication, role-based access control (Super Admin, Admin, Editor, Viewer), user management with CRUD operations, brute-force protection (IP blocking, rate limiting), comprehensive security headers, activity monitoring, and configurable security settings.
**Architecture Decisions:**
-   **Framework-less PHP:** Provides full control, minimal dependencies, and faster execution, trading off built-in framework features for custom control.
-   **Admin Panel Separation:** A dedicated `/admin` directory with its own authentication ensures clear separation of concerns and enhanced security for content management.

### Data Architecture

**Database Schema:** Includes tables for Videos (metadata, URLs, thumbnails, categories, tags, views), Categories, Tags, Analytics, Ads, Settings, Users/Admin (with role-based access), Blog_posts (title, slug, content, images, status, timestamps), `pages` (title, slug, content, meta_description, status), `login_attempts`, `ip_blocks`, `activity_logs`, `security_settings`, and `sessions`.
**Content Management:** Features auto-fetching YouTube metadata, thumbnail management, category/tag assignment, view counting, and a full blog management system with rich text editing and HTML sanitization. Legal pages are database-driven for easy updates via the admin panel.

### SEO Implementation

**Strategy:** Dynamic meta tags (title, description, keywords), OpenGraph tags, Twitter Card tags, and Schema.org VideoObject structured data are generated per page. Includes `sitemap.php` and `robots.txt` for improved search engine visibility.

### Monetization Architecture

**Ad Management:** Custom code injection points (HEAD, BODY, FOOTER) via the admin panel allow integration with third-party ad networks and analytics scripts.

## External Dependencies

### Third-Party Services

-   **YouTube Embedding:** Utilizes the YouTube IFrame API for video playback and the YouTube oEmbed API for automatically fetching video metadata. Supports standard YouTube videos, Shorts, and live streams.
-   **Video Sources:** Primarily YouTube embeds, with generic iframe support for movies and live sports streams.

### Server Requirements

-   **PHP:** Version 8.2 or higher, with PDO and PDO_SQLite (or PDO_MySQL) extensions.
-   **Web Server:** Compatible with Apache/Nginx for production, currently runnable on PHP's built-in development server.
-   **Database:** SQLite 3 (development), MySQL 5.7+/MariaDB 10.2+ (production).

### Browser Dependencies

-   Modern web browser with HTML5 video support, JavaScript enabled, and CSS Grid/Flexbox support.
-   No external JavaScript libraries (e.g., jQuery, React, Vue) are used, ensuring minimal page weight and fast load times.