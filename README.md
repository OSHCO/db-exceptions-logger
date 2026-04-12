# Database Exceptions Logger

A [WebFiori](https://webfiori.com) framework extension that logs exceptions to a database. Currently supports SQL Server only.

## Requirements

- PHP 8.0 or later
- [WebFiori Framework](https://github.com/WebFiori/framework) v3.0.0-RC0
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

Replace `<your-connection>` with your database connection name. The library defaults to a connection named `exceptions-logger`.

### 2. Register the error handler

In any of your application's initialization files:

```php
use oshco\handler\DatabaseErrHandler;
use oshco\database\logger\ExceptionsDB;
use WebFiori\Error\Handler;

Handler::registerHandler(new DatabaseErrHandler(new ExceptionsDB()));
```

## How It Works

When an exception occurs, `DatabaseErrHandler` captures:

- Exception code, class, and message
- File, line number, and stack trace
- Request URL and parameters
- A SHA-256 hash of the exception for deduplication

All data is stored in the `system_exceptions` table via `ExceptionsDB`.

## Classes and Interfaces

| Class / Interface | Description |
|---|---|
| [`HandlerController`](oshco/handler/HandlerController.php) | Interface for exception storage. Requires `addSystemException()`. |
| [`DatabaseErrHandler`](oshco/handler/DatabaseErrHandler.php) | Error handler that captures exception details and delegates storage to a `HandlerController`. |
| [`ExceptionsDB`](oshco/database/logger/ExceptionsDB.php) | Database layer implementing `HandlerController`. Provides CRUD operations on the `system_exceptions` table. |
| [`SystemExceptionsTable`](oshco/database/logger/SystemExceptionsTable.php) | MSSQL table schema definition for `system_exceptions`. |
| [`SystemException`](oshco/entity/logger/SystemException.php) | Entity representing a logged exception record. |

## Running Tests

Requires a running SQL Server instance:

```bash
SA_SQL_SERVER_PASSWORD='<your-password>' composer test
```

## License

MIT
