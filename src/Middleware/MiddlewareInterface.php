<?php

namespace Bot\Middleware;

use Bot\DTO\Update\UpdateDTO;

interface MiddlewareInterface
{
    /**
     * @param \Bot\DTO\Update\UpdateDTO $update
     * @param callable $next
     * @return void
     */
    public function process(UpdateDTO $update, callable $next): void;
}
