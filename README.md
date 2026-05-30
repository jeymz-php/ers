# UCC Event Reservation System (UCC-ERS)

A comprehensive event reservation system for the University of Caloocan City, allowing students and faculty to reserve campus venues, manage events, and streamline campus activities.

## Features

### User Features
- 🔐 **Authentication System** - Login, Registration, Forgot Password
- 📅 **Calendar View** - View approved events by campus
- 📍 **Venue Reservation** - Browse and reserve available establishments
- 📎 **Multi-Date Reservations** - Reserve multiple dates in a single request
- 📎 **Multiple File Attachments** - Upload up to 5 files (PDF, JPG, PNG)
- 📊 **Reservation Summary** - View active, pending, and past reservations
- 📄 **PDF Reports** - Download reservation reports
- 🔔 **Email Notifications** - Receive updates on reservation status
- 📱 **Mobile Responsive** - Fully responsive design for all devices

### Admin/Super Admin Features
- 📊 **Dashboard** - Overview with stats, campus utilization, recent activities
- 📅 **Availability Calendar** - View all approved events across campuses
- 👥 **User Management** - Approve/Reject user registrations, bulk approvals
- 📋 **Reservation Management** - Approve/Reject reservation requests
- 🏛️ **Campus Management** - Manage campuses and establishments
- 👨‍💼 **Admin Management** - Create/Delete admin accounts (Super Admin only)
- 📄 **Report Generation** - Generate PDF reports for all reservations
- 🔔 **Real-time Notifications** - Sound and toast notifications for new reservations
- ⚙️ **Settings** - Change password, backup/restore (coming soon)

## Technology Stack

- **Backend**: Laravel 9.x (PHP 8.1+)
- **Frontend**: Blade Templates, HTML5, CSS3, JavaScript
- **Database**: MySQL / MariaDB
- **PDF Generation**: Barryvdh/Laravel-DomPDF
- **Mail**: SMTP (Gmail support)
- **Authentication**: Laravel Breeze/Sanctum

## System Requirements

- PHP >= 8.1
- Composer
- MySQL >= 5.7 or MariaDB >= 10.2
- Web Server (Apache/Nginx) or PHP built-in server
- Node.js & NPM (optional, for frontend assets)