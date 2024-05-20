<?php
namespace oshco\database\logger;

use webfiori\database\mssql\MSSQLTable;
use webfiori\database\ColOption;
use webfiori\database\DataType;
/**
 * A class which represents the database table 'system_exceptions'.
 * The table which is associated with this class will have the following columns:
 * <ul>
 * <li><b>id</b>: Name in database: 'id'. Data type: 'int'.</li>
 * <li><b>hash</b>: Name in database: 'hash'. Data type: 'nvarchar'.</li>
 * <li><b>date</b>: Name in database: 'date'. Data type: 'datetime2'.</li>
 * <li><b>code</b>: Name in database: 'code'. Data type: 'int'.</li>
 * <li><b>class</b>: Name in database: 'class'. Data type: 'varchar'.</li>
 * <li><b>exception-class</b>: Name in database: 'exception_class'. Data type: 'varchar'.</li>
 * <li><b>message</b>: Name in database: 'message'. Data type: 'nvarchar'.</li>
 * <li><b>line</b>: Name in database: 'line'. Data type: 'int'.</li>
 * <li><b>trace</b>: Name in database: 'trace'. Data type: 'nvarchar'.</li>
 * </ul>
 */
class SystemExceptionsTable extends MSSQLTable {
    /**
     * Creates new instance of the class.
     */
    public function __construct() {
        parent::__construct('system_exceptions');
        $this->setComment('This table is used to log system exceptions.');
        $this->addColumns([
            'id' => [
                ColOption::TYPE => DataType::INT,
                ColOption::IDENTITY => true,
                ColOption::PRIMARY => true,
                ColOption::COMMENT => 'The unique ID of the exception.',
            ],
            'hash' => [
                ColOption::TYPE => DataType::NVARCHAR,
                ColOption::SIZE => '128',
                ColOption::COMMENT => 'The unique hash of the exception.',
            ],
            'date' => [
                ColOption::TYPE => DataType::DATETIME2,
                ColOption::DEFAULT => 'now',
                ColOption::COMMENT => 'The date and time at which the exception thrown.',
            ],
            'code' => [
                ColOption::TYPE => DataType::INT,
                ColOption::COMMENT => 'Exception code.',
            ],
            'class' => [
                ColOption::TYPE => DataType::VARCHAR,
                ColOption::SIZE => '128',
                ColOption::COMMENT => 'The class at which the exception was thrown at.',
            ],
            'exception-class' => [
                ColOption::TYPE => DataType::VARCHAR,
                ColOption::SIZE => '128',
                ColOption::COMMENT => 'The class of the exception.',
            ],
            'message' => [
                ColOption::TYPE => DataType::NVARCHAR,
                ColOption::SIZE => '256',
                ColOption::COMMENT => 'Exception message.',
            ],
            'line' => [
                ColOption::TYPE => DataType::INT,
                ColOption::COMMENT => 'Line number at which the exception was thrown at.',
            ],
            'trace' => [
                ColOption::TYPE => DataType::NVARCHAR,
                ColOption::SIZE => '1024',
                ColOption::COMMENT => 'Stack trace of the exception.',
            ],
        ]);
    }
}
