# API Reference

## Overview

CCDT provides a minimal REST API for authenticated access to user data. The application is primarily a web UI-driven tool.

### Base URL

```
http://localhost/api
```

### Authentication

All API routes require Bearer token authentication via Laravel's `auth:api` middleware.

```
Authorization: Bearer {token}
```

Tokens are generated via Laravel's built-in Sanctum or Passport (depending on configuration).

## Endpoints

### Get Authenticated User

```http
GET /api/user
```

**Headers:**
```
Authorization: Bearer {token}
Accept: application/json
```

**Response (200 OK):**
```json
{
  "id": 1,
  "name": "admin",
  "email": "admin@admin.com",
  "isAdmin": true,
  "created_at": "2024-01-01T00:00:00.000000Z",
  "updated_at": "2024-01-01T00:00:00.000000Z"
}
```

**Controller:** `UserController@AuthRouteAPI`

## Web Routes Reference

### Public Routes (No Auth)

| Method | URL | Controller | Description |
|--------|-----|-----------|-------------|
| GET | `/` | `HomeController@index` | Landing page |
| GET | `/login` | Auth login form | User login |
| POST | `/login` | Auth login | Authenticate user |
| GET | `/register` | Auth register form | User registration |
| POST | `/register` | Auth register | Create new user |
| POST | `/logout` | Auth logout | Destroy session |

### Authenticated Routes

| Method | URL | Controller | Description |
|--------|-----|-----------|-------------|
| GET | `/home` | `HomeController@index` | Dashboard |
| GET | `/help` | View | Help documentation |
| GET | `/data/{tableId}` | `DataViewController@index` | Browse table records |
| GET | `/data/{tableId}/{recordId}` | `DataViewController@show` | View single record |
| POST | `/data/search` | `DataViewController@search` | Search records |
| POST | `/upload/{colID}` | `UploadController@storeFiles` | Upload attachments |

### Admin Routes (Admin Only)

#### Collections (`/collection`)

| Method | URL | Controller | Description |
|--------|-----|-----------|-------------|
| GET | `/collection` | `CollectionController@index` | List all collections |
| POST | `/collection/create` | `CollectionController@create` | Create collection |
| POST | `/collection/edit` | `CollectionController@edit` | Update collection |
| GET | `/collection/show/{colID}` | `CollectionController@show` | View collection details |
| GET | `/collection/upload/{colID}` | `CollectionController@upload` | Upload to collection |
| GET | `/collection/{colID}/table/create` | `CollectionController@tableCreate` | Create table form |
| POST | `/collection/disable` | `CollectionController@disable` | Disable collection |
| POST | `/collection/enable` | `CollectionController@enable` | Enable collection |

#### Tables (`/table`)

| Method | URL | Controller | Description |
|--------|-----|-----------|-------------|
| GET | `/table` | `TableController@index` | List all tables |
| GET | `/table/edit/{curTable}` | `TableController@edit` | Edit table metadata |
| POST | `/table/update` | `TableController@update` | Update table |
| GET | `/table/edit/schema/{curTable}` | `TableController@editSchema` | Edit schema form |
| POST | `/table/update/schema` | `TableController@updateSchema` | Update schema |
| GET | `/table/create` | `TableController@create` | Create table form |
| POST | `/table/create/finalize` | `TableController@finalize` | Finalize table creation |
| GET | `/table/load` | `TableController@load` | Load table data |
| POST | `/table/load/store` | `TableController@store` | Store loaded data |
| POST | `/table/restrict` | `TableController@restrict` | Restrict table access |

#### Import Wizard (`/admin/wizard`)

| Method | URL | Controller | Description |
|--------|-----|-----------|-------------|
| GET | `/admin/wizard/import/collection/{colID}` | `WizardController@importCollection` | Select import type |
| GET | `/admin/wizard/flatfile` | `WizardController@flatfile` | Flat file import page |
| POST | `/admin/wizard/flatfile/upload` | `WizardController@flatfileUpload` | Upload flat files |
| POST | `/admin/wizard/flatfile/select` | `WizardController@flatfileSelect` | Select columns |
| GET | `/admin/wizard/cms` | `WizardController@cms` | CMS import page |
| POST | `/admin/wizard/cms/upload` | `WizardController@cmsUpload` | Upload CMS file |
| POST | `/admin/wizard/cms/select` | `WizardController@cmsSelect` | Select record type |

#### Job Management (`/admin/jobs`)

| Method | URL | Controller | Description |
|--------|-----|-----------|-------------|
| GET | `/admin/jobs/pending` | `JobsController@pending` | List pending jobs |
| GET | `/admin/jobs/failed` | `JobsController@failed` | List failed jobs |
| POST | `/admin/jobs/retry/{id}` | `JobsController@retry` | Retry single job |
| POST | `/admin/jobs/retryAll` | `JobsController@retryAll` | Retry all failed |
| POST | `/admin/jobs/forget/{id}` | `JobsController@forget` | Remove job from queue |

## Models & Relationships

### Collection Model (`App\Models\Collection`)

**Attributes:**
- `id` (integer, primary key)
- `clctnName` (string) — Collection name
- `isCms` (boolean) — CMS type flag
- `cmsId` (integer, nullable) — CMS system ID
- `created_at` / `updated_at`

**Relationships:**
- `hasMany(Table)` — Associated tables

### Table Model (`App\Models\Table`)

**Attributes:**
- `id` (integer, primary key)
- `tblNme` (string) — Table name
- `collection_id` (integer, foreign key)
- `created_at` / `updated_at`

**Relationships:**
- `belongsTo(Collection)` — Parent collection

**Dynamic Methods:**
- `recordCount()` — Count of records in table
- `getPage($amount)` — Paginated records
- `getColumnList()` — Column names
- `getOrgCount()` — Field count excluding standard fields
- `getRecord($id)` — Single record by ID

### User Model (`App\Models\User`)

**Attributes:**
- `id` (integer, primary key)
- `name` (string)
- `email` (string)
- `password` (string, hashed)
- `isAdmin` (boolean)
- `created_at` / `updated_at`

### CMSRecords Model (`App\Models\CMSRecords`)

Stores CMS record type headers for CMS imports.

**Attributes:**
- `id` (integer, primary key)
- `recordType` (string) — e.g., "1A", "1B"
- `cmsId` (integer) — CMS system ID
- `fieldNames` (text, serialized array) — Column definitions

### Jobs Model (`App\Models\Jobs`)

Tracks background import job status.

**Attributes:**
- `id` (integer, primary key)
- `table_id` (integer) — Target table
- `status` (string) — pending/processing/completed/failed
- `error_message` (text, nullable)
- `created_at` / `updated_at`

### AllowedFileTypes Model (`App\Models\AllowedFileTypes`)

Defines allowed file extensions for uploads.

## Helper Classes

| Class | Purpose | Key Methods |
|-------|---------|-------------|
| `TableHelper` | Dynamic schema management | `createStringField()`, `createTable()`, `addColumn()` |
| `CollectionHelper` | Collection CRUD | `create()`, `update()`, `delete()` |
| `CSVHelper` | CSV parsing | `detectDelimiter()`, `tknz()` |
| `CMSHelper` | CMS record processing | `getCMSFields()` |
| `CustomStringHelper` | String utilities | Various string manipulation methods |
| `FileViewHelper` | File display helpers | File preview/rendering |

See also: [Architecture Overview](architecture-overview.md) | [Admin Guide](admin-guide.md)
