<?php
namespace oshco\Database\Migrations;

use oshco\database\logger\SystemExceptionsTable;
use WebFiori\Database\Database;
use WebFiori\Database\Schema\AbstractMigration;

class CreateSystemExceptionsTable extends AbstractMigration {
    public function up(Database $db): void {
        $table = new SystemExceptionsTable();
        $db->addTable($table);
        $db->table('system_exceptions')->createTable()->execute();
    }

    public function down(Database $db): void {
        $table = new SystemExceptionsTable();
        $db->addTable($table);
        $db->table('system_exceptions')->drop()->execute();
    }
}
