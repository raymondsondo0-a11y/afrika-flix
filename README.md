# Social Media Ad Scheduler & Cost Planner — Mbeya Template

Swahili-first campaign calendar for small businesses, designed for InfinityFree shared hosting.

## Stack
- PHP 8.1+ with PDO; no Composer or Node.js
- MySQL via phpMyAdmin
- Tailwind CSS CDN and FullCalendar 6 CDN; local CSS in style.css

## Features
- Add, edit and confirm-delete campaigns
- Calendar with platform colors and quick status update
- Daily, weekly and monthly cost summaries
- Monthly budget progress and warning at 80%+
- Platform/status/date filters and CSV export
- Browser notifications for campaigns in the next 30 minutes (dashboard must stay open; checks every 60 minutes)
- Responsive layout for phones

## Deploy to InfinityFree
1. In InfinityFree, open the hosting account for **ndoa.kesug.com** and create a MySQL database.
2. Open phpMyAdmin and import `install.sql`. InfinityFree creates the database for you; do not run `CREATE DATABASE`.
3. On the server, create `/htdocs/private/config.php` based on `db.example.php`. Enter the actual host, database name, username and password from the InfinityFree panel. Never commit credentials to GitHub.
4. Configure GitHub Actions secrets `FTP_SERVER`, `INFINITYFREE_FTP_USERNAME`, and `INFINITYFREE_FTP_PASSWORD`. `FTP_SERVER` is the FTP hostname shown by InfinityFree, not the website domain.
5. Push to `main` or run the Deploy workflow. It deploys to `/ndoa.kesug.com/htdocs/`.
6. Open `https://ndoa.kesug.com/`.

## Security and reminders
- Public `db.php` only loads `private/config.php`; it contains no database password. The deployment workflow excludes the private folder.
- Notifications only work while the dashboard is open in a supported browser and permission has been granted.
- CDN calendar assets need an internet connection. Local CSS keeps the layout usable if Tailwind CDN is unavailable.
