<?php

namespace Oshco\Database\Migrations;

use Oshco\Infrastructure\Schema\SystemExceptionsTable;
use WebFiori\Database\Attributes\AttributeTableBuilder;
use WebFiori\Database\Database;
use WebFiori\Database\Schema\AbstractMigration;

class CreateSystemExceptionsTable extends AbstractMigration {

    public function getDependencies(): array {
        return [];
    }

    public function up(Database $db): void {
        $dbType = $db->getConnectionInfo()->getDatabaseType();
        $table = AttributeTableBuilder::build(SystemExceptionsTable::class, $dbType);
        $db->addTable($table);
        $db->table($table->getNormalName())->createTable()->execute();
    }

    public function down(Database $db): void {
        $dbType = $db->getConnectionInfo()->getDatabaseType();
        $table = AttributeTableBuilder::build(SystemExceptionsTable::class, $dbType);
        $db->table($table->getNormalName())->drop()->execute();
    }
}
