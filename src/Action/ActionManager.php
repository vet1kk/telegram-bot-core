<?php

declare(strict_types=1);

namespace Bot\Action;

use Bot\Attribute\Action as ActionAttr;
use Bot\DTO\Update\CallbackQueryUpdateDTO;
use Bot\Event\EventManagerInterface;
use Bot\Event\Events\ActionHandledEvent;
use Bot\Event\Events\UnhandledEvent;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use ReflectionClass;

class ActionManager implements ActionManagerInterface
{
    /**
     * @var array<string, class-string<\Bot\Action\ActionInterface>>
     */
    protected array $actions = [];

    /**
     * @param \Psr\Container\ContainerInterface $container
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Bot\Event\EventManagerInterface $eventManager
     */
    public function __construct(
        protected ContainerInterface $container,
        protected LoggerInterface $logger,
        protected EventManagerInterface $eventManager
    ) {
    }

    /**
     * @param class-string<\Bot\Action\ActionInterface> $actionClass
     * @return $this
     * @throws \ReflectionException
     */
    public function register(string $actionClass): self
    {
        $reflection = new ReflectionClass($actionClass);
        $attributes = $reflection->getAttributes(ActionAttr::class);

        if (empty($attributes)) {
            throw new \InvalidArgumentException("Class $actionClass is missing the Action attribute.");
        }

        if (count($attributes) > 1) {
            throw new \LogicException("Class $actionClass has multiple Action attributes. Only one is allowed.");
        }

        $attrInstance = current($attributes)->newInstance();
        $this->actions[$attrInstance->name] = $actionClass;

        return $this;
    }

    /**
     * @param \Bot\DTO\Update\CallbackQueryUpdateDTO $update
     * @return ?\Bot\Action\ActionInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    public function resolve(CallbackQueryUpdateDTO $update): ?ActionInterface
    {
        $actionName = $update->callback_query?->data;

        if (empty($actionName)) {
            return null;
        }
        $className = $this->actions[$actionName] ?? null;

        if (empty($className)) {
            return null;
        }

        return $this->container->get($className);
    }

    /**
     * @param \Bot\DTO\Update\CallbackQueryUpdateDTO $update
     * @return void
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    public function handle(CallbackQueryUpdateDTO $update): void
    {
        $action = $this->resolve($update);
        if ($action) {
            $this->logger->info('Executing action: ' . $action::class, [
                'update' => $update->jsonSerialize(),
            ]);

            $action->handle($update);

            $this->eventManager->emit(new ActionHandledEvent($action, $update));

            return;
        }

        $this->eventManager->emit(new UnhandledEvent($update));
    }
}
