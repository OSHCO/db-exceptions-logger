<?php

namespace Oshco\Infrastructure\Repository;

use Oshco\Entity\SystemException;
use Oshco\Infrastructure\Schema\SystemExceptionsTable;
use WebFiori\Database\Attributes\AttributeTableBuilder;
use WebFiori\Database\Database;

/**
 * Repository for CRUD operations on the 'system_exceptions' table.
 */
class ExceptionsRepository {
    const TABLE = 'system_exceptions';
    private Database $db;

    public function __construct(Database $db) {
        $this->db = $db;
        $dbType = $db->getConnectionInfo()->getDatabaseType();
        $table = AttributeTableBuilder::build(SystemExceptionsTable::class, $dbType);
        $this->db->addTable($table);
    }

    public function getDatabase(): Database {
        return $this->db;
    }

    public function add(SystemException $entity): void {
        $this->db->table(self::TABLE)->insert([
            'hash' => $entity->getHash(),
            'code' => $entity->getCode(),
            'class' => $entity->getClass(),
            'exception-class' => $entity->getExceptionClass(),
            'message' => substr($entity->getMessage() ?? '', 0, 256),
            'line' => $entity->getLine(),
            'url' => $entity->getUrl(),
            'parameters' => $entity->getParameters(),
            'trace' => substr($entity->getTrace() ?? '', 0, 1024),
            'portal-id' => $entity->getPortalId(),
        ])->execute();
    }

    public function existsByHash(string $hash): bool {
        return $this->db->table(self::TABLE)
                ->select()
                ->where('hash', $hash)
                ->execute()
                ->getRowsCount() != 0;
    }

    public function getLast(): ?SystemException {
        $id = $this->db->table(self::TABLE)->selectMax('id')->execute()->getRows()[0]['max'];

        return $id === null ? null : $this->getById($id);
    }

    public function getById(int $id): ?SystemException {
        $mappedRecords = $this->db->table(self::TABLE)
                ->select()
                ->where('id', $id)
                ->execute()
                ->map(function (array $record) {
                    return SystemException::map($record);
                });

        if ($mappedRecords->getRowsCount() == 1) {
            return $mappedRecords->getRows()[0];
        }

        return null;
    }

    public function getAll(int $page = 0, int $size = 10): array {
        return $this->db->table(self::TABLE)
                ->select()
                ->page($page, $size)
                ->orderBy(['id'])
                ->execute()
                ->map(function (array $record) {
                    return SystemException::map($record);
                })->toArray();
    }

    public function count(): int {
        return $this->db->table(self::TABLE)
                ->selectCount()
                ->execute()
                ->getRows()[0]['count'];
    }

    public function getByPortal(int $portalId, int $page = 1, int $size = 10): array {
        return $this->db->table(self::TABLE)
                ->select()
                ->where('portal-id', $portalId)
                ->page($page, $size)
                ->orderBy(['id'])
                ->execute()
                ->map(function (array $record) {
                    return SystemException::map($record);
                })->toArray();
    }

    public function countByPortal(int $portalId): int {
        return $this->db->table(self::TABLE)
                ->selectCount()
                ->where('portal-id', $portalId)
                ->execute()
                ->getRows()[0]['count'];
    }
}
