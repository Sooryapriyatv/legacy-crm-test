# Changes

## 2026-08-21, 2026-08-22, 2026-08-23

### Issues Faced on Basic Setup
- Paths.php file missing and ystemDirectory path issues and other variable path issues
- Solved by creating Paths.php inside app/Config folder and paths declared
- Environment setup issue, solved by creating development.php inside app/Config/Boot folder
- Writable folder missing
- Fixed the browser fatal error by adding the missing UserAgents.php configuration.
- Migrations.php was created because CodeIgniter 4.7.4 requires a Config\Migrations class to manage migration settings. Without this file, php spark migrate failed.


### Application Testing
- http://localhost:8080/customers view shows "The error view file was not specified. Cannot display error view.". Because paginate() was called on the query builder, now uses the model pagination API.
- Syntax error in customer insertion, solved by updating ActivityModel    
    protected $useTimestamps = false; 
changed true to false and insertion worked.


### PART1 - FIND & FIX BROKEN FEATURES
- Search enabled in customers view using js
- Delete query uncommented in customer control and deletion worked
- Form validations added - in create customer name and city accepts only letters, phone number is only numbers and email and phone number made unique to avoid same insertion
- CSRF enabled
- Filter query is missing in controller so added with like and it worked
- Dashboard count is manuallly set, changed with values from db, controller updated
- In CSV export, customer data is not looped to show in exported file it corrected by looping and try catch added.
- Bulk delete option added - checkboxes added in frontend, js for checking, add function in customer controller to delete data bulk.


### PART2 - REST API

### JWT Authentication
- Installed Firebase JWT using Composer:
    composer require firebase/php-jwt
- Added Firebase JWT v7.1.0 to the project dependencies.
- Added JWT_SECRET_KEY to .env for securely signing and validating JWT tokens.
- Added JWT configuration in app/Config/Jwt.php to load the secret key and token expiration settings.
- Kept the JWT secret in .env instead of hardcoding it in the application code.
- Added JWT service in app/Libraries/JwtService.php for generating and validating tokens.
- Added JWT authentication filter in app/Filters/JwtAuthFilter.php.
- Registered the JWT filter in app/Config/Filters.php.
- Added API authentication route for POST /api/login.
- Added JWT protection for customer API routes.

### JWT Installation Issue
- While setting up JWT, the use Firebase\JWT\JWT; line was initially showing an error in the editor even though the package was installed.
- Checked the package with Composer and confirmed firebase/php-jwt was installed. Then tested the Composer autoloader directly:
    php -r "require 'vendor/autoload.php'; var_dump(class_exists('Firebase\\JWT\\JWT'));"
- The result was:
    bool(true)
- This confirmed that the JWT library was installed and loading correctly. The issue was related to the editor/autoload cache rather than the JWT package itself.
- Resolved it by rebuilding Composer's autoload files:
    composer dump-autoload -o
and reloading the editor.

### CSRF Issue With API Login
- The first API login request returned:
    403 - The action you requested is not allowed.
- The error was caused by the global CodeIgniter CSRF filter blocking the JSON API request.
- Updated app/Config/Filters.php so /api/* routes are excluded from the CSRF filter:
    'csrf' => [
        'except' => [
            'api/*',
        ],
    ],
- This allowed API requests to use JWT authentication without the normal web form CSRF token.

### API Files Created
- app/Controllers/Api/Auth.php
- app/Controllers/Api/Customers.php
- app/Filters/JwtAuthFilter.php
- app/Libraries/JwtService.php
- app/Config/Jwt.php

### Files Updated
- app/Config/Routes.php
- app/Config/Filters.php
- .env
- composer.json
- composer.lock

### Customer API
- Added the following endpoints:
    POST   /api/login
    GET    /api/customers
    GET    /api/customers/{id}
    POST   /api/customers
    PUT    /api/customers/{id}
    DELETE /api/customers/{id}

- All customer endpoints require a valid JWT token.

### API Features
- JWT token authentication
- Token expiration
- Customer pagination using page and per_page
- Filtering using status and city
- Sorting using sort and order
- JSON responses
- Proper HTTP status codes
- Protected customer routes using JWT middleware/filter
- API login endpoint for mobile and third-party applications

### API Testing

- Login:
    
    POST http://localhost:8080/api/login

- JSON body:

    {
        "email": "admin@example.com",
        "password": "Admin@123"
    }

- For protected requests, use:

    Authorization: Bearer YOUR_JWT_TOKEN

- Sample response get,
    {
        "status": true,
        "message": "Login successful.",
        "data": {
            "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3ODc0MzA2NzksImV4cCI6MTc4NzQzNDI3OSwic3ViIjoiMSIsImVtYWlsIjoiYWRtaW5AZXhhbXBsZS5jb20iLCJyb2xlIjoiYWRtaW4ifQ.R7wYAqygL5U4OKpvGIxbhK_-s4l7xOBJIf1r5LDGL8M",
            "expires_at": "2026-08-22T21:31:19+00:00",
            "expires_in": 3600
        }
    }

- Get customers:

    GET http://localhost:8080/api/customers?page=1&per_page=20

- Filter customers:

    GET http://localhost:8080/api/customers?status=active&city=Mumbai

- Sort customers:

    GET http://localhost:8080/api/customers?sort=name&order=asc

- Get one customer:

    GET http://localhost:8080/api/customers/1

- Create:

    POST http://localhost:8080/api/customers

- Update:

    PUT http://localhost:8080/api/customers/1

- Delete:

    DELETE http://localhost:8080/api/customers/1

- All request and response data for the API is handled as JSON.

### Testing Credentials
- JWT Token mentioned in .env file
- Admin Login Credentials
    Username(Email): admin@example.com
    Password: Admin@123

### Other Modifications
- Added role-aware API access checks.
- Added API validation and duplicate email/phone checks that exclude the customer being updated.
- Added JSON validation and structured API error responses.


### PART3 - EMAIL NOTIFICATIONS

### Mailtrap SMTP Setup
- Created a Mailtrap Sandbox for email testing.
- Configured SMTP settings in `app/Config/Email.php`.
- Stored SMTP credentials using environment variables.
- Verified email delivery through the Mailtrap Sandbox inbox.
- Used Mailtrap instead of PHP `mail()` to provide a reliable development email environment.

### Modifications Done
- Created `app/Services/EmailService.php` to handle email operations.
- Added reusable email templates in `app/Views/emails/`.
- Configured SMTP email delivery using Mailtrap Sandbox for testing.
- Integrated automatic welcome email sending when a new customer is created.
- Added error handling and logging to prevent email failures from interrupting customer creation.

### Issues Faced
- Email sending initially failed because the application was attempting to use PHP's default `mail()` function, which was not configured on the development server.
- Configured Mailtrap SMTP credentials, updated the email configuration to use SMTP protocol, and verified successful email delivery through the Mailtrap Sandbox.

### Testing
- Create a new customer from the application.
- Verify the customer record is saved successfully.
- Open Mailtrap Sandbox and confirm the welcome email is received.
- Test with invalid SMTP credentials and verify customer creation still
- succeeds while the error is logged.


### PART 4 - ROLE-BASED ACCESS CONTROL

### Database Changes
- Created a users table to manage application users.
- Added a role column to support Admin, Manager, and Sales roles.
- Added an assigned_to column in the customers table to assign customers to users.
- Created migrations to manage database schema changes.
- Added seeders to create test users and sample role-based data.

### Authentication Updates
- Replaced hardcoded login credentials with database-based authentication.
- Added UserModel integration for user validation.
- Implemented password hash verification using password_verify().
- Stored user ID and role in session after successful login.
- Updated authentication flow to support role-based access control.

### Role-Based Access Control
- Implemented Admin, Manager, and Sales user roles.
- Admin users have full access to all customer records.
- Manager users can access customer records according to assigned permissions.
- Sales users can access only customers assigned to them.
- Added role-based customer filtering using session data.
- Prepared application for permission-based access to customer operations.

### Issues Faced
- Faced foreign key constraint issues while creating user relationships due to mismatched column definitions.
- Resolved the issue by aligning database column types and updating migration definitions.

### Testing
- Verified successful login for Admin, Manager, and Sales users.
- Confirmed user role and session data are stored correctly after authentication.
- Tested role-based customer visibility using different user accounts.
- Verified customer assignments are stored correctly in the database.
- Tested database migrations and seeders on a fresh database setup.

### Test Credentials
- Admin Credentials,
    Username: admin@example.com
    Password: Admin@123
- Manager Credentials
    Username: manager@example.com
    Password: Manager@123
- Sales Credentials
    Username: sales1@example.com
    Password: Sales@123 


### PART5 - DASHBOARD WITH CHARTS

### Summary Cards
- Summary cards updated by fetching each data and showing it in cards
- Mobile responsive done

### Charts
- Pie, Bar, Line charts created through cdn https://cdn.jsdelivr.net/npm/chart.js
- Canvas added and make it responsive
- get each data from db using aggrigate functions and passed to js function to view it as charts


- Caching and refresh button added
- Updated responsive menu


### Seed Data And Database

- Added role and user seed execution to the main database seeder.
- Added demo data generation for 100 customers and 500 customer activities.
- Seeded customer emails and phone numbers are deterministic and unique.
- Added customer assignment seed execution.
- Added migrations for users, customer assignments, manager relationships, and unique customer contact indexes.


### Seed Data

- Created RoleUserSeeder to generate RBAC test users.
- Created one Admin user, one Manager user, and two Sales users.
- Configured sales1 and sales2 with the Manager user's ID through manager_id.
- Passwords are securely hashed using PHP password_hash() with PASSWORD_DEFAULT.
- Created CustomerAssignmentSeeder to distribute existing customers between sales1 and sales2.
- Updated DatabaseSeeder to automatically:
- Create RBAC users.
- Insert 100 sample customers.
- Insert 500 sample customer activities.
- Assign customers between Sales users.
- Test credentials are provided for Admin, Manager, and Sales roles.
- UserSeeder is separate from the RBAC seed process and should not be executed together with RoleUserSeeder because both use admin@example.com.

### db changes file

- created db_changes.sql in migrations folder