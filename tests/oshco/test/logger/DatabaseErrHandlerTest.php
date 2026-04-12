<?php
namespace oshco\test\logger;

use Exception;
use oshco\entity\logger\SystemException;
use oshco\handler\DatabaseErrHandler;
use oshco\handler\HandlerController;
use PHPUnit\Framework\TestCase;

class MockHandlerController implements HandlerController {
    public ?SystemException $lastException = null;
    public int $callCount = 0;

    public function addSystemException(SystemException $ex) {
        $this->lastException = $ex;
        $this->callCount++;
    }
}

class DatabaseErrHandlerTest extends TestCase {

    private MockHandlerController $controller;
    private DatabaseErrHandler $handler;

    protected function setUp(): void {
        $this->controller = new MockHandlerController();
        $this->handler = new DatabaseErrHandler($this->controller);
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
        $this->assertEquals(500, $this->controller->lastException->getCode());
    }

    public function testHandleSetsMessage() {
        $this->handler->setException(new Exception('Something went wrong'));
        $this->handler->handle();
        $this->assertEquals('Something went wrong', $this->controller->lastException->getMessage());
    }

    public function testHandleSetsExceptionClass() {
        $this->handler->setException(new Exception('err'));
        $this->handler->handle();
        $this->assertEquals(Exception::class, $this->controller->lastException->getExceptionClass());
    }

    public function testHandleSetsLine() {
        $this->handler->setException(new Exception('err'));
        $this->handler->handle();
        $this->assertNotNull($this->controller->lastException->getLine());
    }

    public function testHandleSetsClass() {
        $this->handler->setException(new Exception('err'));
        $this->handler->handle();
        $this->assertNotNull($this->controller->lastException->getClass());
    }

    public function testHandleSetsTrace() {
        $this->handler->setException(new Exception('err'));
        $this->handler->handle();
        $this->assertNotNull($this->controller->lastException->getTrace());
    }

    public function testHandleSetsUrl() {
        $this->handler->setException(new Exception('err'));
        $this->handler->handle();
        $this->assertNotNull($this->controller->lastException->getUrl());
    }

    public function testHandleComputesHash() {
        $this->handler->setException(new Exception('err'));
        $this->handler->handle();
        $this->assertNotEmpty($this->controller->lastException->getHash());
        $this->assertEquals(64, strlen($this->controller->lastException->getHash()));
    }

    public function testHandleCallsControllerOnce() {
        $this->handler->setException(new Exception('err'));
        $this->handler->handle();
        $this->assertEquals(1, $this->controller->callCount);
    }

    public function testHandleWithDifferentExceptionTypes() {
        $this->handler->setException(new \RuntimeException('runtime err', 99));
        $this->handler->handle();
        $this->assertEquals(\RuntimeException::class, $this->controller->lastException->getExceptionClass());
        $this->assertEquals(99, $this->controller->lastException->getCode());
        $this->assertEquals('runtime err', $this->controller->lastException->getMessage());
    }

    public function testHandleMultipleCallsOverwritesController() {
        $this->handler->setException(new Exception('first', 1));
        $this->handler->handle();
        $this->assertEquals('first', $this->controller->lastException->getMessage());

        $this->handler->setException(new Exception('second', 2));
        $this->handler->handle();
        $this->assertEquals('second', $this->controller->lastException->getMessage());
        $this->assertEquals(2, $this->controller->callCount);
    }
}
