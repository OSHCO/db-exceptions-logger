<?php
namespace oshco\entity\logger;

use webfiori\database\RecordMapper;
use webfiori\json\Json;
use webfiori\json\JsonI;

/**
 * An auto-generated entity class which maps to a record in the
 * table 'system_exceptions'
 **/
class SystemException implements JsonI {
    /**
     * A mapper which is used to map a record to an instance of the class.
     * 
     * @var RecordMapper
     **/
    private static $RecordMapper;
    /**
     * The attribute which is mapped to the column 'class'.
     * 
     * @var string
     **/
    private $class;
    /**
     * The attribute which is mapped to the column 'code'.
     * 
     * @var int
     **/
    private $code;
    /**
     * The attribute which is mapped to the column 'date'.
     * 
     * @var string
     **/
    private $date;
    /**
     * The attribute which is mapped to the column 'exception_class'.
     * 
     * @var string
     **/
    private $exceptionClass;
    /**
     * The attribute which is mapped to the column 'hash'.
     * 
     * @var string
     **/
    private $hash;
    /**
     * The attribute which is mapped to the column 'id'.
     * 
     * @var int
     **/
    private $id;
    /**
     * The attribute which is mapped to the column 'line'.
     * 
     * @var int
     **/
    private $line;
    /**
     * The attribute which is mapped to the column 'message'.
     * 
     * @var string
     **/
    private $message;
    /**
     * The attribute which is mapped to the column 'parameters'.
     * 
     * @var string
     **/
    private $parameters;
    /**
     * The attribute which is mapped to the column 'trace'.
     * 
     * @var string
     **/
    private $trace;
    /**
     * The attribute which is mapped to the column 'url'.
     * 
     * @var string
     **/
    private $url;
    /**
     * Returns the value of the attribute 'class'.
     * 
     * The value of the attribute is mapped to the column which has
     * the name 'class'.
     * 
     * @return string The value of the attribute.
     **/
    public function getClass() {
        return $this->class;
    }
    /**
     * Returns the value of the attribute 'code'.
     * 
     * The value of the attribute is mapped to the column which has
     * the name 'code'.
     * 
     * @return int The value of the attribute.
     **/
    public function getCode() {
        return $this->code;
    }
    /**
     * Returns the value of the attribute 'date'.
     * 
     * The value of the attribute is mapped to the column which has
     * the name 'date'.
     * 
     * @return string The value of the attribute.
     **/
    public function getDate() {
        return $this->date;
    }
    /**
     * Returns the value of the attribute 'exceptionClass'.
     * 
     * The value of the attribute is mapped to the column which has
     * the name 'exception_class'.
     * 
     * @return string The value of the attribute.
     **/
    public function getExceptionClass() {
        return $this->exceptionClass;
    }
    public function computeHash() {
        return hash('sha256', $this->getClass()
                .$this->getCode()
                .$this->getExceptionClass()
                .$this->getLine()
                .$this->getMessage()
                .$this->getTrace()
                .$this->getUrl());
    }
    /**
     * Returns the value of the attribute 'hash'.
     * 
     * The value of the attribute is mapped to the column which has
     * the name 'hash'.
     * 
     * @return string The value of the attribute.
     **/
    public function getHash() {
        if ($this->hash === null) {
            $this->hash = $this->computeHash();
        }
        return $this->hash;
    }
    /**
     * Returns the value of the attribute 'id'.
     * 
     * The value of the attribute is mapped to the column which has
     * the name 'id'.
     * 
     * @return int The value of the attribute.
     **/
    public function getId() {
        return $this->id;
    }
    /**
     * Returns the value of the attribute 'line'.
     * 
     * The value of the attribute is mapped to the column which has
     * the name 'line'.
     * 
     * @return int The value of the attribute.
     **/
    public function getLine() {
        return $this->line;
    }
    /**
     * Returns the value of the attribute 'message'.
     * 
     * The value of the attribute is mapped to the column which has
     * the name 'message'.
     * 
     * @return string The value of the attribute.
     **/
    public function getMessage() {
        return $this->message;
    }
    /**
     * Returns the value of the attribute 'parameters'.
     * 
     * The value of the attribute is mapped to the column which has
     * the name 'parameters'.
     * 
     * @return string The value of the attribute.
     **/
    public function getParameters() {
        return $this->parameters;
    }
    /**
     * Returns the value of the attribute 'trace'.
     * 
     * The value of the attribute is mapped to the column which has
     * the name 'trace'.
     * 
     * @return string The value of the attribute.
     **/
    public function getTrace() {
        return $this->trace;
    }
    /**
     * Returns the value of the attribute 'url'.
     * 
     * The value of the attribute is mapped to the column which has
     * the name 'url'.
     * 
     * @return string The value of the attribute.
     **/
    public function getUrl() {
        return $this->url;
    }
    /**
     * Sets the value of the attribute 'class'.
     * 
     * The value of the attribute is mapped to the column which has
     * the name 'class'.
     * 
     * @param $class string The new value of the attribute.
     **/
    public function setClass($class) {
        $this->class = $class;
    }
    /**
     * Sets the value of the attribute 'code'.
     * 
     * The value of the attribute is mapped to the column which has
     * the name 'code'.
     * 
     * @param $code int The new value of the attribute.
     **/
    public function setCode($code) {
        $this->code = $code;
    }
    /**
     * Sets the value of the attribute 'date'.
     * 
     * The value of the attribute is mapped to the column which has
     * the name 'date'.
     * 
     * @param $date string The new value of the attribute.
     **/
    public function setDate($date) {
        $this->date = $date;
    }
    /**
     * Sets the value of the attribute 'exceptionClass'.
     * 
     * The value of the attribute is mapped to the column which has
     * the name 'exception_class'.
     * 
     * @param $exceptionClass string The new value of the attribute.
     **/
    public function setExceptionClass($exceptionClass) {
        $this->exceptionClass = $exceptionClass;
    }
    /**
     * Sets the value of the attribute 'hash'.
     * 
     * The value of the attribute is mapped to the column which has
     * the name 'hash'.
     * 
     * @param $hash string The new value of the attribute.
     **/
    public function setHash($hash) {
        $this->hash = $hash;
    }
    /**
     * Sets the value of the attribute 'id'.
     * 
     * The value of the attribute is mapped to the column which has
     * the name 'id'.
     * 
     * @param $id int The new value of the attribute.
     **/
    public function setId($id) {
        $this->id = $id;
    }
    /**
     * Sets the value of the attribute 'line'.
     * 
     * The value of the attribute is mapped to the column which has
     * the name 'line'.
     * 
     * @param $line int The new value of the attribute.
     **/
    public function setLine($line) {
        $this->line = $line;
    }
    /**
     * Sets the value of the attribute 'message'.
     * 
     * The value of the attribute is mapped to the column which has
     * the name 'message'.
     * 
     * @param $message string The new value of the attribute.
     **/
    public function setMessage($message) {
        $this->message = $message;
    }
    /**
     * Sets the value of the attribute 'parameters'.
     * 
     * The value of the attribute is mapped to the column which has
     * the name 'parameters'.
     * 
     * @param $parameters string The new value of the attribute.
     **/
    public function setParameters($parameters) {
        $this->parameters = $parameters;
    }
    /**
     * Sets the value of the attribute 'trace'.
     * 
     * The value of the attribute is mapped to the column which has
     * the name 'trace'.
     * 
     * @param $trace string The new value of the attribute.
     **/
    public function setTrace($trace) {
        $this->trace = $trace;
    }
    /**
     * Sets the value of the attribute 'url'.
     * 
     * The value of the attribute is mapped to the column which has
     * the name 'url'.
     * 
     * @param $url string The new value of the attribute.
     **/
    public function setUrl($url) {
        $this->url = $url;
    }
    /**
     * Maps a record which is taken from the table system_exceptions to an instance of the class.
     * 
     * @param array $record An associative array that represents the
     * record. 
     * @return SystemException An instance of the class.
     */
    public static function map(array $record) {
        if (self::$RecordMapper === null ||  count(array_keys($record)) != self::$RecordMapper->getSettersMapCount()) {
            self::$RecordMapper = new RecordMapper(self::class, array_keys($record));
        }
        return self::$RecordMapper->map($record);
    }
    /**
     * Returns an object of type 'Json' that contains object information.
     * 
     * The returned object will have the following attributes:
     * <ul>
     * <li>class</li>
     * <li>code</li>
     * <li>date</li>
     * <li>exceptionClass</li>
     * <li>hash</li>
     * <li>id</li>
     * <li>line</li>
     * <li>message</li>
     * <li>parameters</li>
     * <li>trace</li>
     * <li>url</li>
     * </ul>
     * 
     * @return Json An object of type 'Json'.
     */
    public function toJSON() : Json {
        $json = new Json([
            'class' => $this->getClass(),
            'code' => $this->getCode(),
            'date' => $this->getDate(),
            'exceptionClass' => $this->getExceptionClass(),
            'hash' => $this->getHash(),
            'id' => $this->getId(),
            'line' => $this->getLine(),
            'message' => $this->getMessage(),
            'parameters' => $this->getParameters(),
            'trace' => $this->getTrace(),
            'url' => $this->getUrl()
        ]);
        return $json;
    }
}
