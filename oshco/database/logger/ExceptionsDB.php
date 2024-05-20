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
        $this->table('system_exceptions')->insert([
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
     * Deletes a record from the table 'system_exceptions'.
     *
     * @param SystemException $entity An object that holds record information.
     */
    public function deleteSystemException(SystemException $entity) {
        $this->table('system_exceptions')
                ->delete()
                ->where('id', $entity->getId())
                ->execute();
    }
    /**
     * Returns the information of a record from the table 'system_exceptions'.
     *
     * @return SystemException|null If a record with given information exist,
     * The method will return an object which holds all record information.
     * Other than that, null is returned.
     */
    public function getSystemException(int $id) {
        $mappedRecords = $this->table('system_exceptions')
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
        return $this->table('system_exceptions')
                ->select()
                ->page($pageNum, $pageSize)
                ->orderBy(["id"])
                ->execute()
                ->map(function (array $record) {
                    return C::map($record);
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
        return $this->table('system_exceptions')
                ->selectCount()
                ->execute()
                ->getRows()[0]['count'];
    }
    /**
     * Updates a record on the table 'system_exceptions'.
     *
     * @param SystemException $entity An object that holds updated record information.
     */
    public function updateSystemException(SystemException $entity) {
        $this->table('system_exceptions')
            ->update([
                'hash' => $entity->getHash(),
                'date' => $entity->getDate(),
                'code' => $entity->getCode(),
                'class' => $entity->getClass(),
                'exception-class' => $entity->getExceptionClass(),
                'message' => $entity->getMessage(),
                'line' => $entity->getLine(),
                'url' => $entity->getUrl(),
                'parameters' => $entity->getParameters(),
                'trace' => $entity->getTrace(),
            ])
            ->where('id', $entity->getId())
            ->execute();
    }
}
