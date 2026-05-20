# Database Exceptions Logger

A [WebFiori](https://webfiori.com) framework extension that logs exceptions to a database. Currently supports SQL Server only.

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

// Get paginated list (page 0, 10 per page)
$exceptions = $repo->getAll(0, 10);

// Get by ID
$exception = $repo->getById(1);

// Get the most recent exception
$latest = $repo->getLast();

// Check if an exception with a given hash exists
$exists = $repo->existsByHash($hashString);
```

### Entity properties

Each `SystemException` entity exposes:

- `getId()` — auto-increment ID
- `getHash()` — SHA-256 hash for deduplication
- `getDate()` — timestamp when the exception was logged
- `getCode()` — exception code
- `getClass()` — class where the exception was thrown
- `getExceptionClass()` — the exception's class name
- `getMessage()` — exception message
- `getLine()` — line number
- `getUrl()` — request URL
- `getParameters()` — request parameters
- `getTrace()` — stack trace

## How It Works

When an exception occurs, `DatabaseErrHandler` captures the exception details (code, class, message, file, line, stack trace, request URL, and parameters), computes a SHA-256 hash for deduplication, and stores everything in the `system_exceptions` table via `ExceptionsRepository`.

## Classes

| Class | Description |
|---|---|
| [`DatabaseErrHandler`](Oshco/ErrHandler/DatabaseErrHandler.php) | Error handler that captures exception details and delegates storage to `ExceptionsRepository`. |
| [`ExceptionsRepository`](Oshco/Infrastructure/Repository/ExceptionsRepository.php) | Repository providing CRUD operations on the `system_exceptions` table. |
| [`SystemExceptionsTable`](Oshco/Infrastructure/Schema/SystemExceptionsTable.php) | Attribute-based MSSQL table schema definition for `system_exceptions`. |
| [`SystemException`](Oshco/Entity/SystemException.php) | Entity representing a logged exception record. |

## Running Tests

Requires a running SQL Server instance:

```bash
SA_SQL_SERVER_PASSWORD='<your-password>' composer test
```

## License

MIT
