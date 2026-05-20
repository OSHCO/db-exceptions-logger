# Database Exceptions Logger

A [WebFiori](https://webfiori.com) framework extension that logs exceptions to a database with optional per-portal tracking. Currently supports SQL Server only.

## Requirements

- PHP 8.1 or later
- [WebFiori Framework](https://github.com/WebFiori/framework) v3.0.0-RC
- SQL Server with ODBC Driver 18

## Installation

```bash
composer require oshco/db-exceptions-logger
```

## Setup

### 1. Create the database table

Run the migration to create the `system_exceptions` table:

```bash
php webfiori migrations:ini --connection=<your-connection>
php webfiori migrations:run --connection=<your-connection>
```

### 2. Register the error handler

```php
use Oshco\ErrHandler\DatabaseErrHandler;
use Oshco\Infrastructure\Repository\ExceptionsRepository;
use WebFiori\Database\Database;
use WebFiori\Error\Handler;
use WebFiori\Framework\App;

$db = new Database(App::getConfig()->getDBConnection('your-connection'));
$repo = new ExceptionsRepository($db);
Handler::registerHandler(new DatabaseErrHandler($repo));
```

### 3. (Optional) Configure portal tracking

To associate exceptions with a specific portal, set a resolver callable:

```php
use Oshco\ErrHandler\DatabaseErrHandler;
use WebFiori\Framework\Session\SessionsManager;

DatabaseErrHandler::setPortalIdResolver(function () {
    $session = SessionsManager::getActiveSession();
    return $session?->get('portal-id');
});
```

The resolver is called when an exception occurs. It should return an `int` (portal ID) or `null`.

## Usage

### Querying logged exceptions

```php
use Oshco\Infrastructure\Repository\ExceptionsRepository;
use WebFiori\Database\Database;
use WebFiori\Framework\App;

$db = new Database(App::getConfig()->getDBConnection('your-connection'));
$repo = new ExceptionsRepository($db);

// Get total count
$count = $repo->count();

// Get paginated list (page 1, 10 per page)
$exceptions = $repo->getAll(1, 10);

// Get by ID
$exception = $repo->getById(1);

// Get the most recent exception
$latest = $repo->getLast();

// Check if an exception with a given hash exists
$exists = $repo->existsByHash($hashString);

// Filter by portal
$portalExceptions = $repo->getByPortal(portalId: 1, page: 1, size: 10);
$portalCount = $repo->countByPortal(portalId: 1);
```

### Entity properties

Each `SystemException` entity exposes:

| Method | Description |
|--------|-------------|
| `getId()` | Auto-increment ID |
| `getHash()` | SHA-256 hash for deduplication |
| `getDate()` | Timestamp when the exception was logged |
| `getCode()` | Exception code |
| `getClass()` | Class where the exception was thrown |
| `getExceptionClass()` | The exception's fully-qualified class name |
| `getMessage()` | Exception message |
| `getLine()` | Line number |
| `getUrl()` | Request URL |
| `getParameters()` | Request parameters |
| `getTrace()` | Stack trace |
| `getPortalId()` | Portal ID (null if not set) |

## How It Works

When an exception occurs, `DatabaseErrHandler`:

1. Captures exception details (code, class, message, file, line, stack trace)
2. Captures request context (URL, parameters)
3. Resolves the portal ID via the configured resolver (if set)
4. Computes a SHA-256 hash for deduplication
5. Stores everything in the `system_exceptions` table via `ExceptionsRepository`

## Database Schema

| Column | Type | Nullable | Description |
|--------|------|----------|-------------|
| `id` | INT (PK, identity) | No | Auto-increment ID |
| `hash` | NVARCHAR(128) | No | SHA-256 hash for deduplication |
| `date` | DATETIME2 | No | Timestamp (defaults to now) |
| `code` | INT | No | Exception code |
| `class` | VARCHAR(128) | No | Class where exception was thrown |
| `exception_class` | VARCHAR(128) | No | Exception class name |
| `message` | NVARCHAR(256) | No | Exception message |
| `line` | INT | No | Line number |
| `url` | NVARCHAR(256) | Yes | Request URL |
| `parameters` | NVARCHAR(1024) | Yes | Request parameters |
| `trace` | NVARCHAR(1024) | No | Stack trace |
| `portal_id` | INT | Yes | Portal where exception occurred |

## Classes

| Class | Description |
|-------|-------------|
| `DatabaseErrHandler` | Error handler that captures exceptions and delegates storage to the repository. Supports a configurable portal ID resolver. |
| `ExceptionsRepository` | Repository providing CRUD and filtering operations on the `system_exceptions` table. |
| `SystemExceptionsTable` | Attribute-based MSSQL table schema definition. |
| `SystemException` | Entity representing a logged exception record. |
| `CreateSystemExceptionsTable` | Migration that creates the base table. |
| `AlterSystemExceptionsAddPortalId` | Migration that adds the `portal_id` column (safe for existing tables). |

## Running Tests

Requires a running SQL Server instance:

```bash
SA_SQL_SERVER_PASSWORD='<your-password>' composer test
```

## License

MIT
