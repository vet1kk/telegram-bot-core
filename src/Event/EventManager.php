<?php

declare(strict_types=1);

namespace Bot\Event;

use Psr\Container\ContainerInterface;

class EventManager
{
    /**
     * @var array<string, array<int, array{string, string}>>
     */
    protected array $listeners = [];

    /**
     * @param \Psr\Container\ContainerInterface $container
     */
    public function __construct(protected ContainerInterface $container)
    {
    }

    /**
     * @param string $eventClass
     * @param string $listener
     * @param string $method
     * @return void
     */
    public function listen(string $eventClass, string $listener, string $method): void
    {
        $this->listeners[$eventClass][] = [$listener, $method];
    }

    /**
     * @param \Bot\Event\EventInterface $event
     * @return void
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    public function emit(EventInterface $event): void
    {
        $name = $event::class;

        foreach ($this->listeners[$name] ?? [] as [$listener, $method]) {
            $this->container->get($listener)?->$method($event);
        }
    }
}
