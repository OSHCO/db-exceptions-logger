<?php
namespace oshco\test\logger;

use oshco\database\logger\ExceptionsDB;
use oshco\entity\logger\SystemException;
use PHPUnit\Framework\TestCase;
use webfiori\error\TraceEntry;
use webfiori\http\Request;
/**
 * Description of ExceptionDBTest
 *
 * @author i.binalshikh
 */
class ExceptionDBTest extends TestCase {
    /**
     * @test
     */
    public function test00() {
        $ex = new SystemException();
        $ex->setCode(33);
        $ex->setClass(SystemException::class);
        $ex->setExceptionClass(get_class($this));
        $ex->setLine(77);
        $ex->setMessage('This is a test');
        $tr = new TraceEntry([
            'class' => TraceEntry::class,
            'file' => TraceEntry::class.'.php',
            'line' => 33,
            
        ]);
        $trace = ''.$tr."\n".$tr;
        $ex->setTrace($trace);
        $ex->setUrl(Request::getRequestedURI());
       
        ExceptionsDB::get()->addSystemException($ex);
        $this->assertTrue(ExceptionsDB::get()->hasExceptionWithHash($ex->getHash()));
    }
 }
