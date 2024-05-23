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
        $this->assertNull(ExceptionsDB::get()->getLastAddedSystemException());
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
        $ex->setUrl('https://my-api.com/do-it');
        
        ExceptionsDB::get()->addSystemException($ex);
        $date = date('Y-m-d H:i:s.0');
        $this->assertNotNull(ExceptionsDB::get()->getLastAddedSystemException());
        $this->assertTrue(ExceptionsDB::get()->hasExceptionWithHash($ex->getHash()));
        $this->assertEquals(1, ExceptionsDB::get()->getSystemExceptionsCount());
        
        for ($x = 0 ; $x < 6 ; $x++) {
            ExceptionsDB::get()->addSystemException($ex);
        }
        $this->assertEquals(7, ExceptionsDB::get()->getSystemExceptionsCount());
        $exArr = ExceptionsDB::get()->getSystemExceptions(1, 2);
        $this->assertEquals(2, count($exArr));
        
        $ex1 = $exArr[0];
        $ex1 instanceof SystemException;
        $this->assertEquals(SystemException::class, $ex1->getClass());
        $this->assertEquals(33, $ex1->getCode());
        $this->assertEquals(ExceptionDBTest::class, $ex1->getExceptionClass());
        $this->assertEquals($ex->getHash(), $ex1->getHash());
        $this->assertEquals(1, $ex1->getId());
        $this->assertEquals(77, $ex1->getLine());
        $this->assertEquals('This is a test', $ex1->getMessage());
        $this->assertEquals(null, $ex1->getParameters());
        $this->assertEquals("At class webfiori\\error\TraceEntry line 33\n"
                . "At class webfiori\\error\TraceEntry line 33", $ex1->getTrace());
        $this->assertEquals('https://my-api.com/do-it', $ex1->getUrl());
        $this->assertNull(ExceptionsDB::get()->getSystemException(100));
        $ex1 = ExceptionsDB::get()->getSystemException($ex1->getId());
        
        $this->assertEquals(SystemException::class, $ex1->getClass());
        $this->assertEquals(33, $ex1->getCode());
        $this->assertEquals(ExceptionDBTest::class, $ex1->getExceptionClass());
        $this->assertEquals($ex->getHash(), $ex1->getHash());
        $this->assertEquals(1, $ex1->getId());
        $this->assertEquals(77, $ex1->getLine());
        $this->assertEquals('This is a test', $ex1->getMessage());
        $this->assertEquals(null, $ex1->getParameters());
        $this->assertEquals("At class webfiori\\error\TraceEntry line 33\n"
                . "At class webfiori\\error\TraceEntry line 33", $ex1->getTrace());
        $this->assertEquals('https://my-api.com/do-it', $ex1->getUrl());
        
        $this->assertEquals('{"class":"oshco\\\\entity\\\\logger\\\\SystemException",'
                . '"code":33,'
                . '"date":"'.$date.'",'
                . '"exceptionClass":"oshco\\\\test\\\\logger\\\\ExceptionDBTest",'
                . '"hash":"10fab27b0e869d5356d791f99e01b89e1950ffb6ced9fdd9b409bf879a86a790",'
                . '"id":1,'
                . '"line":77,'
                . '"message":"This is a test",'
                . '"parameters":null,'
                . '"trace":"At class webfiori\\\\error\\\\TraceEntry line 33\nAt class webfiori\\\\error\\\\TraceEntry line 33",'
                . '"url":"https:\/\/my-api.com\/do-it"}', $ex1->toJSON().'');
    }
    /**
     * @test
     */
    public function test01() {
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
        $ex->setUrl('https://my-api.com/do-it');
        $ex->setParameters('"A" => "B"');
        
        ExceptionsDB::get()->addSystemException($ex);
        $added = ExceptionsDB::get()->getLastAddedSystemException();
        $this->assertEquals('"A" => "B"', $added->getParameters());
    }
 }
