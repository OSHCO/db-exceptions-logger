<?php
namespace Tests\Oshco\ErrHandler;

use Exception;
use Oshco\Entity\SystemException;
use Oshco\ErrHandler\DatabaseErrHandler;
use Oshco\Infrastructure\Repository\ExceptionsRepository;
use PHPUnit\Framework\TestCase;
use WebFiori\Database\Database;

class MockExceptionsRepository extends ExceptionsRepository {
    public ?SystemException $lastException = null;
    public int $callCount = 0;

    public function __construct() {
        // Skip parent constructor — no real DB needed for unit tests
    }

    public function add(SystemException $entity): void {
        $this->lastException = $entity;
        $this->callCount++;
    }
}

class DatabaseErrHandlerTest extends TestCase {
    private MockExceptionsRepository $repo;
    private DatabaseErrHandler $handler;

    protected function setUp(): void {
        $this->repo = new MockExceptionsRepository();
        $this->handler = new DatabaseErrHandler($this->repo);
    }

    public function testIsActive() {
        $this->assertTrue($this->handler->isActive());
    }

    public function testIsShutdownHandler() {
        $this->assertTrue($this->handler->isShutdownHandler());
    }

    public function testHandleSetsCodeFromException() {
        $this->handler->setException(new Exception('err', 500));
        $this->handler->handle();
        $this->assertEquals(500, $this->repo->lastException->getCode());
    }

    public function testHandleSetsMessage() {
        $this->handler->setException(new Exception('Something went wrong'));
        $this->handler->handle();
        $this->assertEquals('Something went wrong', $this->repo->lastException->getMessage());
    }

    public function testHandleSetsExceptionClass() {
        $this->handler->setException(new Exception('err'));
        $this->handler->handle();
        $this->assertEquals(Exception::class, $this->repo->lastException->getExceptionClass());
    }

    public function testHandleSetsLine() {
        $this->handler->setException(new Exception('err'));
        $this->handler->handle();
        $this->assertNotNull($this->repo->lastException->getLine());
    }

    public function testHandleSetsClass() {
        $this->handler->setException(new Exception('err'));
        $this->handler->handle();
        $this->assertNotNull($this->repo->lastException->getClass());
    }

    public function testHandleSetsTrace() {
        $this->handler->setException(new Exception('err'));
        $this->handler->handle();
        $this->assertNotNull($this->repo->lastException->getTrace());
    }

    public function testHandleSetsUrl() {
        $this->handler->setException(new Exception('err'));
        $this->handler->handle();
        $this->assertNotNull($this->repo->lastException->getUrl());
    }

    public function testHandleComputesHash() {
        $this->handler->setException(new Exception('err'));
        $this->handler->handle();
        $this->assertNotEmpty($this->repo->lastException->getHash());
        $this->assertEquals(64, strlen($this->repo->lastException->getHash()));
    }

    public function testHandleCallsRepoOnce() {
        $this->handler->setException(new Exception('err'));
        $this->handler->handle();
        $this->assertEquals(1, $this->repo->callCount);
    }

    public function testHandleWithDifferentExceptionTypes() {
        $this->handler->setException(new \RuntimeException('runtime err', 99));
        $this->handler->handle();
        $this->assertEquals(\RuntimeException::class, $this->repo->lastException->getExceptionClass());
        $this->assertEquals(99, $this->repo->lastException->getCode());
        $this->assertEquals('runtime err', $this->repo->lastException->getMessage());
    }

    public function testHandleMultipleCalls() {
        $this->handler->setException(new Exception('first', 1));
        $this->handler->handle();
        $this->assertEquals('first', $this->repo->lastException->getMessage());

        $this->handler->setException(new Exception('second', 2));
        $this->handler->handle();
        $this->assertEquals('second', $this->repo->lastException->getMessage());
        $this->assertEquals(2, $this->repo->callCount);
    }
}
