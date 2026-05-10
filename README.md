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

### 1. Initialize the database table

The library uses migrations to create the `system_exceptions` table. Run:

```bash
php webfiori migrations:ini --connection=<your-connection>
php webfiori migrations:run --connection=<your-connection>
```

### 2. Register the error handler

```php
use Oshco\ErrHandler\DatabaseErrHandler;
use Oshco\Infrastructure\Repository\ExceptionsRepository;
use WebFiori\Error\Handler;
use WebFiori\Framework\App;

$db = App::getDatabase('your-connection');
$repo = new ExceptionsRepository($db);
Handler::registerHandler(new DatabaseErrHandler($repo));
```

## How It Works

When an exception occurs, `DatabaseErrHandler` captures:

- Exception code, class, and message
- File, line number, and stack trace
- Request URL and parameters
- A SHA-256 hash of the exception for deduplication

All data is stored in the `system_exceptions` table via `ExceptionsRepository`.

## Classes

| Class | Description |
|---|---|
| [`DatabaseErrHandler`](Oshco/ErrHandler/DatabaseErrHandler.php) | Error handler that captures exception details and delegates storage to `ExceptionsRepository`. |
| [`ExceptionsRepository`](Oshco/Infrastructure/Repository/ExceptionsRepository.php) | Repository providing CRUD operations on the `system_exceptions` table. |
| [`SystemExceptionsTable`](Oshco/Infrastructure/Schema/SystemExceptionsTable.php) | MSSQL table schema definition for `system_exceptions`. |
| [`SystemException`](Oshco/Entity/SystemException.php) | Entity representing a logged exception record. |

## Running Tests

Requires a running SQL Server instance:

```bash
SA_SQL_SERVER_PASSWORD='<your-password>' composer test
```

## License

MIT
