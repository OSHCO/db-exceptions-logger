<?php

namespace Oshco\Database\Migrations;

use WebFiori\Database\Database;
use WebFiori\Database\Schema\AbstractMigration;

class AlterSystemExceptionsAddPortalId extends AbstractMigration {

    public function getTargetConnections(): array {
        return ['exceptions-logger'];
    }

    public function getDependencies(): array {
        return [CreateSystemExceptionsTable::class];
    }

    public function up(Database $db): void {
        $db->raw("IF COL_LENGTH('system_exceptions', 'portal_id') IS NULL ALTER TABLE [system_exceptions] ADD [portal_id] INT NULL")->execute();
    }

    public function down(Database $db): void {
        $db->raw("IF COL_LENGTH('system_exceptions', 'portal_id') IS NOT NULL ALTER TABLE [system_exceptions] DROP COLUMN [portal_id]")->execute();
    }
}
