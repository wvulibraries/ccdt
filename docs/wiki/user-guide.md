# User Guide

## Authentication & Roles

### Logging In

Navigate to `/login` and enter your credentials. The application supports:

- **Regular users** (`isAdmin = false`) — Can browse data, search records, and upload files
- **Admin users** (`isAdmin = true`) — Full access including collection/table management and imports

### Creating Test Users

```bash
docker exec -it ccdt_php php artisan db:seed --class=UsersTableSeeder
```

Default test accounts:
| Email | Password | Role |
|-------|----------|------|
| `test@test.com` | `testing` | Regular user |
| `admin@admin.com` | `testing` | Admin |

### Password Reset

Use the `/register` page to create new accounts. Admin users can manage existing accounts through the database.

---

## Managing Collections

Collections are the top-level organizational unit in CCDT. They group related tables and associated files.

### Creating a Collection (Admin Only)

1. Navigate to **Collections** (`/collection`)
2. Click **Create New Collection**
3. Fill in:
   - **Name**: 6-30 characters, alphanumeric only
   - **Type**: Flat File or CMS (Constituent Management System)
   - **CMS ID** (optional): Required for CMS-type collections to determine the correct header format
4. Click **Save**

### Viewing Collections

The collection index (`/collection`) displays:
- Collection name
- Number of associated tables
- Whether files are attached
- Enabled/disabled status

### Editing a Collection

1. Navigate to `/collection`
2. Find the collection and click **Edit**
3. Modify:
   - Collection name (must be unique)
   - Enable/disable status
4. Click **Save**

### Disabling a Collection

Disabled collections cannot be imported into but are preserved in the database. This is useful for archiving old data without deleting it.

### Deleting a Collection (Admin Only)

1. Navigate to `/collection`
2. Find the collection and click **Delete**
3. Confirm deletion

> **Warning**: Deleting a collection removes all associated tables and their records permanently.

---

## Importing Data

CCDT supports two import methods via the **Import Wizard** (admin only).

### Flat File Import

For CSV, TSV, pipe-delimited, or semicolon-delimited files.

#### Step 1: Navigate to Import Wizard

Go to `/admin/wizard/flatfile`

#### Step 2: Select Collection

Choose the target collection from the dropdown.

#### Step 3: Upload File(s)

1. Click **Choose File**
2. Select one or more delimited files
3. Click **Upload**

The system automatically detects the delimiter (`,`, `;`, `\t`, or `|`) from the first row.

#### Step 4: Configure Import

1. Preview the detected columns
2. Map source columns to table fields if needed
3. Specify the upload folder name
4. Click **Start Import**

#### Step 5: Monitor Progress

Navigate to `/admin/jobs/pending` to track import progress. Large files are processed as background jobs.

### CMS Record Type Import

For structured Constituent Management System data with predefined record types (e.g., 1A, 1B, 2A).

#### Prerequisites

- Collection must be marked as **CMS type**
- `cmsId` must be set on the collection
- CMS record type headers must exist in the `cms_records` table

#### Step 1: Navigate to Import Wizard

Go to `/admin/wizard/cms`

#### Step 2: Select Collection

Choose a CMS-type collection.

#### Step 3: Upload CMS File

1. Click **Choose File**
2. Select the CMS-formatted file
3. Click **Upload**

#### Step 4: Select Record Type

The wizard displays available CMS record types. Select the matching type (e.g., `1A`, `1B`).

#### Step 5: Configure & Import

1. Review the detected field mapping
2. Confirm the import settings
3. Click **Start Import**

---

## Managing Tables

### Viewing All Tables

Navigate to `/table` to see all tables across all collections.

### Creating a Table (Admin Only)

Tables are typically created automatically during import. Manual creation is available for custom schemas:

1. Navigate to `/table/create`
2. Select the target collection
3. Define column names and sizes:
   - **Default**: 30 characters
   - **Medium**: 150 characters
   - **Big**: 500 characters
4. Click **Create Table**

### Editing a Table

1. Navigate to `/table`
2. Find the table and click **Edit**
3. Modify:
   - Table name
   - Associated collection
4. Click **Save**

### Editing Table Schema (Admin Only)

Add, remove, or modify columns in an existing table:

1. Navigate to `/table/edit/{id}`
2. Click **Edit Schema**
3. Add new columns with type/size selection
4. Remove unwanted columns
5. Click **Save Schema**

### Restricting Table Access (Admin Only)

Tables can be restricted to limit which collections can access them:

1. Navigate to `/table`
2. Find the table and click **Restrict**
3. Configure access permissions

---

## Viewing & Searching Data

### Browsing Records

Navigate to `/data/{tableId}` to view records in a card-based layout with pagination (30 records per page).

Each record displays:
- All field values from the table schema
- Creation and update timestamps

### Viewing a Single Record

Click on any record ID to view its full details at `/data/{tableId}/{recordId}`.

### Searching Records

The search bar at the top of the data view performs full-text searches across indexed fields:

1. Enter search terms in the search box
2. Press Enter or click **Search**
3. Results are displayed with matching fields highlighted

### Search Index Management

Search indexes can be created and optimized via console commands (see [Admin Guide](admin-guide.md#console-commands)).

---

## Job Management

### Viewing Pending Jobs

Navigate to `/admin/jobs/pending` to see:
- Job ID
- Table name being imported
- Status (pending, processing, completed)
- Timestamps

### Viewing Failed Jobs

Navigate to `/admin/jobs/failed` to see:
- Job ID
- Error messages
- Retry options

### Retrying Jobs

- **Single job**: Click **Retry** next to the failed job
- **All jobs**: Click **Retry All** to retry every failed job at once

### Forgetting Jobs

Remove completed or abandoned jobs from the queue by clicking **Forget**.

---

## File Uploads

Authenticated users can upload files (attachments) to collections:

1. Navigate to `/upload/{colID}`
2. Select files to upload
3. Specify the destination folder name
4. Click **Upload**

Files are stored in `storage/app/files/{collectionName}/{folderName}/`.

---

## Navigation Quick Reference

| Page | URL | Access |
|------|-----|--------|
| Home | `/` | All users |
| Login | `/login` | All users |
| Dashboard | `/home` | Authenticated |
| Help | `/help` | All users |
| Collections | `/collection` | Admin only |
| Tables | `/table` | Admin only |
| Import Wizard (Flatfile) | `/admin/wizard/flatfile` | Admin only |
| Import Wizard (CMS) | `/admin/wizard/cms` | Admin only |
| Pending Jobs | `/admin/jobs/pending` | Admin only |
| Failed Jobs | `/admin/jobs/failed` | Admin only |
| Browse Data | `/data/{tableId}` | Authenticated |
| Upload Files | `/upload/{colID}` | Authenticated |

See also: [Getting Started](getting-started.md) | [Admin Guide](admin-guide.md) | [Architecture Overview](architecture-overview.md)
