<?php

declare(strict_types=1);

namespace Bot\Event;

class EventManager
{
    /**
     * @var array<string, array<int, callable>>
     */
    protected static array $listeners = [];

    /**
     * @return static
     */
    public static function create(): static
    {
        return new static();
    }

    /**
     * @param string $event
     * @param callable $handler
     * @return static
     */
    public function on(string $event, callable $handler): static
    {
        static::$listeners[$event][] = $handler;

        return $this;
    }

    /**
     * @param \Bot\Event\EventInterface $event
     * @return void
     */
    public static function emit(EventInterface $event): void
    {
        $name = $event::class;

        foreach (static::$listeners[$name] ?? [] as $listener) {
            $listener($event);
        }
    }
}
