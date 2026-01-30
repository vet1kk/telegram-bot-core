<?php

declare(strict_types=1);

namespace Bot\Event;

class EventManager
{
    /**
     * @var array<string, array<int, callable>>
     */
    protected array $listeners = [];

    /**
     * @param string $event
     * @param callable $handler
     * @return static
     */
    public function on(string $event, callable $handler): static
    {
        $this->listeners[$event][] = $handler;

        return $this;
    }

    /**
     * @param \Bot\Event\EventInterface $event
     * @return void
     */
    public function emit(EventInterface $event): void
    {
        $name = $event::class;

        foreach ($this->$listeners[$name] ?? [] as $listener) {
            $listener($event);
        }
    }
}
