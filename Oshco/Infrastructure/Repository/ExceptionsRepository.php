<?php

namespace Oshco\Infrastructure\Repository;

use Oshco\Entity\SystemException;
use Oshco\Infrastructure\Schema\SystemExceptionsTable;
use WebFiori\Database\Attributes\AttributeTableBuilder;
use WebFiori\Database\Database;

/**
 * Repository for CRUD operations on the `system_exceptions` table.
 *
 * Provides methods to add, retrieve, count, and filter logged exceptions.
 */
class ExceptionsRepository {
    const TABLE = 'system_exceptions';
    private Database $db;

    /**
     * Creates a new repository instance.
     *
     * Builds and registers the table schema on the given database connection.
     *
     * @param Database $db The database connection to use.
     */
    public function __construct(Database $db) {
        $this->db = $db;
        $dbType = $db->getConnectionInfo()->getDatabaseType();
        $table = AttributeTableBuilder::build(SystemExceptionsTable::class, $dbType);
        $this->db->addTable($table);
    }

    /**
     * Returns the underlying database instance.
     *
     * @return Database
     */
    public function getDatabase(): Database {
        return $this->db;
    }

    /**
     * Inserts a new exception record into the database.
     *
     * @param SystemException $entity The exception to store.
     */
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

    /**
     * Checks if an exception with the given hash already exists.
     *
     * @param string $hash The SHA-256 hash to check.
     *
     * @return bool True if a record with this hash exists.
     */
    public function existsByHash(string $hash): bool {
        return $this->db->table(self::TABLE)
                ->select()
                ->where('hash', $hash)
                ->execute()
                ->getRowsCount() != 0;
    }

    /**
     * Returns the most recently logged exception.
     *
     * @return SystemException|null Null if no exceptions have been logged.
     */
    public function getLast(): ?SystemException {
        $id = $this->db->table(self::TABLE)->selectMax('id')->execute()->getRows()[0]['max'];

        return $id === null ? null : $this->getById($id);
    }

    /**
     * Retrieves an exception by its ID.
     *
     * @param int $id The record ID.
     *
     * @return SystemException|null Null if not found.
     */
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

    /**
     * Retrieves a paginated list of all exceptions.
     *
     * @param int $page Page number (1-based).
     * @param int $size Number of records per page.
     *
     * @return array Array of SystemException objects.
     */
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

    /**
     * Returns the total number of logged exceptions.
     *
     * @return int
     */
    public function count(): int {
        return $this->db->table(self::TABLE)
                ->selectCount()
                ->execute()
                ->getRows()[0]['count'];
    }

    /**
     * Retrieves exceptions filtered by portal ID.
     *
     * @param int $portalId The portal ID to filter by.
     * @param int $page Page number (1-based).
     * @param int $size Number of records per page.
     *
     * @return array Array of SystemException objects.
     */
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

    /**
     * Returns the count of exceptions for a specific portal.
     *
     * @param int $portalId The portal ID to count for.
     *
     * @return int
     */
    public function countByPortal(int $portalId): int {
        return $this->db->table(self::TABLE)
                ->selectCount()
                ->where('portal-id', $portalId)
                ->execute()
                ->getRows()[0]['count'];
    }
}
