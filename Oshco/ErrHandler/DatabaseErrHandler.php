<?php

namespace Oshco\ErrHandler;

use Oshco\Entity\SystemException;
use Oshco\Infrastructure\Repository\ExceptionsRepository;
use Override;
use WebFiori\Error\AbstractHandler;
use WebFiori\Framework\App;

/**
 * Error handler that captures exception details and stores them in the database.
 *
 * Captures: exception code, class, message, line, stack trace, request URL,
 * request parameters, and optionally the portal ID where the error occurred.
 *
 * Portal ID is resolved via a configurable callable set with `setPortalIdResolver()`.
 */
class DatabaseErrHandler extends AbstractHandler {
    private ExceptionsRepository $repo;

    /** @var callable|null */
    private static $portalIdResolver = null;

    /**
     * Creates a new instance.
     *
     * @param ExceptionsRepository $repo The repository used to persist exceptions.
     */
    public function __construct(ExceptionsRepository $repo) {
        parent::__construct();
        $this->repo = $repo;
    }

    /**
     * Sets a callable that resolves the current portal ID.
     *
     * The callable should return an int (portal ID) or null if no portal context
     * is available. This is called during exception handling to associate the
     * exception with a specific portal.
     *
     * Example:
     * ```php
     * DatabaseErrHandler::setPortalIdResolver(function () {
     *     $session = SessionsManager::getActiveSession();
     *     return $session?->get('portal-id');
     * });
     * ```
     *
     * @param callable|null $resolver A callable returning int|null, or null to disable.
     */
    public static function setPortalIdResolver(?callable $resolver): void {
        self::$portalIdResolver = $resolver;
    }

    /**
     * Handles the exception by logging it to the database.
     *
     * Captures all relevant exception data, resolves the portal ID (if a resolver
     * is configured), and stores the record via the repository.
     */
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

    /**
     * Returns whether this handler is active.
     *
     * @return bool Always true.
     */
    #[Override]
    public function isActive(): bool {
        return true;
    }

    /**
     * Returns whether this handler should also handle shutdown errors.
     *
     * @return bool Always true.
     */
    #[Override]
    public function isShutdownHandler(): bool {
        return true;
    }
}
