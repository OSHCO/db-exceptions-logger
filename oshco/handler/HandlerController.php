<?php

namespace oshco\handler;

/**
 * An interface that holds the methods that must be implemented by business logic
 * in order to register it as controller.
 *
 */
interface HandlerController {
    public function addSystemException(\oshco\entity\logger\SystemException $ex);
}
