# Smart Library Management System

## Technology
- Frontend: HTML, CSS, Bootstrap, JavaScript
- Backend: PHP
- Database: MySQL
- Server: XAMPP/Apache
- QR Scanner: html5-qrcode CDN
- IDE: VS Code

## Features
1. Admin/librarian login
2. Student registration
3. Student QR/University ID scanning
4. Library key QR scanning
5. Automatic entry/exit tracking
6. Book issue and return
7. Due-date and overdue status
8. Admin dashboard
9. Library visit logs

## XAMPP Setup
1. Install XAMPP.
2. Start Apache and MySQL.
3. Copy this project folder into `C:\xampp\htdocs\`.
4. Open `http://localhost/phpmyadmin`.
5. Create/import the database using `database/smart_library.sql`.
6. If your MySQL username/password differs, edit `config/db.php`.
7. Open `http://localhost/smart_library_management_system/`.

## Demo Login
Email: admin@library.com
Password: admin123

## Demo QR Values
Student QR: `DIU-2026-001`
Library Key QR: `LIB-KEY-001`

For real deployment, use HTTPS for camera access and change the demo password.
