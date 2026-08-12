========================================
CleanDouala - Website for Douala
========================================

TECH: XAMPP + HTML + CSS + PHP (+ a little JavaScript for the map)

HOW TO INSTALL ON YOUR COMPUTER
--------------------------------

1. Make sure XAMPP is installed and running (Apache + MySQL)

2. Copy the whole "cleandouala" folder into:
   C:\xampp\htdocs\          (Windows)
   or
   /opt/lampp/htdocs/        (Linux)

3. Open phpMyAdmin: http://localhost/phpmyadmin

4. Create the database:
   - Click "Import"
   - Choose the file: cleandouala/sql/database.sql
   - Click Go

5. Open the website:
   http://localhost/cleandouala/

========================================
PAGES
========================================
- index.php     → Live map + recent reports (with photos)
- report.php    → Report dirty spots / drains (with photo upload)
- pickup.php    → Request waste pickup
- alerts.php    → Drain & Flood alerts
- admin/        → Admin panel (manage reports & pickups)

========================================
ADMIN LOGIN
========================================
URL: http://localhost/cleandouala/admin/

Password: cleandouala2026

(You can change this password in admin/index.php)

========================================
LANGUAGE
========================================
The site is available in French (default) and English.
Click FR or EN in the top menu to switch.

========================================
IMPORTANT NOTES
========================================
- The map uses free OpenStreetMap + Leaflet (no API key needed)
- Photos are saved in the "uploads" folder
- Make sure the "uploads" folder has write permission
- Default database user is root with no password (XAMPP default)

========================================
WHAT WAS ADDED
========================================
1. Admin panel → mark reports as pending / in progress / resolved
2. French + English language switcher
3. Photos shown on the map popups and in the report lists

Good luck cleaning Douala! 🌿
