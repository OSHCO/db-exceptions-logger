<?php
namespace Oshco\Entity;

use WebFiori\Database\Entity\RecordMapper;
use WebFiori\Json\Json;
use WebFiori\Json\JsonI;

/**
 * An entity class which maps to a record in the table 'system_exceptions'.
 */
class SystemException implements JsonI {
    private static $RecordMapper;
    private $class;
    private $code;
    private $date;
    private $exceptionClass;
    private $hash;
    private $id;
    private $line;
    private $message;
    private $parameters;
    private $trace;
    private $url;
    private $portalId;

    public function getClass() {
        return $this->class;
    }

    public function getCode() {
        return $this->code;
    }

    public function getDate() {
        return $this->date;
    }

    public function getExceptionClass() {
        return $this->exceptionClass;
    }

    public function computeHash(): string {
        return hash('sha256', $this->getClass()
                .$this->getCode()
                .$this->getExceptionClass()
                .$this->getLine()
                .$this->getMessage()
                .$this->getTrace()
                .$this->getUrl());
    }

    public function getHash() {
        if ($this->hash === null) {
            $this->hash = $this->computeHash();
        }

        return $this->hash;
    }

    public function getId() {
        return $this->id;
    }

    public function getLine() {
        return $this->line;
    }

    public function getMessage() {
        return $this->message;
    }

    public function getParameters() {
        return $this->parameters;
    }

    public function getTrace() {
        return $this->trace;
    }

    public function getUrl() {
        return $this->url;
    }

    public function getPortalId() {
        return $this->portalId;
    }

    public function setClass($class) {
        $this->class = $class;
    }

    public function setCode($code) {
        $this->code = $code;
    }

    public function setDate($date) {
        $this->date = $date;
    }

    public function setExceptionClass($exceptionClass) {
        $this->exceptionClass = $exceptionClass;
    }

    public function setHash($hash) {
        $this->hash = $hash;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setLine($line) {
        $this->line = $line;
    }

    public function setMessage($message) {
        $this->message = $message;
    }

    public function setParameters($parameters) {
        $this->parameters = $parameters;
    }

    public function setTrace($trace) {
        $this->trace = $trace;
    }

    public function setUrl($url) {
        $this->url = $url;
    }

    public function setPortalId($portalId) {
        $this->portalId = $portalId;
    }

    public static function map(array $record) {
        if (self::$RecordMapper === null || count(array_keys($record)) != self::$RecordMapper->getSettersMapCount()) {
            self::$RecordMapper = new RecordMapper(self::class, array_keys($record));
        }

        return self::$RecordMapper->map($record);
    }

    public function toJSON(): Json {
        return new Json([
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
            'url' => $this->getUrl(),
            'portalId' => $this->getPortalId(),
        ]);
    }
}
