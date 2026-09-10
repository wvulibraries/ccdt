# Architecture Overview

## Technology Stack

| Layer | Technology |
|-------|-----------|
| **Framework** | Laravel 9 (PHP 8.1+) |
| **Database** | MySQL 8 |
| **Web Server** | PHP-FPM 8.2 (port 9000) |
| **Document Parsing** | Apache Tika 1.28.4 (port 9998) |
| **Frontend** | Blade templates, Bootstrap CSS |
| **Build Tools** | Gulp, Webpack Mix, npm |
| **Testing** | PHPUnit 9.6, Laravel Browser Kit |

## Project Structure

```
ccdt/
├── app/
│   ├── Adapters/          # Data import/export adapters
│   │   ├── ImportAdapter.php
│   │   ├── searchIndexAdapter.php
│   │   └── updateSearchAdapter.php
│   ├── Console/
│   │   ├── Commands/      # Artisan CLI commands (15 commands)
│   │   └── Kernel.php
│   ├── Exceptions/        # Custom exception handlers
│   ├── Helpers/           # Business logic utilities
│   │   ├── CMSHelper.php       # CMS record type processing
│   │   ├── CollectionHelper.php # Collection CRUD operations
│   │   ├── CSVHelper.php       # CSV parsing & delimiter detection
│   │   ├── CustomStringHelper.php
│   │   ├── FileViewHelper.php
│   │   └── TableHelper.php     # Dynamic schema management
│   ├── Http/
│   │   ├── Controllers/    # 8 controllers (see below)
│   │   └── Middleware/     # Auth & admin access control
│   ├── Jobs/              # Queue jobs for background imports
│   ├── Libraries/         # Third-party library wrappers
│   ├── Models/            # Eloquent models
│   │   ├── AllowedFileTypes.php
│   │   ├── CMSRecords.php
│   │   ├── Collection.php  # Groups tables and files
│   │   ├── Jobs.php        # Job queue tracking
│   │   ├── Table.php       # Dynamic table records
│   │   └── User.php
│   ├── Providers/         # Service providers
│   └── Services/          # Application services
├── config/               # Laravel configuration files
├── database/
│   ├── migrations/       # Schema migrations
│   └── seeds/            # Database seeders
├── public/              # Web root (index.php, assets)
├── resources/
│   ├── assets/          # Source CSS/JS
│   ├── lang/            # Localization files
│   └── views/           # Blade templates
├── routes/
│   ├── web.php          # Web UI routes
│   ├── api.php          # API routes
│   └── console.php      # Artisan route bindings
├── scripts/             # Dev ops scripts
│   ├── cleanup-dev.sh
│   ├── setup.sh
│   ├── startup-dev.sh
│   └── startup.sh
└── storage/
    ├── app/flatfiles/   # Mounted: raw import files (preserved)
    ├── app/files/       # Mounted: uploaded attachments
    ├── exports/         # Mounted: exported data
    └── logs/            # Mounted: application logs
```

## Core Domain Model

```
Collection (1) ──┬── (N) Table
                 │
                 └── (N) File (via Storage)

Table (1) ──┬── (N) Records (dynamic, per-table)
            └── (N) Jobs (import tracking)

User ──┬── Admin (isAdmin = true)
       └── Regular User (isAdmin = false)
```

### Collections

Collections are the top-level organizational unit. Each collection can be:
- **Flat file** — Import from CSV/delimited files
- **CMS** — Import from Constituent Management System record types (requires `cmsId`)

### Tables

Tables are dynamically created at runtime (no migrations for data tables). Each table has:
- Standard fields: `id`, `created_at`, `updated_at`, `srchIndex`
- User-defined fields (string columns of varying sizes)

## Middleware Stack

| Middleware | Purpose | Access Control |
|-----------|---------|---------------|
| `RedirectIfAdmin` | Admin-only access | Requires `isAdmin = true` |
| `RedirectIfAuthenticated` | Auth redirect | Redirects logged-in users away from login |
| `CheckCollectionId` | Validates collection ID exists | Authenticated users |
| `CheckTableId` | Validates table ID exists | Authenticated users |
| `EncryptCookies` | Cookie encryption | All requests |
| `VerifyCsrfToken` | CSRF protection | All POST/PUT/PATCH requests |

## Route Map

### Web Routes (`routes/web.php`)

| Prefix | Controller | Purpose | Admin Only |
|--------|-----------|---------|------------|
| `/` | HomeController | Landing page | No |
| `/auth/*` | Auth controllers | Login/Register/Logout | No |
| `/home` | HomeController | Dashboard | No |
| `/help` | View | Help documentation | No |
| `/collection/*` | CollectionController | CRUD for collections | Yes |
| `/table/*` | TableController | CRUD for tables & schemas | Yes |
| `/admin/wizard/*` | WizardController | Import wizard (flatfile/CMS) | Yes |
| `/data/{table}` | DataViewController | Browse/search records | Auth only |
| `/upload/{colID}` | UploadController | File uploads | Auth only |

### API Routes (`routes/api.php`)

| Method | Endpoint | Purpose |
|--------|---------|---------|
| GET | `/api/user` | Authenticated user info |

## Data Flow: Import Process

```
1. Admin creates Collection (flatfile or CMS type)
        │
        ▼
2. Admin uploads file(s) via Import Wizard
        │
        ▼
3. CSVHelper detects delimiter, parses header
        │
        ▼
4. TableHelper creates dynamic table schema
        │
        ▼
5. FileImport job dispatched to queue
        │
        ▼
6. ImportAdapter processes rows in batches
        │
        ▼
7. Search index created/updated via CreateSearchIndex job
        │
        ▼
8. Admin monitors progress at /admin/jobs/pending
```

## Storage Architecture

The application uses Docker volume mounts for persistent data:

| Host Path | Container Path | Purpose | Preserved on Cleanup? |
|-----------|---------------|---------|----------------------|
| `./data/flatfiles` | `/var/www/storage/app/flatfiles` | Raw import files | ✅ Yes |
| `./data/files` | `/var/www/storage/app/files` | Uploaded attachments | ✅ Yes |
| `./data/exports` | `/var/www/storage/exports` | Exported data | ❌ No |
| `./data/logs` | `/var/www/storage/logs` | Application logs | ❌ No |
| `./data/vendor` | `/var/www/vendor` | Composer dependencies | ❌ No (regenerate) |

See also: [Getting Started](getting-started.md) | [Admin Guide](admin-guide.md)
