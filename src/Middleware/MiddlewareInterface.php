<?php

namespace Bot\Middleware;

use Bot\Update;

interface MiddlewareInterface
{
    public function process(Update $update, callable $next): void;
}
