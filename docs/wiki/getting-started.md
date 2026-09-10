# Getting Started

## What is CCDT?

The Constituent Correspondence Data Tool (CCDT) is a web application that transforms congressional correspondence data into searchable, analyzable information. It provides:

- **Collection management** — Group related tables and files together
- **Flat file import** — Import CSV/delimited files directly into the database
- **CMS record type import** — Import structured CMS (Constituent Management System) record types
- **Table management** — Create, edit, and modify table schemas dynamically
- **Data viewing** — Browse records with pagination and card-based display
- **Full-text search** — Search across indexed fields
- **Job queue** — Monitor and manage background import jobs

## Prerequisites

- Docker & Docker Compose (v2+)
- PHP 8.1+ (for local development)
- Composer
- Node.js 18+ & npm

## Quick Start with Docker

### 1. Clone the repository

```bash
git clone https://github.com/wvulibraries/ccdt.git
cd ccdt
```

### 2. Configure environment

Copy and edit the environment files:

```bash
cp env/.env.example prod env/.env.prod   # if not already present
```

Ensure `env/.env.mysql` contains your MySQL credentials.

### 3. Start services

```bash
docker compose -f docker-compose.dev.yml up -d
```

This starts three services:
- **app** — PHP-FPM application server on port 9000
- **database** — MySQL 8 on port 3306
- **tika_service** — Apache Tika on port 9998 (document parsing)

### 4. Run database migrations

```bash
docker exec -it ccdt_php sh ./scripts/setup.sh
```

### 5. Seed test users (optional)

To create default test accounts, run inside the container:

```bash
docker exec -it ccdt_php php artisan db:seed --class=UsersTableSeeder
```

This creates:
| Email | Password | Role |
|-------|----------|------|
| `test@test.com` | `testing` | Regular user |
| `admin@admin.com` | `testing` | Admin |

### 6. Access the application

The PHP-FPM server runs on port 9000. You'll need a web server (Nginx/Apache) in front of it to serve the application. See the [Development Guide](development.md#local-development-setup) for full setup instructions.

## Cleanup

To reset the development environment while preserving raw import data:

```bash
bash scripts/cleanup-dev.sh
docker compose -f docker-compose.dev.yml up -d
docker exec -it ccdt_php sh ./scripts/setup.sh
```

See also: [Architecture Overview](architecture-overview.md) | [User Guide](user-guide.md)
