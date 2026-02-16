<?php

declare(strict_types=1);

namespace Bot\Event;

interface EventManagerInterface
{
    /**
     * @param \Bot\Event\EventInterface $event
     * @return void
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    public function emit(EventInterface $event): void;

    /**
     * @param class-string<\Bot\Listener\ListenerInterface> $listenerClass
     */
    public function registerListener(string $listenerClass): self;
}
