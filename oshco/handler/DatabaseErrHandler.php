<?php

use oshco\database\logger\ExceptionsDB;
use oshco\entity\logger\SystemException;
use webfiori\error\AbstractHandler;

/**
 * Errors handler which is used to log errors to a database.
 *
 * @author i.binalshikh
 */
class DatabaseErrHandler extends AbstractHandler {

    #[Override]
    public function handle() {
        $ex = new SystemException();
        $ex->setCode($this->getCode());
        $ex->setClass($this->getClass());
        $ex->setExceptionClass(get_class($this->getException()));
        $ex->setLine($this->getLine());
        $ex->setMessage($this->getMessage());
        $trace = '';
        foreach ($this->getTrace() as $entry) {
            $trace .= $entry . "\n";
        }
        $ex->setTrace($trace);
        ExceptionsDB::get()->addSystemException($ex);
    }

    #[Override]
    public function isActive(): bool {
        return true;
    }

    #[Override]
    public function isShutdownHandler(): bool {
        return true;
    }
}
