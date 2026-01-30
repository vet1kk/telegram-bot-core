<?php

declare(strict_types=1);

namespace Bot\Middleware;

use Bot\Update;

class MiddlewareManager
{
    /**
     * @var array<int, \Bot\Middleware\MiddlewareInterface>
     */
    protected array $middlewareStack = [];

    /**
     * @param \Bot\Middleware\MiddlewareInterface $middleware
     * @return static
     */
    public function register(MiddlewareInterface $middleware): static
    {
        $this->middlewareStack[] = $middleware;

        return $this;
    }

    /**
     * @param \Bot\Update $update
     * @param callable $destination
     * @return void
     */
    public function process(Update $update, callable $destination): void
    {
        $pipeline = array_reduce(
            array_reverse($this->middlewareStack),
            static function (callable $next, MiddlewareInterface $middleware) {
                return static function (Update $update) use ($next, $middleware) {
                    $middleware->process($update, $next);
                };
            },
            $destination
        );

        $pipeline($update);
    }
}
