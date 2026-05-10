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
    '--host' => 'env:TEST_DB_HOST',
    '--port' => '1433',
    '--user' => 'env:TEST_DB_USER',
    '--password' => 'env:TEST_DB_USER_PASS',
    '--database' => 'env:TEST_DB_NAME',
    '--name' => 'exceptions-logger',
    '--extras' => '{"TrustServerCertificate":true,"Encrypt":false}',
    '--no-check'
])->start();

if ($exitCode != 0) {
    fwrite(STDOUT, "Error During Initialization. Tests Will not Execute\n");
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
    fwrite(STDOUT, "Error During Initialization. Tests Will not Execute\n");
    fwrite(STDOUT, "----------------------------------------------\n");
    exit($exitCode);
}

register_shutdown_function(function () {
});
fwrite(STDOUT, "----------------------------------------------\n");
fwrite(STDOUT, "Bootstrapping Done\n");
fwrite(STDOUT, "----------------------------------------------\n");
