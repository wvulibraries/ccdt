# Development Guide

## Local Development Setup

### Prerequisites

- Docker & Docker Compose (v2+)
- PHP 8.1+
- Composer
- Node.js 18+ & npm
- MySQL 8 (optional, for local testing without Docker)

### Environment Configuration

1. Copy environment files:
```bash
cp env/.env.example env/.env.local   # or create your own
```

2. Edit `env/.env.local` with your settings:
```ini
APP_NAME=CCDT
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ccdt
DB_USERNAME=root
DB_PASSWORD=

MAIL_MAILER=smtp
MAIL_HOST=localhost
MAIL_PORT=587
MAIL_USERNAME=null
MAIL_PASSWORD=null
```

### Docker Development

#### Start services
```bash
docker compose -f docker-compose.dev.yml up -d
```

#### Run migrations
```bash
docker exec -it ccdt_php sh ./scripts/setup.sh
```

#### Install dependencies
```bash
# Composer (inside container)
docker exec -it ccdt_php composer install

# npm (inside container or host)
docker exec -it ccdt_php npm install
```

#### Build assets
```bash
docker exec -it ccdt_php npm run dev      # Development build
docker exec -it ccdt_php npm run production  # Production build
```

### Nginx Configuration (Optional)

For a complete local setup, configure Nginx to proxy to PHP-FPM:

```nginx
server {
    listen 80;
    server_name ccdt.local;
    root /path/to/ccdt/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.ht {
        deny all;
    }
}
```

## Running Tests

### PHPUnit

```bash
# Run all tests
docker exec -it ccdt_php vendor/bin/phpunit

# Run specific test file
docker exec -it ccdt_php vendor/bin/phpunit tests/Unit/Models/CollectionUnitTest.php

# Run with coverage
docker exec -it ccdt_php vendor/bin/phpunit --coverage-html=reports/coverage

# Run Feature tests only
docker exec -it ccdt_php vendor/bin/phpunit tests/Feature/

# Run Unit tests only
docker exec -it ccdt_php vendor/bin/phpunit tests/Unit/
```

### Test Structure

```
tests/
├── AuthTest.php           # Authentication tests
├── BrowserKitTestCase.php # Base browser test class
├── RegFormTest.php        # Registration form tests
├── TestCase.php           # Base test case
├── TestHelper.php         # Test utilities
├── ViewTest.php           # View rendering tests
├── Feature/               # Feature/integration tests
│   ├── Controllers/
│   └── Admin/
└── Unit/                  # Unit tests
    ├── Controllers/
    ├── Helpers/
    ├── Jobs/
    ├── Middleware/
    └── Models/
```

## Adding New Features

### Creating a New Controller

```bash
docker exec -it ccdt_php php artisan make:controller MyController
```

Then add routes in `routes/web.php`:

```php
Route::prefix('my-feature')->name('my.')->group(function() {
    Route::get('/', 'MyController@index')->name('index');
    Route::post('action', 'MyController@action')->name('action');
});
```

### Creating a New Model

```bash
docker exec -it ccdt_php php artisan make:model MyModel
```

### Creating a New Artisan Command

```bash
docker exec -it ccdt_php php artisan make:command MyCommand
```

Register in `app/Console/Kernel.php`:

```php
protected $commands = [
    \App\Console\Commands\MyCommand::class,
];
```

### Creating a New Middleware

```bash
docker exec -it ccdt_php php artisan make:middleware MyMiddleware
```

Register in `app/Http/Kernel.php`:

```php
protected $routeMiddleware = [
    'my-middleware' => \App\Http\Middleware\MyMiddleware::class,
];
```

### Creating a New View

Create the Blade template at `resources/views/my-feature/index.blade.php`:

```blade
@extends('layouts.app')

@section('content')
<div class="container">
    <h1>My Feature</h1>
    <p>{{ $message }}</p>
</div>
@endsection
```

### Adding Database Columns

For existing tables, create a migration:

```bash
docker exec -it ccdt_php php artisan make:migration add_new_column_to_tables --table=tables
```

Edit the migration:

```php
public function up()
{
    Schema::table('tables', function (Blueprint $table) {
        $table->string('new_column')->nullable();
    });
}

public function down()
{
    Schema::table('tables', function (Blueprint $table) {
        $table->dropColumn('new_column');
    });
}
```

Run the migration:

```bash
docker exec -it ccdt_php php artisan migrate
```

## Project Scripts Reference

| Script | Purpose |
|--------|---------|
| `scripts/setup.sh` | Run database migrations |
| `scripts/cleanup-dev.sh` | Reset dev environment (preserves import data) |
| `scripts/startup-dev.sh` | Container entrypoint for dev |
| `scripts/startup.sh` | Container entrypoint for prod |
| `scripts/update.sh` | Update dependencies |

## Key Files Reference

| File | Purpose |
|------|---------|
| `composer.json` | PHP dependencies (Laravel 9, Symfony 6) |
| `package.json` | Node.js dependencies (Gulp, Webpack) |
| `phpunit.xml` | PHPUnit configuration |
| `gulpfile.js` | Gulp build tasks |
| `webpack.mix.js` | Webpack Mix configuration |
| `docker-compose.dev.yml` | Dev environment services |
| `Dockerfile.dev` | Dev container image |

## Code Conventions

- **Naming**: Controllers use PascalCase, methods use camelCase, properties use camelCase
- **Models**: Use snake_case for database columns, camelCase for model attributes (`$snakeAttributes = false`)
- **Views**: Organized by feature area (admin/, collection/, table/, user/)
- **Helpers**: Static utility classes in `app/Helpers/`
- **Jobs**: Queueable background tasks in `app/Jobs/`

## Troubleshooting

### Common Issues

1. **Asset compilation fails** — Ensure Node.js 18+ is installed
2. **Composer install fails** — Check PHP version matches `composer.json` requirements (PHP 8.1+)
3. **Database connection refused** — Verify MySQL container is running and credentials match
4. **Permission denied on storage** — Run `chown -R www-data:www-data /var/www/storage`

See also: [Getting Started](getting-started.md) | [Architecture Overview](architecture-overview.md) | [Admin Guide](admin-guide.md)
