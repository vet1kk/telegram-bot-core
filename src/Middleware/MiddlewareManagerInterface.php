<?php

declare(strict_types=1);

namespace Bot\Middleware;

use Bot\DTO\Update\UpdateDTO;

interface MiddlewareManagerInterface
{
    /**
     * @param string $middlewareClass
     * @return self
     */
    public function register(string $middlewareClass): self;

    /**
     * @param \Bot\DTO\Update\UpdateDTO $update
     * @param callable $destination
     * @return void
     */
    public function process(UpdateDTO $update, callable $destination): void;
}
