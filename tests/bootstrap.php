<?php

$root = dirname(__DIR__);
fwrite(STDOUT, "Root: ".$root."\n");
require $root.'/vendor/autoload.php';

use WebFiori\Framework\App;

fwrite(STDOUT, "Initializing App...\n");
try {
    App::initiate('Oshco', 'public', $root.'/public');
    App::start();
} catch (Throwable $e) {
    fwrite(STDOUT, "Error During Initialization: ".$e->getMessage()."\n");
    exit(1);
}
fwrite(STDOUT, "Done\n");
fwrite(STDOUT, "----------------------------------------------\n");

fwrite(STDOUT, "Adding Database Connection...\n");
$exitCode = App::getRunner()->setArgsVector([
    'webfiori',
    'add:db-connection',
    '--db-type' => 'mssql',
    '--host' => getenv('DB_HOST') ?: 'localhost',
    '--port' => getenv('DB_PORT') ?: '1433',
    '--user' => getenv('DB_USER') ?: 'sa',
    '--password' => getenv('SA_SQL_SERVER_PASSWORD') ?: 'StrongPass@2024',
    '--database' => getenv('DB_NAME') ?: 'testing',
    '--name' => 'exceptions-logger',
    '--extras' => '{"TrustServerCertificate":true,"Encrypt":false}',
    '--no-check'
])->start();

if ($exitCode != 0) {
    fwrite(STDOUT, "Error adding database connection. Tests will not execute.\n");
    fwrite(STDOUT, "----------------------------------------------\n");
    exit($exitCode);
}
fwrite(STDOUT, "Done\n");
fwrite(STDOUT, "----------------------------------------------\n");

fwrite(STDOUT, "Initializing Migrations Table...\n");
$exitCode = App::getRunner()->setArgsVector([
    'webfiori',
    'migrations:ini',
    '--connection' => 'exceptions-logger',
])->start();

if ($exitCode != 0) {
    fwrite(STDOUT, "Error initializing migrations table.\n");
    fwrite(STDOUT, "----------------------------------------------\n");
    exit($exitCode);
}
fwrite(STDOUT, "Done\n");
fwrite(STDOUT, "----------------------------------------------\n");

fwrite(STDOUT, "Applying Migrations...\n");
$exitCode = App::getRunner()->setArgsVector([
    'webfiori',
    'migrations:run',
    '--connection' => 'exceptions-logger',
    '--env' => 'dev'
])->start();

if ($exitCode != 0) {
    fwrite(STDOUT, "Error applying migrations. Tests will not execute.\n");
    fwrite(STDOUT, "----------------------------------------------\n");
    exit($exitCode);
}

register_shutdown_function(function () {
    fwrite(STDOUT, "Testing Finished.\n");
});
fwrite(STDOUT, "----------------------------------------------\n");
fwrite(STDOUT, "Bootstrapping Done\n");
fwrite(STDOUT, "----------------------------------------------\n");
