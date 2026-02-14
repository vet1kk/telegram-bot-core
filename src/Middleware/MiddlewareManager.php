<?php

declare(strict_types=1);

namespace Bot\Middleware;

use Bot\DTO\Update\UpdateDTO;
use Psr\Container\ContainerInterface;

class MiddlewareManager
{
    /**
     * @var array<class-string<MiddlewareInterface>> $middlewareStack
     */
    protected array $middlewareStack = [];

    /**
     * @param \Psr\Container\ContainerInterface $container
     */
    public function __construct(protected ContainerInterface $container)
    {
    }

    /**
     * @param class-string<MiddlewareInterface> $middlewareClass
     */
    public function register(string $middlewareClass): void
    {
        $this->middlewareStack[] = $middlewareClass;
    }

    /**
     * @param \Bot\DTO\Update\UpdateDTO $update
     * @param callable $destination
     * @return void
     */
    public function process(UpdateDTO $update, callable $destination): void
    {
        $container = $this->container;
        $pipeline = array_reduce(
            array_reverse($this->middlewareStack),
            static function (callable $next, string $middleware) use ($container) {
                return static function (UpdateDTO $update) use ($container, $next, $middleware) {
                    /** @var \Bot\Middleware\MiddlewareInterface $instance */
                    $instance = $container->get($middleware);
                    $instance->process($update, $next);
                };
            },
            $destination
        );

        $pipeline($update);
    }
}
