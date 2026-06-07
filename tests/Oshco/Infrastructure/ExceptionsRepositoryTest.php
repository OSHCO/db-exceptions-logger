<?php
namespace Tests\Oshco\Infrastructure;

use Oshco\Entity\SystemException;
use Oshco\Infrastructure\Repository\ExceptionsRepository;
use PHPUnit\Framework\TestCase;
use WebFiori\Database\Database;
use WebFiori\Error\TraceEntry;
use WebFiori\Framework\App;

class ExceptionsRepositoryTest extends TestCase {
    private static ?ExceptionsRepository $repo = null;

    private static function getRepo(): ExceptionsRepository {
        if (self::$repo === null) {
            $db = new Database(App::getConfig()->getDBConnection('exceptions-logger'));
            self::$repo = new ExceptionsRepository($db);
        }

        return self::$repo;
    }

    public function test00_initiallyEmpty() {
        self::getRepo()->getDatabase()->table(ExceptionsRepository::TABLE)->delete()->execute();
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
        $this->assertGreaterThan(0, $ex1->getId());
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
        $this->assertGreaterThan(0, $ex1->getId());
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

    public function test03_addWithPortalId() {
        $ex = $this->createTestException();
        $ex->setPortalId(5);

        self::getRepo()->add($ex);
        $added = self::getRepo()->getLast();
        $this->assertEquals(5, $added->getPortalId());
    }

    public function test04_getByPortal() {
        $ex = $this->createTestException();
        $ex->setPortalId(99);
        self::getRepo()->add($ex);
        self::getRepo()->add($ex);

        $results = self::getRepo()->getByPortal(99, 1, 10);
        $this->assertCount(2, $results);
        $this->assertEquals(99, $results[0]->getPortalId());

        $count = self::getRepo()->countByPortal(99);
        $this->assertEquals(2, $count);

        $empty = self::getRepo()->getByPortal(999, 1, 10);
        $this->assertCount(0, $empty);
    }

    public function test05_getAllReturnsNewestFirst() {
        $all = self::getRepo()->getAll(1, 5);
        $this->assertNotEmpty($all);
        $this->assertGreaterThanOrEqual($all[1]->getId(), $all[0]->getId());
    }

    public function test06_getByPortalReturnsNewestFirst() {
        $results = self::getRepo()->getByPortal(99, 1, 5);
        $this->assertNotEmpty($results);
        $this->assertGreaterThanOrEqual($results[1]->getId(), $results[0]->getId());
    }

    public function test07_statsByExceptionClass() {
        $stats = self::getRepo()->getStatsByExceptionClass();
        $this->assertNotEmpty($stats);
        $this->assertArrayHasKey('exception_class', $stats[0]);
        $this->assertArrayHasKey('count', $stats[0]);
        $this->assertGreaterThanOrEqual($stats[1]['count'] ?? 0, $stats[0]['count']);
    }

    public function test08_statsByClassAndLine() {
        $stats = self::getRepo()->getStatsByClassAndLine();
        $this->assertNotEmpty($stats);
        $this->assertArrayHasKey('class', $stats[0]);
        $this->assertArrayHasKey('line', $stats[0]);
        $this->assertArrayHasKey('count', $stats[0]);
    }

    public function test09_statsByPortal() {
        $stats = self::getRepo()->getStatsByPortal();
        $this->assertNotEmpty($stats);
        $this->assertArrayHasKey('portal_id', $stats[0]);
        $this->assertArrayHasKey('count', $stats[0]);
    }

    public function test10_statsByUrl() {
        $stats = self::getRepo()->getStatsByUrl();
        $this->assertNotEmpty($stats);
        $this->assertArrayHasKey('url', $stats[0]);
        $this->assertArrayHasKey('count', $stats[0]);
    }

    public function test11_statsByDay() {
        $stats = self::getRepo()->getStatsByDay();
        $this->assertNotEmpty($stats);
        $this->assertArrayHasKey('date', $stats[0]);
        $this->assertArrayHasKey('count', $stats[0]);
    }

    public function test12_topRecurring() {
        $stats = self::getRepo()->getTopRecurring();
        $this->assertNotEmpty($stats);
        $this->assertArrayHasKey('hash', $stats[0]);
        $this->assertArrayHasKey('message', $stats[0]);
        $this->assertArrayHasKey('count', $stats[0]);
        $this->assertGreaterThan(1, $stats[0]['count']);
    }

    public function test13_countInRange() {
        $total = self::getRepo()->count();
        $rangeCount = self::getRepo()->countInRange(date('Y-m-d').' 00:00:00', date('Y-m-d').' 23:59:59');
        $this->assertGreaterThan(0, $rangeCount);
        $this->assertLessThanOrEqual($total, $rangeCount);
    }

    public function test14_statsWithDateRange() {
        $today = date('Y-m-d');
        $stats = self::getRepo()->getStatsByExceptionClass($today.' 00:00:00', $today.' 23:59:59');
        $this->assertNotEmpty($stats);

        // Future date should return empty
        $futureStats = self::getRepo()->getStatsByExceptionClass('2099-01-01', '2099-12-31');
        $this->assertEmpty($futureStats);
    }

    public function test15_countInRangeNoResults() {
        $count = self::getRepo()->countInRange('2099-01-01', '2099-12-31');
        $this->assertEquals(0, $count);
    }

    public function test16_hashExcludesUrlAndTrace() {
        $ex1 = $this->createTestException();
        $ex1->setUrl('https://url-one.com');
        $ex1->setTrace('trace one');

        $ex2 = $this->createTestException();
        $ex2->setUrl('https://url-two.com');
        $ex2->setTrace('trace two');

        $this->assertEquals($ex1->getHash(), $ex2->getHash());
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
