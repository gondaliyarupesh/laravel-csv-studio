# Laravel CSV Studio 🎬

[![Latest Version on Packagist](https://img.shields.io/packagist/v/rupeshgondaliya/laravel-csv-studio.svg?style=flat-square)](https://packagist.org/packages/rupeshgondaliya/laravel-csv-studio)
[![Total Downloads](https://img.shields.io/packagist/dt/rupeshgondaliya/laravel-csv-studio.svg?style=flat-square)](https://packagist.org/packages/rupeshgondaliya/laravel-csv-studio)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![PHP Version](https://img.shields.io/badge/php-%5E8.0-blue.svg?style=flat-square)](https://php.net)
[![Laravel Version](https://img.shields.io/badge/laravel-%5E9.0%20%7C%20%5E10.0%20%7C%20%5E11.0%20%7C%20%5E12.0-red.svg?style=flat-square)](https://laravel.com)

**Laravel CSV Studio** is a beautiful, premium, and feature-rich Laravel package that converts a basic CSV importer/exporter into a high-fidelity visual workspace. Built with modern glassmorphic designs, micro-animations, and Excel-like editing, it provides an immersive, single-page application (SPA) style dashboard for developers and end-users to import and export database records interactively.

---

## 🌟 Key Features

### 1. Dual-Workflow Welcome Dashboard
* Seamlessly select between **Importing CSV Data** or **Exporting Database Tables** via modern interactive glassmorphic cards with responsive hover transitions.

### 2. Interactive Import Workspace (CSV $\rightarrow$ DB)
* **Smart Auto-Mapping**: Automatically pairs CSV headers with database columns using smart synonym matching (e.g. `Full Name` $\rightarrow$ `name`, `E-mail Address` $\rightarrow$ `email`).
* **Excel-Like Grid**: Double-click any cell in the table grid to edit content inline before importing.
* **Flexible Validation**: Real-time format checks (emails, phone numbers, numeric prices, required columns) with distinct visual warnings and interactive CSS hover tooltips.
* **Dynamic Grid Controls**: Add or delete records on the fly instantly from the visual grid interface.
* **Insert vs Update Modes**:
  - **Insert Mode**: Generates new records and hides database auto-incrementing ID fields to prevent collisions.
  - **Update Mode**: Requires mapping a unique ID column, validates that every row has an ID, checks database existence, and performs transactional in-place updates.
* **Safe Transactions**: Executes import operations within database transactions, performing clean rollbacks if any row fails database-level insertion constraints.

### 3. Live Preview Exporter Workspace (DB $\rightarrow$ CSV)
* **Real-Time Data Preview**: Once a target database table is selected, a secure background JSON query fetches live table schemas and actual database entries.
* **Live preview Grid**: Renders a read-only preview grid of database table entries (up to 100 rows) with counts, table structure, and column headers.
* **Memory-Safe Streaming**: Features streamed CSV downloads from the database, allowing memory-safe exports of large datasets using Laravel streamed responses.

---

## 🚀 Installation

Install the package via Composer:

```bash
composer require rupeshgondaliya/laravel-csv-studio
```

Optional: Publish the configuration file to customize allowed tables, route prefixes, and middleware:

```bash
php artisan vendor:publish --tag=laravel-csv-studio-config
```

---

## ⚙️ Configuration

The published configuration file is located at `config/csv-importer.php`. Customize the settings as needed:

```php
return [
    /*
    |--------------------------------------------------------------------------
    | Route Configurations
    |--------------------------------------------------------------------------
    */
    'route_prefix' => 'csv-importer',
    'middleware' => ['web'],

    /*
    |--------------------------------------------------------------------------
    | Allowed Tables
    |--------------------------------------------------------------------------
    | Specify an array of table names that are allowed for import/export.
    | Leave empty to allow all tables except the excluded tables below.
    */
    'allowed_tables' => [],

    /*
    |--------------------------------------------------------------------------
    | Excluded Tables
    |--------------------------------------------------------------------------
    */
    'excluded_tables' => [
        'migrations',
        'password_resets',
        'password_reset_tokens',
        'failed_jobs',
        'personal_access_tokens',
        'sessions',
        'jobs',
        'job_batches',
        'cache',
        'cache_locks'
    ],
];
```

---

## 🎨 Walkthrough & Usage

### Launching the Studio
Navigate to the configured route in your web browser:
```
http://your-app.test/csv-importer
```

### Flow 1: Importing CSV Data
1. Select **Import CSV Data** from the landing dashboard.
2. Select your destination database table and choose the **Import Mode** (Insert vs Update).
3. Drag & drop or browse your `.csv` or `.txt` file, then click **Analyze & Map Columns**.
4. Review the auto-mapped headers, double-click cells to modify values directly in the interactive grid, correct validation tooltips, and click **Verify & Import Data**.

### Flow 2: Exporting Database Tables
1. Select **Export Table Data** from the landing dashboard.
2. Choose your target database table from the select list.
3. Review columns and actual records in the **Live Database Record Preview** grid.
4. Click **Export Data to CSV** to stream and download a clean CSV copy.

---

## 🧪 Testing

Run the automated integration and unit test suite to verify everything functions properly:

```bash
vendor/bin/phpunit
```

---

## 👥 Authors & Credits

* **Rupesh Gondaliya** - [gondaliyarupesh@gmail.com](mailto:gondaliyarupesh@gmail.com)
* Created with a dedication to premium user experiences and robust web development practices.

---

## 📄 License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
