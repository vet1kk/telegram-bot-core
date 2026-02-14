<?php

declare(strict_types=1);

namespace Bot\Routing;

use Bot\Action\ActionManager;
use Bot\Command\CommandManager;
use Bot\DTO\Update\CallbackQueryUpdateDTO;
use Bot\DTO\Update\MessageUpdateDTO;
use Bot\DTO\Update\UpdateDTO;
use Bot\Event\EventManager;
use Bot\Event\Events\UnhandledEvent;
use Psr\Log\LoggerInterface;

class Router
{
    /**
     * @param \Bot\Command\CommandManager $commandManager
     * @param \Bot\Action\ActionManager $actionManager
     * @param \Bot\Event\EventManager $eventManager
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        protected CommandManager $commandManager,
        protected ActionManager $actionManager,
        protected EventManager $eventManager,
        protected LoggerInterface $logger
    ) {
    }

    /**
     * @param \Bot\DTO\Update\UpdateDTO $update
     * @return void
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    public function route(UpdateDTO $update): void
    {
        match (true) {
            $update instanceof MessageUpdateDTO => $this->commandManager->handle($update),
            $update instanceof CallbackQueryUpdateDTO => $this->actionManager->handle($update),
            default => $this->eventManager->emit(new UnhandledEvent($update)),
        };
    }
}
