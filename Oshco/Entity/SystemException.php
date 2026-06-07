<?php

namespace Oshco\Entity;

use WebFiori\Database\Entity\RecordMapper;
use WebFiori\Json\Json;
use WebFiori\Json\JsonI;

/**
 * Entity representing a logged system exception record.
 *
 * Maps to a row in the `system_exceptions` table. Includes a SHA-256 hash
 * computed from the exception's key properties for deduplication purposes.
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

    /**
     * Returns the class where the exception was thrown.
     *
     * @return string|null
     */
    public function getClass() {
        return $this->class;
    }

    /**
     * Returns the exception code.
     *
     * @return int|null
     */
    public function getCode() {
        return $this->code;
    }

    /**
     * Returns the date/time when the exception was logged.
     *
     * @return string|null
     */
    public function getDate() {
        return $this->date;
    }

    /**
     * Returns the fully-qualified class name of the exception.
     *
     * @return string|null
     */
    public function getExceptionClass() {
        return $this->exceptionClass;
    }

    /**
     * Computes a SHA-256 hash from the exception's key properties.
     *
     * Used for deduplication — identical exceptions produce the same hash.
     *
     * @return string A 64-character hex string.
     */
    public function computeHash(): string {
        return hash('sha256', $this->getClass()
                .$this->getCode()
                .$this->getExceptionClass()
                .$this->getLine()
                .$this->getMessage());
    }

    /**
     * Returns the hash of the exception. Computes it if not already set.
     *
     * @return string
     */
    public function getHash() {
        if ($this->hash === null) {
            $this->hash = $this->computeHash();
        }

        return $this->hash;
    }

    /**
     * Returns the auto-increment ID of the record.
     *
     * @return int|null
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Returns the line number where the exception was thrown.
     *
     * @return int|null
     */
    public function getLine() {
        return $this->line;
    }

    /**
     * Returns the exception message.
     *
     * @return string|null
     */
    public function getMessage() {
        return $this->message;
    }

    /**
     * Returns the request parameters at the time of the exception.
     *
     * @return string|null
     */
    public function getParameters() {
        return $this->parameters;
    }

    /**
     * Returns the stack trace.
     *
     * @return string|null
     */
    public function getTrace() {
        return $this->trace;
    }

    /**
     * Returns the request URL at the time of the exception.
     *
     * @return string|null
     */
    public function getUrl() {
        return $this->url;
    }

    /**
     * Returns the portal ID where the exception occurred.
     *
     * @return int|null Null if no portal context was available.
     */
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

    /**
     * Sets the portal ID where the exception occurred.
     *
     * @param int|null $portalId
     */
    public function setPortalId($portalId) {
        $this->portalId = $portalId;
    }

    /**
     * Maps a database record array to a SystemException instance.
     *
     * @param array $record Associative array from the database.
     *
     * @return SystemException
     */
    public static function map(array $record) {
        if (self::$RecordMapper === null || count(array_keys($record)) != self::$RecordMapper->getSettersMapCount()) {
            self::$RecordMapper = new RecordMapper(self::class, array_keys($record));
        }

        return self::$RecordMapper->map($record);
    }

    /**
     * Returns a JSON representation of the exception.
     *
     * @return Json
     */
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
