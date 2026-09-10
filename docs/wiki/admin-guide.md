# Admin Guide

## Console Commands

All commands are run inside the Docker container via `docker exec`.

### Collection Management

```bash
# Create a new collection
docker exec -it ccdt_php php artisan collection:create --name="My Collection" --isCms=false

# Rename a collection
docker exec -it ccdt_php php artisan collection:rename --id=1 --newName="New Name"

# Enable a disabled collection
docker exec -it ccdt_php php artisan collection:enable --id=1

# Disable a collection (archiving)
docker exec -it ccdt_php php artisan collection:disable --id=1

# Delete a collection (permanent)
docker exec -it ccdt_php php artisan collection:delete --id=1

# Set CMS ID for a collection
docker exec -it ccdt_php php artisan collection:setcms --id=1 --cmsId=12345

# Unset CMS ID from a collection
docker exec -it ccdt_php php artisan collection:unsetcms --id=1
```

### Table Management

```bash
# Export a table to CSV
docker exec -it ccdt_php php artisan table:export --tableId=1 --path="./storage/exports/"

# Import from CSV file
docker exec -it ccdt_php php artisan table:import --tableId=1 --file="./storage/app/flatfiles/data.csv"

# Drop a table (permanent)
docker exec -it ccdt_php php artisan table:drop --tableId=1

# Rename a table
docker exec -it ccdt_php php artisan table:rename --oldName="old_tbl" --newName="new_tbl"

# Truncate all records from a table (keeps schema)
docker exec -it ccdt_php php artisan table:truncate --tableId=1
```

### Search Index Management

```bash
# Create search index for a table
docker exec -it ccdt_php php artisan search:create --tableId=1

# Optimize existing search index
docker exec -it ccdt_php php artisan search:optimize --tableId=1
```

## Database Setup

### Initial Setup

```bash
# Run all database migrations
docker exec -it ccdt_php sh ./scripts/setup.sh
```

The setup script runs Laravel migrations to create the core tables:
- `users` — User accounts
- `collections` — Collection metadata
- `tables` — Table metadata (schema definitions)
- `jobs` — Job queue tracking
- `cms_records` — CMS record type headers
- `allowed_file_types` — Allowed file extensions

### Manual Migration

```bash
docker exec -it ccdt_php php artisan migrate
```

### Rollback Migrations

```bash
# Rollback last batch
docker exec -it ccdt_php php artisan migrate:rollback

# Rollback all migrations
docker exec -it ccdt_php php artisan migrate:reset
```

### Seed Database

```bash
# Seed all tables
docker exec -it ccdt_php php artisan db:seed

# Seed specific class
docker exec -it ccdt_php php artisan db:seed --class=UsersTableSeeder
```

## Storage Structure

### Volume Mounts (docker-compose.dev.yml)

| Host Path | Container Path | Purpose | Cleanup Safe? |
|-----------|---------------|---------|--------------|
| `./data/flatfiles` | `/var/www/storage/app/flatfiles` | Raw import files | ❌ Never delete |
| `./data/files` | `/var/www/storage/app/files` | Uploaded attachments | ❌ Never delete |
| `./data/exports` | `/var/www/storage/exports` | Exported data | ✅ Safe |
| `./data/logs` | `/var/www/storage/logs` | Application logs | ✅ Safe |
| `./data/vendor` | `/var/www/vendor` | Composer dependencies | ✅ Regenerate with correct PHP |

### Cleanup Script

```bash
bash scripts/cleanup-dev.sh
```

This script:
1. Prunes unused Docker volumes (database, etc.)
2. Clears `./data/exports/*`
3. Clears `./data/logs/*`
4. Clears `./data/vendor/*`
5. **Preserves** `./data/flatfiles/*` and `./data/files/*`

### Storage Paths Reference

```
/var/www/storage/
├── app/
│   ├── flatfiles/     ← Import source files
│   ├── files/         ← User uploads
│   └── exports/       ← Exported data
├── framework/
│   ├── cache/         ← Application cache
│   ├── sessions/      ← Session storage
│   └── views/         ← Compiled Blade templates
├── logs/              ← Laravel log files
└── vendor/            ← Composer dependencies (mounted)
```

## Middleware Configuration

### Admin-Only Routes

The `RedirectIfAdmin` middleware checks `$request->user()->isAdmin`. Non-admin users are redirected to `/home`.

Applied to:
- All `/collection/*` routes
- All `/table/*` routes
- All `/admin/wizard/*` routes
- All `/admin/jobs/*` routes

### Authenticated Routes

The `RedirectIfAuthenticated` middleware is applied to auth routes (login/register). Logged-in users are redirected away from these pages.

### Custom Validation Middleware

- `CheckCollectionId` — Validates the collection ID exists in the database
- `CheckTableId` — Validates the table ID exists in the database

## Configuration Files

Key configuration files in `config/`:

| File | Purpose |
|------|---------|
| `app.php` | Application name, timezone, locale |
| `database.php` | MySQL connection settings |
| `filesystems.php` | Storage disk configuration |
| `mail.php` | SMTP/email settings |
| `queue.php` | Queue driver (database/redis/sync) |
| `session.php` | Session storage configuration |

## Troubleshooting

### Common Issues

1. **`ResetInterface not found`** — PHP version mismatch between host and container. Run `cleanup-dev.sh` and rebuild.

2. **Import jobs stuck in pending** — Check the queue worker is running:
   ```bash
   docker exec -it ccdt_php php artisan queue:work --stop-when-empty
   ```

3. **Search returns no results** — Ensure search index exists:
   ```bash
   docker exec -it ccdt_php php artisan search:optimize --tableId={id}
   ```

4. **Permission errors on storage** — Ensure correct ownership:
   ```bash
   docker exec -it ccdt_php chown -R www-data:www-data /var/www/storage
   ```

See also: [Getting Started](getting-started.md) | [User Guide](user-guide.md) | [Development](development.md)
