<?php
namespace Tests\Oshco\Infrastructure;

use Oshco\Entity\SystemException;
use Oshco\Infrastructure\Repository\ExceptionsRepository;
use PHPUnit\Framework\TestCase;
use WebFiori\Error\TraceEntry;
use WebFiori\Framework\App;

class ExceptionsRepositoryTest extends TestCase {
    private static ?ExceptionsRepository $repo = null;

    private static function getRepo(): ExceptionsRepository {
        if (self::$repo === null) {
            $db = App::getDatabase('exceptions-logger');
            self::$repo = new ExceptionsRepository($db);
        }

        return self::$repo;
    }

    public function test00_initiallyEmpty() {
        $this->assertNull(self::getRepo()->getLast());
    }

    public function test01_addAndRetrieve() {
        $ex = $this->createTestException();

        self::getRepo()->add($ex);
        $date = date('Y-m-d H:i:s.0');

        $last = self::getRepo()->getLast();
        $this->assertNotNull($last);
        $this->assertTrue(self::getRepo()->existsByHash($ex->getHash()));
        $this->assertEquals(1, self::getRepo()->count());

        for ($x = 0; $x < 6; $x++) {
            self::getRepo()->add($ex);
        }
        $this->assertEquals(7, self::getRepo()->count());

        $exArr = self::getRepo()->getAll(1, 2);
        $this->assertEquals(2, count($exArr));

        $ex1 = $exArr[0];
        $this->assertEquals(SystemException::class, $ex1->getClass());
        $this->assertEquals(33, $ex1->getCode());
        $this->assertEquals(self::class, $ex1->getExceptionClass());
        $this->assertEquals($ex->getHash(), $ex1->getHash());
        $this->assertEquals(1, $ex1->getId());
        $this->assertEquals(77, $ex1->getLine());
        $this->assertEquals('This is a test', $ex1->getMessage());
        $this->assertNull($ex1->getParameters());
        $this->assertEquals("At class WebFiori\\Error\TraceEntry line 33\n"
                ."At class WebFiori\\Error\TraceEntry line 33", $ex1->getTrace());
        $this->assertEquals('https://my-api.com/do-it', $ex1->getUrl());

        $this->assertNull(self::getRepo()->getById(100));

        $ex1 = self::getRepo()->getById($ex1->getId());
        $this->assertEquals(SystemException::class, $ex1->getClass());
        $this->assertEquals(33, $ex1->getCode());
        $this->assertEquals(self::class, $ex1->getExceptionClass());
        $this->assertEquals($ex->getHash(), $ex1->getHash());
        $this->assertEquals(1, $ex1->getId());
        $this->assertEquals(77, $ex1->getLine());
        $this->assertEquals('This is a test', $ex1->getMessage());
        $this->assertNull($ex1->getParameters());
        $this->assertEquals("At class WebFiori\\Error\TraceEntry line 33\n"
                ."At class WebFiori\\Error\TraceEntry line 33", $ex1->getTrace());
        $this->assertEquals('https://my-api.com/do-it', $ex1->getUrl());

        $json = $ex1->toJSON().'';
        $this->assertStringContainsString('"class":"Oshco\\\\Entity\\\\SystemException"', $json);
        $this->assertStringContainsString('"code":33', $json);
        $this->assertStringContainsString('"exceptionClass":"Tests\\\\Oshco\\\\Infrastructure\\\\ExceptionsRepositoryTest"', $json);
        $this->assertStringContainsString('"line":77', $json);
        $this->assertStringContainsString('"message":"This is a test"', $json);
        $this->assertStringContainsString('"url":"https:\/\/my-api.com\/do-it"', $json);
    }

    public function test02_addWithParameters() {
        $ex = $this->createTestException();
        $ex->setParameters('"A" => "B"');

        self::getRepo()->add($ex);
        $added = self::getRepo()->getLast();
        $this->assertEquals('"A" => "B"', $added->getParameters());
    }

    private function createTestException(): SystemException {
        $ex = new SystemException();
        $ex->setCode(33);
        $ex->setClass(SystemException::class);
        $ex->setExceptionClass(self::class);
        $ex->setLine(77);
        $ex->setMessage('This is a test');

        $tr = new TraceEntry([
            'class' => TraceEntry::class,
            'file' => TraceEntry::class.'.php',
            'line' => 33,
        ]);
        $ex->setTrace(''.$tr."\n".$tr);
        $ex->setUrl('https://my-api.com/do-it');

        return $ex;
    }
}
