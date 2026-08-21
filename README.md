# Legacy CRM System - Machine Round Test

## Setup Instructions

### 1. Prerequisites
- PHP 8.0 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- Composer

### 2. Installation

```bash
# Clone the repository
git clone https://github.com/YOUR-USERNAME/legacy-crm-test.git
cd legacy-crm-test

# Install dependencies
composer install

# Copy environment file
cp env .env

# Edit .env file with your database credentials
```

### 3. Database Setup

Edit `.env` file:
```
database.default.hostname = localhost
database.default.database = legacy_crm
database.default.username = root
database.default.password = 
database.default.DBDriver = MySQLi
```

Run the database migration:
```bash
php spark migrate
php spark db:seed DatabaseSeeder
```

This will create:
- `customers` table with 100 sample records
- `customer_activities` table with activity logs
- `users` table with admin user

### 4. Start Development Server

```bash
php spark serve
```

Visit: http://localhost:8080

### 5. Login Credentials

**Admin Account:**
- Username: `admin`
- Password: `admin123`

## Project Structure

```
app/
├── Controllers/
│   ├── Auth.php          # Login/Logout
│   ├── Dashboard.php     # Home page
│   └── Customers.php     # Customer CRUD
├── Models/
│   ├── CustomerModel.php
│   └── ActivityModel.php
├── Views/
│   ├── auth/
│   ├── customers/
│   └── layout/
└── Database/
    ├── Migrations/
    └── Seeds/
```

## Your Task

This is a legacy system with **intentional bugs and missing features**. Your job is to:

1. **Find and fix 8 broken features** (search, delete, edit, dashboard, filters, export, validation, pagination)
2. **Build CSV Import feature** (with validation and error handling)
3. **Build Email Notification System** (SMTP integration, EmailService class, templates)
4. **Build Bulk Delete feature** (checkboxes, Select All, JavaScript, confirmation)
5. **Document everything** in `CHANGES.md` file

**Important:** You must test the application to find what's broken. We won't tell you exactly where each bug is - that's part of the test!

See full task description and evaluation criteria after you register on the portal.

Good luck!
