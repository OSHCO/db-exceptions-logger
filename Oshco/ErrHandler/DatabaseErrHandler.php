<?php
namespace Oshco\ErrHandler;

use Oshco\Entity\SystemException;
use Oshco\Infrastructure\Repository\ExceptionsRepository;
use Override;
use WebFiori\Error\AbstractHandler;
use WebFiori\Framework\App;

/**
 * Error handler that captures exception details and stores them via ExceptionsRepository.
 */
class DatabaseErrHandler extends AbstractHandler {
    private ExceptionsRepository $repo;

    public function __construct(ExceptionsRepository $repo) {
        parent::__construct();
        $this->repo = $repo;
    }

    #[Override]
    public function handle(): void {
        $ex = new SystemException();
        $ex->setCode($this->getCode());
        $ex->setClass($this->getClass());
        $ex->setExceptionClass(get_class($this->getException()));
        $ex->setLine($this->getLine());
        $ex->setMessage($this->getMessage());

        $trace = '';

        foreach ($this->getTrace() as $entry) {
            $trace .= $entry."\r\n";
        }
        $ex->setTrace($trace);

        $params = '';

        foreach (App::getRequest()->getParams() as $key => $val) {
            $params .= $key.' => "'.$val."\"\r\n";
        }

        if (strlen($params) != 0) {
            $ex->setParameters($params);
        }
        $ex->setUrl(App::getRequest()->getRequestedURI());
        $this->repo->add($ex);
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
