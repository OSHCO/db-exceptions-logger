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
    private static $portalIdResolver = null;

    public function __construct(ExceptionsRepository $repo) {
        parent::__construct();
        $this->repo = $repo;
    }

    /**
     * Sets a callable that returns the current portal ID.
     *
     * The callable should return an int or null.
     * Example: DatabaseErrHandler::setPortalIdResolver(fn() => $_SESSION['portal-id'] ?? null);
     */
    public static function setPortalIdResolver(?callable $resolver): void {
        self::$portalIdResolver = $resolver;
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

        if (self::$portalIdResolver !== null) {
            $portalId = call_user_func(self::$portalIdResolver);
            $ex->setPortalId($portalId);
        }

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
