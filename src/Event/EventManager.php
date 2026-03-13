<?php

declare(strict_types=1);

namespace Bot\Event;

use Bot\Attribute\Listener;
use Bot\Listener\ListenerInterface;
use Bot\Trait\ServiceGetterTrait;
use Psr\Container\ContainerInterface;

class EventManager implements EventManagerInterface
{
    use ServiceGetterTrait;

    /**
     * @var array<string, array<int, array{class-string<ListenerInterface>, string}>>
     */
    protected array $listeners = [];

    /**
     * @param \Psr\Container\ContainerInterface $container
     */
    public function __construct(protected ContainerInterface $container)
    {
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
            $listenerInstance = $this->getService($listener);

            if (!method_exists($listenerInstance, $method)) {
                throw new \LogicException(sprintf('Listener method `%s::%s` is not callable', $listener, $method));
            }

            $listenerInstance->$method($event);
        }
    }

    /**
     * @param class-string<\Bot\Listener\ListenerInterface> $listenerClass
     * @return $this
     * @throws \ReflectionException
     */
    public function registerListener(string $listenerClass): self
    {
        $reflection = new \ReflectionClass($listenerClass);

        foreach ($reflection->getMethods() as $method) {
            $attributes = $method->getAttributes(Listener::class);

            foreach ($attributes as $attribute) {
                /** @var \Bot\Attribute\Listener $instance */
                $instance = $attribute->newInstance();

                $this->listen($instance->eventClass, $listenerClass, $method->getName());
            }
        }

        return $this;
    }

    /**
     * @param string $eventClass
     * @param class-string<ListenerInterface> $listener
     * @param string $method
     * @return void
     */
    protected function listen(string $eventClass, string $listener, string $method): void
    {
        foreach ($this->listeners[$eventClass] ?? [] as [$l, $m]) {
            if ($l === $listener && $m === $method) {
                return;
            }
        }
        $this->listeners[$eventClass][] = [$listener, $method];
    }

    /**
     * @return \Psr\Container\ContainerInterface
     */
    protected function getContainer(): ContainerInterface
    {
        return $this->container;
    }
}
