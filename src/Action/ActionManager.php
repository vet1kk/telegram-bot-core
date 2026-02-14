<?php

declare(strict_types=1);

namespace Bot\Action;

use Bot\Attribute\Action as ActionAttr;
use Bot\DTO\Update\CallbackQueryUpdateDTO;
use Bot\Event\EventManager;
use Bot\Event\Events\ActionHandledEvent;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use ReflectionClass;

class ActionManager
{
    /**
     * @var array<string, class-string<\Bot\Action\ActionInterface>>
     */
    protected array $actions = [];

    /**
     * @param \Psr\Container\ContainerInterface $container
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Bot\Event\EventManager $eventManager
     */
    public function __construct(
        protected ContainerInterface $container,
        protected LoggerInterface $logger,
        protected EventManager $eventManager
    ) {
    }

    /**
     * @param class-string<\Bot\Action\ActionInterface> $actionClass
     * @return void
     * @throws \ReflectionException
     */
    public function register(string $actionClass): void
    {
        $reflection = new ReflectionClass($actionClass);
        $attributes = $reflection->getAttributes(ActionAttr::class);

        if (empty($attributes)) {
            throw new \InvalidArgumentException("Class $actionClass is missing the Action attribute.");
        }

        $attrInstance = current($attributes)->newInstance();
        $this->actions[$attrInstance->name] = $actionClass;
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
            $this->logger->warning('Received callback query with empty data.');

            return null;
        }
        $className = $this->actions[$actionName] ?? null;

        if (empty($className)) {
            $this->logger->info("No action registered for name: $actionName");

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
        }
    }
}
