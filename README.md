<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

# Laboratory Equipment Borrowing Management System (LEBMS) User Manual

## Table of Contents

 1. Project Overview
 2. System Requirements and Prerequisites
 3. Project Structure
 4. Installation & Setup
 5. Usage Instructions
 6. Export/Import Features
 7. Routes Overview
 8. Troubleshooting & FAQ
 9. Contact & Support
10. Appendix: Additional Resources

## 1. Project Overview

The Laboratory Equipment Borrowing Management System (LEBMS) is a web-based application built with Laravel (version 10.x or higher recommended). It streamlines the management of laboratory equipment, user accounts, borrowing transactions, penalties, and reporting for academic or research institutions. The system supports multiple user roles (Admin, Laboratory Head, Laboratory In-charge, Employee, Borrower). It is designed to be intuitive for both technical and non-technical users.

Key features include:

- **Inventory Management**: Track and categorize laboratory equipment.
- **Borrowing Transactions**: Manage borrowing requests, approvals, returns, and penalties.
- **User Management**: Administer user accounts and roles.
- **Reporting**: Generate and export reports for stock, transactions, and penalties.
- **Security**: Role-based access control and secure authentication.

This manual provides comprehensive instructions for setup, configuration, and usage.

## 2. System Requirements and Prerequisites

### Software Requirements

- **PHP**: Version 8.1 or higher (Laravel 10.x compatibility).
- **Composer**: Latest version for PHP dependency management.
- **Node.js & npm**: Version 16.x or higher for frontend asset compilation.
- **Database**: MySQL 5.7+ or MariaDB 10.3+ (PostgreSQL or other Laravel-supported databases also compatible).
- **Web Server**: Apache or Nginx (Laravel’s development server for local testing).
- **Operating System**: Windows, macOS, or Linux.

### Hardware Requirements

- **CPU**: Dual-core processor or better.
- **RAM**: Minimum 4GB (8GB recommended).
- **Storage**: At least 1GB free disk space for the application, database.

### Additional Tools

- **Text Editor/IDE**: Visual Studio Code, PHPStorm, or similar for editing configuration files.
- **Browser**: Modern browser (e.g., Chrome, Firefox, Edge).
- **Terminal/CLI**: For running Artisan commands and managing dependencies.
- **File Extraction Tool**: Software to unzip the project ZIP file (e.g., WinRAR, 7-Zip, or built-in OS tools).

### Laravel-Specific Dependencies

From `composer.json` (ensure these are included in the ZIP):

- `laravel/framework`: Core Laravel framework.
- `maatwebsite/excel`: For Excel/CSV report exports.

### Environment Setup

- A running database server with credentials (username, password, database name).
- Write permissions for `storage`, `bootstrap/cache`, and `public/storage` directories.
- Access to the project ZIP file (`ACC-LEBMS.zip`).

## 3. Project Structure

The LEBMS follows Laravel’s standard structure:

| Path | Purpose |
| --- | --- |
| `app/` | Core application code (models, controllers, logic) |
| `app/Models/` | Eloquent models (e.g., `User`, `Laboratory`, `Item`, `Category`) |
| `app/Http/Controllers/` | Controllers for HTTP requests (e.g., `AuthController`, `ItemController`) |
| `app/Exports/` | Classes for exporting reports (e.g., `StockSummaryExport`) |
| `database/migrations/` | Database schema definitions |
| `database/seeders/` | Seed files for initial data (see `DatabaseSeeder.php`) |
| `public/` | Public assets (`index.php`, CSS, JS, images) |
| `resources/views/` | Blade templates for web pages |
| `routes/web.php` | Web routes (see Routes Overview) |
| `config/` | Configuration files (e.g., `database.php`, `app.php`, `media-library.php`) |
| `storage/` | Logs, cache, and file uploads (ensure writable) |
| `tests/` | Automated test suites |
| `.env` | Environment configuration file |
| `artisan` | Laravel CLI for tasks |
| `composer.json` | PHP dependencies |
| `package.json` | Node.js dependencies |

## 4. Installation & Setup

### Step-by-Step Guide

 1. **Unzip the Project**

    - Extract the ZIP file to a directory (e.g., `/path/to/lebms`) using a tool like WinRAR, 7-Zip, or your OS’s built-in unzip utility.
    - Example (Linux/macOS):

      ```sh
      unzip ACC-LEBMS.zip -d /path/to/lebms
      ```
    - Navigate to the project directory:

      ```sh
      cd /path/to/lebms
      ```

 2. **Install PHP Dependencies**

    - Run the following to install dependencies:

      ```sh
      composer install
      ```
    - If Composer is not installed, download it from https://getcomposer.org.

 3. **Install Node.js Dependencies**

    - Install frontend dependencies:

      ```sh
      npm install
      ```

 4. **Configure the Environment File**

    - Copy the example environment file:

      ```sh
      cp .env.example .env
      ```
    - Edit `.env` with a text editor and configure:

      ```env
      APP_NAME=LEBMS
      APP_ENV=local
      APP_KEY=
      APP_DEBUG=true
      APP_URL=http://localhost
      
      DB_CONNECTION=mysql
      DB_HOST=127.0.0.1
      DB_PORT=3306
      DB_DATABASE=lebms
      DB_USERNAME=root
      DB_PASSWORD=
      
      FILESYSTEM_DISK=public
      MEDIA_DISK=public
      ```
    - Update `DB_DATABASE`, `DB_USERNAME`, and `DB_PASSWORD` with your database credentials.

 5. **Generate Application Key**

    - Run:

      ```sh
      php artisan key:generate
      ```

 6. **Set Up the Database**

    - Ensure your database server is running and the database (e.g., `lebms`) exists.
    - Run migrations and seed initial data:

      ```sh
      php artisan migrate --seed
      ```
    - This creates tables for `laboratories`, `users`, `categories`, `items`, and more, and populates them with data from `DatabaseSeeder.php`, including:
      - **Laboratories**: Biology Research Lab, Chemistry Analysis Lab, Physics Experimental Lab.
      - **Users**: 1 Admin (`sysadmin`), Laboratory Heads and In-charges for each lab, 2 Employees, 2 Borrowers.
      - **Categories**: Microscopes, Glassware, Chemicals, etc.
      - **Items**: Compound Microscope, Beaker 500ml, Hydrochloric Acid, etc.
      - **Note**: The transaction seeding section is commented out in the provided seeder.

 7. **Link Storage**

    - Create a symlink for media storage:

      ```sh
      php artisan storage:link
      ```
    - This links `storage/app/public` to `public/storage`.

 8. **Compile Frontend Assets**

    - Build assets using Vite or Laravel Mix:

      ```sh
      npm run build
      ```

 9. **Set Directory Permissions**

    - Ensure writable permissions for storage directories:

      ```sh
      chmod -R 775 storage bootstrap/cache public/storage
      ```

10. **Start the Application**

    - Launch the development server:

      ```sh
      php artisan serve
      ```
    - Open the URL (e.g., `http://127.0.0.1:8000`) in a browser.

11. **Verify Installation**

    - Access the login page (`/`) and log in with seeded credentials:
      - **Admin**: Username: `sysadmin`, Password: `password`
      - **Laboratory Head**: Username: `labhead1`, Password: `password` (and similar for other labs)
      - **Laboratory In-charge**: Username: `incharge1`, Password: `password`
      - **Employee**: Username: `employee1`, Password: `password`
      - **Borrower**: Username: `borrower1`, Password: `password`

> **Note**: For production, configure a web server (Apache/Nginx), set `APP_ENV=production`, and ensure HTTPS.

## 5. Usage Instructions

### Logging In

- Visit the application URL (e.g., `http://127.0.0.1:8000`).
- Log in with seeded credentials or register at `/register`.
- Use `/recover` for password recovery if needed.

### User Roles and Permissions

The seeder defines five roles:

- **Admin**: Full access to all features (`/admin/*` routes).
- **Laboratory Head**: Manages lab-specific inventory and transactions.
- **Laboratory In-charge**: Assists with lab operations and transactions.
- **Employee**: Views items and manages own transactions (`/employee/*` routes).
- **Borrower**: Requests borrowing and views transactions (`/borrower/*` routes).

### Main Features

#### Inventory Management

- **Access**: Admin (`/admin/inventory`), Staff (`/staff/inventory`).
- **Actions**:
  - Add/edit/delete items (`/items` routes).
  - View item details.
- **Categories**:
  - Manage at `/admin/category` or `/staff/category`.
  - Seeded categories: Microscopes, Glassware, Chemicals, etc.

#### Borrowing Transactions

- **Requesting**:
  - Borrowers/Employees: Use `/borrower/transaction` or `/employee/transaction` to request items.
  - Select items (e.g., Compound Microscope, Beaker 500ml).
- **Managing**:
  - Admin/Staff: Use `/admin/transaction` or `/staff/transaction` to:
    - Confirm (`transactions.confirm`).
    - Reject (`transactions.reject`).
    - Release (`transactions.release`).
    - Return (`transactions.return`).
    - Cancel (`transactions.cancel`).
  - Check borrowing limits via `/transactions/check-user-limit/{userId}`.
- **Penalties**: View at `/admin/penalty` or `/staff/penalties` (seeder does not include penalties by default).

#### User Management

- **Access**: Admin-only (`/admin/user`).
- **Actions**:
  - Manage users (`users.store`, `users.update`, `users.destroy`).
  - Update user status (`users.status`).
  - Change passwords (`changeUserPassword`).

#### Laboratory Management

- **Access**: Admin-only (`/admin/laboratory`).
- **Actions**: Manage labs (`laboratories.store`, `laboratories.update`, etc.).
- **Seeded Labs**: Biology, Chemistry, Physics labs.

#### Reports

- **Access**: Admin (`/admin/report`), Staff (`/staff/report`).
- **Reports**:
  - Stock Summary (`reports.stock_summary`): Lists items like Microscopes, Beakers.
  - Transaction History (`reports.transaction_history`): Borrowing records.
  - Penalty Summary (`reports.penalty_summary`): Penalty details.
  - Overdue Transactions (`reports.overdue_transactions`): Overdue borrowings.
- **Export**: Download as Excel files.

### Navigation

- **Sidebar/Top Menu**: Role-specific navigation (e.g., Dashboard, Inventory).
- **Search/Filters**: Available in inventory and transaction views.

## 6. Export/Import Features

- **Export**:
  - Access via `/admin/report` or `/staff/report`.
  - Export reports (Stock Summary, Transaction History, etc.) in Excel format using `maatwebsite/excel`.
- **Import**:
  - Not supported natively. Use `php artisan db:seed` for bulk data via seeder.

## 7. Routes Overview

Based on the provided `web.php`:

| Route Path | Purpose | Access Role |
| --- | --- | --- |
| `/` | Login page | Guest |
| `/register` | Registration page | Guest |
| `/login` (POST) | Authenticate user | Guest |
| `/logout` (POST) | Log out | Authenticated |
| `/recover` | Password recovery form | Guest |
| `/reset/{token}` | Password reset form | Guest |
| `/admin/dashboard` | Admin dashboard | Admin |
| `/admin/laboratory` | Manage laboratories | Admin |
| `/admin/staff` | View staff list | Admin |
| `/admin/employee` | View employee list | Admin |
| `/admin/borrower` | View borrower list | Admin |
| `/admin/category` | Manage categories | Admin |
| `/admin/item` | Manage items | Admin |
| `/admin/transaction` | Manage transactions | Admin |
| `/admin/inventory` | Manage inventory | Admin |
| `/admin/penalty` | View penalties | Admin |
| `/admin/report` | Generate reports | Admin |
| `/admin/user` | Manage users | Admin |
| `/staff/dashboard` | Staff dashboard | Staff |
| `/staff/category` | Manage categories | Staff |
| `/staff/item` | Manage items | Staff |
| `/staff/transaction` | Manage transactions | Staff |
| `/staff/inventory` | Manage inventory | Staff |
| `/staff/penalties` | View penalties | Staff |
| `/staff/report` | Generate reports | Staff |
| `/employee/dashboard` | Employee dashboard | Employee |
| `/employee/item` | View items | Employee |
| `/employee/transaction` | Manage own transactions | Employee |
| `/borrower/dashboard` | Borrower dashboard | Borrower |
| `/borrower/item` | View items | Borrower |
| `/borrower/transaction` | Manage borrowing requests | Borrower |
| `/laboratories` | List laboratories | Admin |
| `/users` | List users | Admin |
| `/categories` | List categories | Admin, Staff |
| `/items` | List items | Admin, Staff |
| `/transactions` | List transactions | Admin, Staff |
| `/reports/stock_summary` | Stock summary report | Admin, Staff |
| `/reports/transaction_history` | Transaction history report | Admin, Staff |
| `/reports/penalty_summary` | Penalty summary report | Admin, Staff |
| `/reports/overdue_transactions` | Overdue transactions report | Admin, Staff |

## 8. Troubleshooting & FAQ

### Common Issues

- **Database Connection Errors**:
  - Verify `.env` database settings and ensure the database exists.
  - Run `php artisan config:clear`.
- **Migration Errors**:
  - Create the database manually if it doesn’t exist.
  - Run `php artisan migrate:fresh --seed` to reset.
- **Permission Issues**:
  - Set permissions: `chmod -R 775 storage bootstrap/cache public/storage`.

### FAQ

- **Q: How do I log in initially?**
  - A: Use `sysadmin`/`password` for Admin or other seeded credentials.
- **Q: Why are there no transactions?**
  - A: Transaction seeding is commented out in `DatabaseSeeder.php`. Uncomment or create transactions manually.

## 9. Contact & Support

- **Laravel Resources**: https://laravel.com/docs/10.x

## 10. Appendix: Additional Resources

- **Laravel Documentation**: https://laravel.com/docs/10.x
- **Laravel Excel**: https://docs.laravel-excel.com

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
