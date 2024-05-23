<?php
namespace oshco\database\logger;

use oshco\entity\logger\SystemException;
use webfiori\framework\DB;
/**
 * A class which is used to perform operations on the table 'system_exceptions'
 */
class ExceptionsDB extends DB {
    private static $instance;
    /**
     * The name of the table which is used to hold the exceptions.
     */
    const TABLE = 'system_exceptions';
    /**
     * Returns an instance of the class.
     * 
     * Calling this method multiple times will return same instance.
     * 
     * @return ExceptionsDB An instance of the class.
     */
    public static function get() : ExceptionsDB {

        if (self::$instance === null) {
            self::$instance = new ExceptionsDB();
        }

        return self::$instance;
    }
    /**
     * Creates new instance of the class.
     */
    public function __construct(string $conn = 'exceptions-logger') {
        //TODO: Specify the name of database connection to use in performing operations.
        parent::__construct($conn);
        $this->addTable(new SystemExceptionsTable());
    }
    /**
     * Adds new record to the table 'system_exceptions'.
     *
     * @param SystemException $entity An object that holds record information.
     */
    public function addSystemException(SystemException $entity) {
        $this->table(self::TABLE)->insert([
            'hash' => $entity->getHash(),
            'code' => $entity->getCode(),
            'class' => $entity->getClass(),
            'exception-class' => $entity->getExceptionClass(),
            'message' => $entity->getMessage(),
            'line' => $entity->getLine(),
            'url' => $entity->getUrl(),
            'parameters' => $entity->getParameters(),
            'trace' => $entity->getTrace(),
        ])->execute();
    }
    /**
     * Checks if there was an exception which was logged with same hash.
     * 
     * @param string $hash
     * 
     * @return bool If there is a single one with such hash, true is returned.
     * False otherwise.
     */
    public function hasExceptionWithHash(string $hash) : bool {
        return $this->table(self::TABLE)
                ->select()
                ->where('hash', $hash)
                ->execute()
                ->getRowsCount() != 0;
    }
    /**
     * Returns the last added exception information as object.
     * 
     * @return SystemException|null If the database is empty, null is returned.
     * Other than that, an object of type 'SystemException' is returned.
     */
    public function getLastAddedSystemException() {
        $id = $this->table(self::TABLE)->selectMax('id')->execute()->getRows()[0]['max'];
        
        return $id === null ? null : $this->getSystemException($id);
    }
    /**
     * Returns the information of a record from the table 'system_exceptions'.
     *
     * @return SystemException|null If a record with given information exist,
     * The method will return an object which holds all record information.
     * Other than that, null is returned.
     */
    public function getSystemException(int $id) {
        $mappedRecords = $this->table(self::TABLE)
                ->select()
                ->where('id', $id)
                ->execute()
                ->map(function (array $record) {
                    return SystemException::map($record);
                });
        if ($mappedRecords->getRowsCount() == 1) {
            return $mappedRecords->getRows()[0];
        }
    }
    /**
     * Returns all the records from the table 'system_exceptions'.
     *
     * @param int $pageNum The number of page to fetch. Default is 0.
     *
     * @param int $pageSize Number of records per page. Default is 10.
     *
     * @return array An array that holds all table records as objects
     */
    public function getSystemExceptions(int $pageNum = 0, int $pageSize = 10) : array {
        return $this->table(self::TABLE)
                ->select()
                ->page($pageNum, $pageSize)
                ->orderBy(["id"])
                ->execute()
                ->map(function (array $record) {
                    return SystemException::map($record);
                })->toArray();
    }
    /**
     * Returns number of records on the table 'system_exceptions'.
     *
     * The main use of this method is to compute number of pages.
     *
     * @return int Number of records on the table 'system_exceptions'.
     */
    public function getSystemExceptionsCount() : int {
        return $this->table(self::TABLE)
                ->selectCount()
                ->execute()
                ->getRows()[0]['count'];
    }
}
