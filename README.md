# Database Exceptions Logger
 A WebFiori framework extension which is used to log exceptions to database. The library currently supports SQL Server only.

## Configuration
* Add dependency
* Initialize database
* Set errors handler

First, include this library in your project by adding the following dependency:

```
oshco/db-exceptions-logger
```

To initialize the table which is used to store exceptions, run following command:
``` bash
php webfiori run-query --schema="oshco\database\logger\ExceptionsDB" --connection=<your-connection>
```

Replace `your-connection` with the database connection to be used by the class. Note that the class will try to use a connection with the name `exceptions-logger` if no connection provided.

To set errors handler, place following code in any of the initialization files:

``` php
\webfiori\error\Handler::registerHandler(new \oshco\handler\DatabaseErrHandler(new oshco\database\logger\ExceptionsDB()));
```
